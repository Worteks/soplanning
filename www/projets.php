<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

$_SESSION['lastURL'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

if(!$user->checkDroit('projects_manage_all') && !$user->checkDroit('projects_manage_own')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: ../index.php');
	exit;
}

// PARAMÈTRES
$dateDebut = new DateTime();

if (isset($_REQUEST['nb_mois']) && is_numeric($_REQUEST['nb_mois']) && round($_REQUEST['nb_mois']) > 0) {
	$nbMois = $_REQUEST['nb_mois'];
	$_SESSION['projets_nb_mois'] = $_REQUEST['nb_mois'];
} elseif (isset($_SESSION['projets_nb_mois'])) {
	$nbMois = $_SESSION['projets_nb_mois'];
} else {
	$nbMois = 2;
	$_SESSION['projets_nb_mois'] = $nbMois;
}

// French date forcing
// Conversion des dates en mode mobile au format french
if (isset($_REQUEST['date_debut_affiche_projet']) && $_SESSION['isMobileOrTablet']) 
{
	$_REQUEST['date_debut_affiche_projet']=forceUserDateFormat($_REQUEST['date_debut_affiche_projet']);
}
if (isset($_REQUEST['date_debut_affiche_projet'])) {
	$dateDebut = initDateTime($_REQUEST['date_debut_affiche_projet']);
	$_SESSION['date_debut_affiche_projet'] = $_REQUEST['date_debut_affiche_projet'];
} else {
	$_SESSION['date_debut_affiche_projet'] = $dateDebut->format(CONFIG_DATE_LONG);
}
if(!$dateDebut ) {
	echo "Erreur de date";
	exit;
	header('Location: projets.php');
}
if (isset($_REQUEST['statut']) && is_array($_REQUEST['statut'])) {
	$listeStatuts = $_REQUEST['statut'];
} elseif (isset($_SESSION['statut_projet']) && is_array($_SESSION['statut_projet'])) {
	$listeStatuts = $_SESSION['statut_projet'];
} else {
	$listeStatuts = $_SESSION['status_projets_par_defaut'];
}
$_SESSION['statut_projet'] = $listeStatuts;
setcookie('statut_projet', json_encode($listeStatuts), time()+60*60*24*500, '/');

if (isset($_REQUEST['filtrageProjet'])) {
	$filtrageProjet = $_REQUEST['filtrageProjet'];
} elseif (isset($_SESSION['filtrageProjet'])) {
	$filtrageProjet = $_SESSION['filtrageProjet'];
} else {
	$filtrageProjet = 'tous';
}
$_SESSION['filtrageProjet'] = $filtrageProjet;

if (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('projet_id', 'nom_createur', 'nom', 'charge', 'livraison'))) {
	$order = $_REQUEST['order'];
} elseif (isset($_SESSION['projet_order'])) {
	$order = $_SESSION['projet_order'];
} else {
	$order = 'nom';
}

if (isset($_GET['by']) && in_array($_GET['by'], array('asc','desc'))) {
	$by = $_REQUEST['by'];
} elseif (isset($_SESSION['projet_by'])) {
	$by = $_SESSION['projet_by'];
} else {
	$by = 'asc';
}

// FIN PARAMÈTRES

$dateFin = clone $dateDebut;
$dateFin->modify('+' . $nbMois . ' months');
$dateFin->modify('-1 days');
$smarty->assign('dateDebut', $dateDebut->format(CONFIG_DATE_LONG));
$smarty->assign('dateFin', $dateFin->format(CONFIG_DATE_LONG));
$smarty->assign('nbMois', $nbMois);
$smarty->assign('listeStatuts', $listeStatuts);
$smarty->assign('filtrageProjet', $filtrageProjet);
$smarty->assign('order', $order);
$smarty->assign('by', $by);

$projets = new GCollection('Projet');

if(isset($_REQUEST['desactiverfiltreGroupe'])) {
	$filtreGroupeProjet = array();
	$_SESSION['projets_filtreGroupeProjet'] = $filtreGroupeProjet;
}

if (isset($_REQUEST['filtreGroupeProjet'])) {
	$filtreGroupeProjet = array();
	if(isset($_REQUEST['gp'])) {
		$filtreGroupeProjet = $_REQUEST['gp'];
	}
	if(isset($_REQUEST['gp0'])) {
		$filtreGroupeProjet[] = 'gp0';
	}
	$_SESSION['projets_filtreGroupeProjet'] = $filtreGroupeProjet;
} elseif (isset($_SESSION['projets_filtreGroupeProjet'])) {
	$filtreGroupeProjet = $_SESSION['projets_filtreGroupeProjet'];
} else {
	$filtreGroupeProjet = array();
}

