<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

if(!$user->checkDroit('stats_users')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: index.php');
	exit;
}

/*
PARAMETRES :
- date début graphe
- date fin graphe
- liste de projets
- liste de users
- echelle basse ordonnées (vide par défaut), nombre d'heures
- echelle haut ordonnées (vide par défaut), nombre d'heures
- echelle abscisse : jours, semaines, mois
- width et height du graphe

*/

// PARAMÈTRES
$dateDebut = new DateTime();
$pattern = '/^([1-9]|0[1-9]|1[0-9]|2[0-9]|3[01])\/([1-9]|0[1-9]|1[012])\/(19[0-9][0-9]|20[0-9][0-9])$/';

if(isset($_POST['date_debut'])) {
	$_SESSION['stats_users'] = $_POST;
	if(isset($_POST['users'])) {
		if(strlen(trim($_POST['users'])) > 0) {
			$_SESSION['stats_users']['users'] = explode(',', trim($_POST['users']));
		} else {
			$_SESSION['stats_users']['users'] = array();
		}
	}

	if(isset($_POST['projets'])) {
		if(strlen(trim($_POST['projets'])) > 0) {
			$_SESSION['stats_users']['projets'] = explode(',', trim($_POST['projets']));
		} else {
			$_SESSION['stats_users']['projets'] = array();
		}
	}

} elseif(isset($_SESSION['stats_users'])) {

} else {
	$_SESSION['stats_users'] = array();
}


if (!isset($_SESSION['stats_users']['abscisse_echelle']) || !in_array($_SESSION['stats_users']['abscisse_echelle'], array('jour','semaine','mois'))) {
	$_SESSION['stats_users']['abscisse_echelle'] = "semaine";
	$_SESSION['stats_users']['abscisse_echelle_valeur'] = "heures";
	$_SESSION['stats_users']['grille'] = "grille_h";
}
if (!isset($_SESSION['stats_users']['date_debut']) || preg_match($pattern, $_SESSION['stats_users']['date_debut']) != 1) {
	$_SESSION['stats_users']['date_debut'] = date('d/m/Y');
}
if (!isset($_SESSION['stats_users']['date_fin']) || preg_match($pattern, $_SESSION['stats_users']['date_fin']) != 1) {
	$dateTmp = new DateTime();
	$dateTmp->modify('+1 month');
	$_SESSION['stats_users']['date_fin'] = $dateTmp->format('d/m/Y');
}

if (!isset($_SESSION['stats_users']['projets'])) {
	$_SESSION['stats_users']['projets'] = array();
}
if (!isset($_SESSION['stats_users']['users'])) {
	$_SESSION['stats_users']['users'] = array();
}
if (!isset($_SESSION['stats_users']['ordonnee_max']) || $_SESSION['stats_users']['ordonnee_max'] <= 0) {
	$_SESSION['stats_users']['ordonnee_max'] = "";
}
if (!isset($_SESSION['stats_users']['ordonnee_min']) || $_SESSION['stats_users']['ordonnee_min'] < 0) {
	$_SESSION['stats_users']['ordonnee_min'] = "";
}
if (!isset($_SESSION['stats_users']['graphe_width']) || $_SESSION['stats_users']['graphe_width'] <= 0) {
	$_SESSION['stats_users']['graphe_width'] = "1100";
}
if (!isset($_SESSION['stats_users']['graphe_height']) || $_SESSION['stats_users']['graphe_height'] <= 0) {
	$_SESSION['stats_users']['graphe_height'] = "500";
}

$dateDebutGraphe = new DateTime();
$dateDebutGraphe->setDate(substr($_SESSION['stats_users']['date_debut'],6,4), substr($_SESSION['stats_users']['date_debut'],3,2), substr($_SESSION['stats_users']['date_debut'],0,2));
$dateFinGraphe = clone $dateDebutGraphe;
$dateFinGraphe->setDate(substr($_SESSION['stats_users']['date_fin'],6,4), substr($_SESSION['stats_users']['date_fin'],3,2), substr($_SESSION['stats_users']['date_fin'],0,2));

