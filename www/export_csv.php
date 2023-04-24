<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

$html = '';
$js = '';

$joursFeries = getJoursFeries();

// PARAMÈTRES ////////////////////////////////
$dateDebut = initDateTime($_SESSION['date_debut_affiche']);
$dateFin = initDateTime($_SESSION['date_fin_affiche']);

$nbLignes = $_SESSION['nb_lignes'];
$pageLignes = $_SESSION['page_lignes'];

$masquerLigneVide = $_SESSION['masquerLigneVide'];

$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);

// FIN PARAMÈTRES ////////////////////////////////

$now = new DateTime();

$dateBoutonInferieur = clone $dateDebut;
$dateBoutonInferieur->modify('-1 month');

$dateBoutonSuperieur = clone $dateDebut;
$dateBoutonSuperieur->modify('+1 month');

$headerNomJours = '';
$tmpDate = clone $dateDebut;

if(!isset($_GET['debug'])) {
	header("Content-Type: application/force-download");
	header("Content-disposition: attachment; filename=planning_" . $dateDebut->format('Y-m-d') . "_" . $dateFin->format('Y-m-d') . ".csv");
}


// GESTION DES ENTETES DU TABLEAU (MOIS, SEMAINE ET JOUR)
while ($tmpDate <= $dateFin) {
	if (!in_array($tmpDate->format('w'), $DAYS_INCLUDED) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
		if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
		} else {
			$tmpDate->modify('+1 day');
			continue;
		}
	}
	$headerNomJours .= $tmpDate->format('Y-m-d') . ';';
	$tmpDate->modify('+1 day');
}

$html = 'Title;';
$html .= $headerNomJours . CRLF;


// FIN GESTION DES ENTETES DU TABLEAU (MOIS, SEMAINE ET JOUR)


// recuperation des projets couvrant la période, pour le filtre de projets
$projetsFiltre = new GCollection('Projet');
$sql = "SELECT distinct pp.*, pg.nom AS groupe_nom
		FROM planning_projet pp
		INNER JOIN planning_periode pd ON pp.projet_id = pd.projet_id
		LEFT JOIN planning_groupe AS pg ON pp.groupe_id = pg.groupe_id ";
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'équipe de ce user
	$sql .= " INNER JOIN planning_user AS pu ON pd.user_id = pu.user_id ";
}
$sql .= "WHERE (
			(pd.date_debut <= '" . $dateDebut->format('Y-m-d') . "'
			AND pd.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
			OR
			(pd.date_debut <= '" . $dateFin->format('Y-m-d') . "'
			AND pd.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
	)";
if($user->checkDroit('tasks_view_own_projects')) {
	// on filtre sur les projets dont le user courant est propriétaire ou assigné
	$sql .= " AND (pp.createur_id = " . val2sql($user->user_id) . " OR pd.user_id = " . val2sql($user->user_id) . ")";
}
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'équipe de ce user
	$sql .= " AND pu.user_groupe_id = " . val2sql($user->user_groupe_id);
}
if ($user->checkDroit('tasks_view_only_own')) {
	$sql .= " AND pd.user_id = " . val2sql($user->user_id);
}
$sql .= "	GROUP BY pp.nom, pp.projet_id
			ORDER BY pp.groupe_id, pp.nom";
$projetsFiltre->db_loadSQL($sql);
$smarty->assign('listeProjets', $projetsFiltre->getSmartyData());
if($user->checkDroit('tasks_view_own_projects')) {
	$listeProjetsPossibles = $projetsFiltre->get('projet_id');
}
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	$listeProjetsPossibles = $projetsFiltre->get('projet_id');
}

// recuperation de la liste des utilisateurs pour filtre sur users
$usersFiltre = new GCollection('User');
$sql = "SELECT * FROM planning_user WHERE visible_planning = 'oui' ORDER BY nom";
$usersFiltre->db_loadSQL($sql);
$smarty->assign('listeUsers', $usersFiltre->getSmartyData());