if(isset($_REQUEST['rechercheProjet'])){
	if($_REQUEST['rechercheProjet'] != ''){
		$search = $_REQUEST['rechercheProjet'];
		$_SESSION['projets_search'] = $search;
	} else{
		unset($_SESSION['projets_search']);
		$search = '';
	}
} elseif (isset($_SESSION['projets_search'])) {
	$search = $_SESSION['projets_search'];
} else {
	$search = '';
}

if($search != ''){
	$searchParts = explode( ' ', $search );

	$isLike = array('0');

	foreach($searchParts as $word){
		$isLike[] = 'planning_projet.nom LIKE '.val2sql('%' . $word . '%');
		$isLike[] = 'planning_projet.iteration LIKE '.val2sql('%' . $word . '%');
		$isLike[] = 'planning_projet.projet_id LIKE '.val2sql('%' . $word . '%');
		$isLike[] = 'planning_groupe.nom LIKE '.val2sql('%' . $word . '%');
	}

	$isLike = implode(" OR ", $isLike);
	$sql = "SELECT planning_projet.*, planning_groupe.nom AS nom_groupe, planning_user.nom AS nom_createur, ps.nom as statut_nom, ps.pourcentage as statut_pourcentage, ps.couleur as statut_couleur, COUNT(pp.periode_id) AS totalPeriodes
			FROM planning_projet
			INNER JOIN planning_status ps ON ps.status_id = planning_projet.statut
			LEFT JOIN planning_periode pp ON planning_projet.projet_id = pp.projet_id
			LEFT JOIN planning_groupe ON planning_groupe.groupe_id = planning_projet.groupe_id
			LEFT JOIN planning_user ON planning_user.user_id = planning_projet.createur_id
			WHERE (" . $isLike . ") 
			AND planning_projet.statut in ('" . implode("','", $listeStatuts) . "')";
	
	if(!empty($filtreGroupeProjet)) {
	$sql .= "		AND (planning_projet.groupe_id IN ('" . implode("','", $filtreGroupeProjet) . "')";
	if(in_array('gp0', $filtreGroupeProjet)) {
		$sql .= '	OR planning_projet.groupe_id IS NULL ';
	}
	$sql .= ' ) ';
	}			
			
	$sql .= ' GROUP BY planning_projet.projet_id ';
	$sql .= "ORDER BY nom_groupe ASC," . $order . ' ' . $by;
	$smarty->assign('rechercheProjet', $search);
}  else {
	// recuperation des projets couvrant la période
	$sql = "SELECT distinct planning_projet.*, planning_groupe.nom AS nom_groupe, planning_user.nom AS nom_createur, ps.nom as statut_nom, ps.pourcentage as statut_pourcentage, ps.couleur as statut_couleur, COUNT(pp.periode_id) AS totalPeriodes
			FROM planning_projet
			INNER JOIN planning_status ps ON ps.status_id = planning_projet.statut 
			LEFT JOIN planning_groupe ON planning_groupe.groupe_id = planning_projet.groupe_id
			LEFT JOIN planning_periode pp ON planning_projet.projet_id = pp.projet_id
			LEFT JOIN planning_user ON planning_user.user_id = planning_projet.createur_id ";
	if($filtrageProjet != 'tous') {
		$sql .= "INNER JOIN planning_periode ON planning_periode.projet_id = planning_projet.projet_id AND ((planning_periode.date_debut <= '" . $dateDebut->format('Y-m-d') . "' AND planning_periode.date_fin >= '" . $dateDebut->format('Y-m-d') . "') OR (planning_periode.date_debut <= '" . $dateFin->format('Y-m-d') . "' AND planning_periode.date_debut >= '" . $dateDebut->format('Y-m-d') . "')) ";
	}
	$sql .= " WHERE planning_projet.statut in ('" . implode("','", $listeStatuts) . "')";
	
	if(!empty($filtreGroupeProjet)) {
	$sql .= "		AND (planning_projet.groupe_id IN ('" . implode("','", $filtreGroupeProjet) . "')";
	if(in_array('gp0', $filtreGroupeProjet)) {
		$sql .= '	OR planning_projet.groupe_id IS NULL ';
	}
	$sql .= ' )';
	}	
	$sql .= ' GROUP BY planning_projet.projet_id ';
	$sql .=" ORDER BY nom_groupe ASC," . $order . ' ' . $by;
	$smarty->assign('rechercheProjet', '');
 }

$projets->db_loadSQL($sql);

// liste des status
$status = new GCollection('Status');
$status->db_load(array('affichage', 'IN', array('p','tp')), array('priorite' => 'ASC'));
$smarty->assign('listeStatus', $status->getSmartyData());

$groupeProjets = new GCollection('Groupe');
$groupeProjets->db_load(array(), array('nom' => 'ASC'));
$smarty->assign('filtreGroupeProjet', $filtreGroupeProjet);
$smarty->assign('groupeProjets', $groupeProjets->getSmartyData());
$smarty->assign('projets', $projets->getSmartyData());
$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));
$smarty->display('www_projets.tpl');
?>