// check sur les dates, saisir au moins 2 périodes (2 jours mini, etc)
if($_SESSION['stats_users']['abscisse_echelle'] == 'jour') {
	$diff = date_diff2($dateDebutGraphe, $dateFinGraphe);
	if($diff == '0') {
		$dateDebutGraphe->modify('+2 month');
		$_SESSION['stats_users']['date_fin'] = $dateDebutGraphe->format('d/m/Y');
		$_SESSION['message'] = 'stats_erreur_dates';
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
} elseif ($_SESSION['stats_users']['abscisse_echelle'] == 'semaine') {
	if($dateDebutGraphe->format('Y-W') == $dateFinGraphe->format('Y-W')) {
		$dateDebutGraphe->modify('+1 month');
		$_SESSION['stats_users']['date_fin'] = $dateDebutGraphe->format('d/m/Y');
		$_SESSION['message'] = 'stats_erreur_dates';
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
} elseif ($_SESSION['stats_users']['abscisse_echelle'] == 'mois') {
	if($dateDebutGraphe->format('Y-m') == $dateFinGraphe->format('Y-m')) {
		$dateDebutGraphe->modify('+2 month');
		$_SESSION['stats_users']['date_fin'] = $dateDebutGraphe->format('d/m/Y');
		$_SESSION['message'] = 'stats_erreur_dates';
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit;
	}
}
if($dateDebutGraphe > $dateFinGraphe) {
	$tmp = $_SESSION['stats_users']['date_fin'];
	$_SESSION['stats_users']['date_fin'] = $_SESSION['stats_users']['date_debut'];
	$_SESSION['stats_users']['date_debut'] = $tmp;
}

$smarty->assign('stats_users', $_SESSION['stats_users']);

$listeUsers = new GCollection('User');
$listeUsers->db_loadSQL("SELECT pu.*, pug.nom as groupe_nom
						FROM planning_user AS pu
						LEFT JOIN planning_user_groupe AS pug ON pug.user_groupe_id = pu.user_groupe_id
						WHERE visible_planning = 'oui'
						ORDER BY groupe_nom ASC, pu.nom ASC");
$smarty->assign('listeUsers', $listeUsers->getSmartyData());

$listeProjets = new GCollection('Projet');
if($user->checkDroit('tasks_modify_own_project')) {
	$listeProjets->db_loadSQL("SELECT pp.*, pg.nom as groupe_nom
						FROM planning_projet AS pp
						LEFT JOIN planning_groupe AS pg ON pg.groupe_id = pp.groupe_id
						WHERE createur_id = " . val2sql($user->user_id) . "
						ORDER BY groupe_nom ASC, pp.nom ASC");
} elseif ($user->checkDroit('tasks_modify_own_task')) {
	$listeProjets->db_loadSQL("SELECT DISTINCT ppr.*, pg.nom AS groupe_nom
								FROM planning_projet AS ppr 
								INNER JOIN planning_periode AS ppe ON ppr.projet_id = ppe.projet_id 
								LEFT JOIN planning_groupe AS pg ON pg.groupe_id = ppr.groupe_id
								WHERE ppe.user_id = " . val2sql($user->user_id) . " 
								ORDER BY groupe_nom ASC, pp.nom ASC");
} else {
	$listeProjets->db_loadSQL("SELECT pp.*, pg.nom as groupe_nom
						FROM planning_projet AS pp
						LEFT JOIN planning_groupe AS pg ON pg.groupe_id = pp.groupe_id
						WHERE 0 = 0
						ORDER BY groupe_nom ASC, pp.nom ASC");
}
$smarty->assign('listeProjets', $listeProjets->getSmartyData());
$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));
$smarty->display('www_stats_users.tpl');
?>