// CHARGEMENT DES LIGNES (USERS SI NORMAL, PROJET SI INVERSÉ)
if($_SESSION['baseLigne'] == 'projets') {
	$lines = new GCollection('Projet');
	$sql = "SELECT *
			FROM planning_projet ";
	if(count($_SESSION['filtreGroupeProjet']) > 0) {
		$sql.= " WHERE projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
	}
	$sql .= " ORDER BY livraison";
} else {
	$lines = new GCollection('User');
	$sql = "SELECT * FROM planning_user
			WHERE visible_planning = 'oui'";
	if(count($_SESSION['filtreUser']) > 0) {
		$sql.= " AND user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
	}
	if ($user->checkDroit('tasks_view_only_own')) {
		$sql .= " AND planning_user.user_id = " . val2sql($user->user_id);
	}
	$sql .= " ORDER BY nom";
}
$lines->db_loadSQL($sql);
$nbLignesTotal = $lines->getCount();

// FIN CHARGEMENT DES LIGNES (USERS SI NORMAL, PROJET SI INVERSÉ)


$nbLine = 1;
while($lineTmp = $lines->fetch()) {
	if($_SESSION['baseLigne'] == 'projets') {
		$ligneId = $lineTmp->projet_id;
	} else {
		$ligneId = $lineTmp->user_id;
	}

	$nbLine++;

	// on charge les jours occupés pour cette ligne
	$periodes = new GCollection('Periode');
	if($_SESSION['baseLigne'] == 'projets') {
		$sql = "SELECT planning_periode.*, planning_user.*, planning_user.nom as user_nom, planning_status.nom as status_nom, planning_projet.nom as projet_nom,pl.nom as lieu_nom, pr.nom as ressource_nom
				FROM planning_periode
				INNER JOIN planning_projet ON planning_periode.projet_id = planning_projet.projet_id
				INNER JOIN planning_status on planning_status.status_id = planning_periode.statut_tache
				INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id
				LEFT JOIN planning_lieu as pl on planning_periode.lieu_id = pl.lieu_id
				LEFT JOIN planning_ressource as pr on planning_periode.ressource_id = pr.ressource_id
				WHERE planning_periode.projet_id = " . val2sql($ligneId);
	} else {
		$sql = "SELECT planning_periode.*, planning_projet.*, planning_user.nom as user_nom, planning_status.nom as status_nom, planning_projet.nom as projet_nom,pl.nom as lieu_nom, pr.nom as ressource_nom
				FROM planning_periode
				INNER JOIN planning_status on planning_status.status_id = planning_periode.statut_tache
				INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id
				LEFT JOIN planning_lieu as pl on planning_periode.lieu_id = pl.lieu_id
				LEFT JOIN planning_ressource as pr on planning_periode.ressource_id = pr.ressource_id
				INNER JOIN planning_projet ON planning_periode.projet_id = planning_projet.projet_id ";
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			// on filtre sur les projets de l'équipe de ce user
			$sql .= " INNER JOIN planning_user AS pu ON planning_periode.user_id = pu.user_id ";
		}
		$sql .= " WHERE planning_periode.user_id = " .val2sql($ligneId);
	}
	$sql .= "	AND (
					(planning_periode.date_debut <= '" . $dateDebut->format('Y-m-d') . "' AND planning_periode.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
						OR
					(planning_periode.date_debut <= '" . $dateFin->format('Y-m-d') . "' AND planning_periode.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
						)";
	if(count($_SESSION['filtreGroupeProjet']) > 0) {
		$sql.= " AND planning_periode.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
	}
	if(count($_SESSION['filtreUser']) > 0) {
		$sql.= " AND planning_periode.user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
	}
	if($user->checkDroit('tasks_view_own_projects')) {
		$sql .= " AND planning_periode.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
	}
	if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
		// on filtre sur les projets de l'équipe de ce user
		$sql .= " AND pu.user_groupe_id = " . val2sql($user->user_groupe_id);
	}
	if ($user->checkDroit('tasks_view_only_own')) {
		$sql .= " AND planning_periode.user_id = " . val2sql($user->user_id);
	}
	$sql.= " ORDER BY planning_periode.date_debut";
//echo $sql;
//exit;
	$periodes->db_loadSQL($sql);
	$joursOccupes = array();
	// pour chaque période de cette ligne, on remplie le tableau des jours occupés
	while ($periode = $periodes->fetch()) {
		$infosJour = $periode->getData();
		if($_SESSION['baseLigne'] == 'projets') {
			$infosJour['projet_nom'] = $lineTmp->nom;
			$infosJour['user_nom'] = $periode->nom;
		} else {
			$infosJour['projet_nom'] = $periode->nom;
			$infosJour['user_nom'] = $lineTmp->nom;
		}

		$dateDebut_projet = new DateTime();
		$dateDebut_projet->setDate(substr($periode->date_debut,0,4), substr($periode->date_debut,5,2), substr($periode->date_debut,8,2));

		$dateFin_projet = new DateTime();

		$tmpDate = clone $dateDebut_projet;
		if (is_null($periode->date_fin)) {
			$dateFin_projet = clone $dateDebut_projet;
		}
		else {
			$dateFin_projet->setDate(substr($periode->date_fin,0,4), substr($periode->date_fin,5,2), substr($periode->date_fin,8,2));
		}

		while ($tmpDate <= $dateFin_projet) {
				if (isset($joursOccupes[$tmpDate->format('Y-m-d')])) {
					if(CONFIG_PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY == 0) {
						$tmpArray = $joursOccupes[$tmpDate->format('Y-m-d')];
						$tmpArray[] = $infosJour;
						$joursOccupes[$tmpDate->format('Y-m-d')] = $tmpArray;
					}
				} else {
					$tmpArray = array($infosJour);
					$joursOccupes[$tmpDate->format('Y-m-d')] = $tmpArray;
				}
			$tmpDate->modify('+1 day');
		}
	}

	// si option activée, on masque la ligne si elle est vide
	if($masquerLigneVide == 1 && count($joursOccupes) == 0) {
		continue;
	}

	$html .= $lineTmp->nom . ';';
	$tmpDate = clone $dateDebut;
	// on boucle sur la durée de l'affichage
	while ($tmpDate <= $dateFin) {
		if (!in_array($tmpDate->format('w'), $DAYS_INCLUDED) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
			} else {
				$tmpDate->modify('+1 day');
				continue;
			}
		}

		if (isset($joursOccupes[$tmpDate->format('Y-m-d')])) {
			// si il y a des periodes pour le jour courant, on boucle pour toutes les afficher
			foreach ($joursOccupes[$tmpDate->format('Y-m-d')] as $jour) {
				// la case avec le code du projet
				if( $_SESSION['baseLigne']=='projets') {
					$jour['nom_cellule']=$jour['user_id'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_PROJET;
				}
				if( $_SESSION['baseLigne']=='users') {
					$jour['nom_cellule']=$jour['projet_id'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_PERSONNE;
				}
				if( $_SESSION['baseLigne']=='lieux') {
					$jour['nom_cellule']=$jour['projet_id'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_LIEU;
				}
				if( $_SESSION['baseLigne']=='ressources') {
					$jour['nom_cellule']=$jour['projet_id'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE;
				}
				if( $_SESSION['baseLigne']=='heures') {
					$jour['nom_cellule']=$jour['projet_id'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_PROJET;
				}
				switch($type_cellule)
				{
					case 'code_projet': $jour['nom_cellule']=$jour['projet_id'];break;
					case 'code_personne': $jour['nom_cellule']=$jour['user_id'];break;
					case 'code_lieu': $jour['nom_cellule']=$jour['lieu_id'];break;
					case 'code_ressource': $jour['nom_cellule']=$jour['ressource_id'];break;
					case 'nom_projet': $jour['nom_cellule']=$jour['projet_nom'];break;
					case 'nom_personne': $jour['nom_cellule']=$jour['user_nom'];break;
					case 'nom_lieu': $jour['nom_cellule']=$jour['lieu_nom'];break;
					case 'nom_ressource': $jour['nom_cellule']=$jour['ressource_nom'];break;
					case 'nom_tache': $jour['nom_cellule']=$jour['titre'];break;
					case 'vide': $jour['nom_cellule']=" ";break;
				}
				//if($_SESSION['baseLigne'] == 'projets') {
				//	$nom = '[' . $jour['user_id'] . '] ' . $jour['nom'];
				//} else {
				//	$nom = '[' . $jour['projet_id'] . '] ' . $jour['nom'];
				//}
				if(trim($jour['nom_cellule']) == '' && count($joursOccupes[$tmpDate->format('Y-m-d')]) == 1){
					$jour['nom_cellule'] = '- - -';
				}
				$html .= $jour['nom_cellule'];
				$html .= ' - ';
			}
			// on retire le dernier - en trop
			if(count($joursOccupes[$tmpDate->format('Y-m-d')]) > 0) {
				$html = substr($html, 0, strlen($html)-3);
			}

		} else {
		}
		$html .= ';';
		$tmpDate->modify('+1 day');
	}
	$html .= CRLF;
}


$smarty->assign('html', $html);
$smarty->display('www_csv.tpl');
?>
