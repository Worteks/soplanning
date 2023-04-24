<?php

require('./base.inc');
require(BASE . '/../config.inc');

// phase de login auto avec param de l'url (car accès depuis calendrier externe)
if(isset($_GET['login'])) {
	if(!isset($_GET['hash'])) {
		$_SESSION['message'] = 'erreur_bad_login';
		header('Location: ../index.php');
		exit;
	}
	$user = New User();
	if(!$user->db_load(array('login', '=', $_GET['login']))) {
		$_SESSION['message'] = 'erreur_bad_login';
		header('Location: ../index.php');
		exit;
	}

	$hashUser = md5($user->login . '¤¤' . $user->password . '¤¤' . CONFIG_SECURE_KEY);
	if($hashUser != $_GET['hash']) {
		$_SESSION['message'] = 'erreur_bad_login';
		header('Location: ../index.php');
		exit;
	}
	//$_SESSION['user_id'] = $user->user_id;
} else {
	// accès normal depuis le site
	require BASE . '/../includes/header.inc';
}

$joursFeries = getJoursFeries();

// PARAMETRES ////////////////////////////////
$dateDebut = new DateTime();
$smarty = new MySmarty();

if (isset($_GET['age'])) {
	$dateDebut->modify('-' . (int)$_GET['age'] . ' months');
}

$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);

// FIN PARAMETRES ////////////////////////////////

$now = new DateTime();

$dateFin = clone $dateDebut;
$dateFin->modify('+120 months');

$v = new vcalendar( array( 'unique_id' => 'SOPlanning', "TZID" => date_default_timezone_get()));
$v->setProperty( 'X-WR-CALNAME', 'SOPlanning calendar');
$v->setProperty( 'X-WR-CALDESC', 'Calendar generated from SOPlanning (http://www.soplanning.org)');
$v->setProperty( "method", "PUBLISH" );                    // required of some calendar software
$v->setProperty( "X-WR-TIMEZONE", date_default_timezone_get());                   // required of some calendar software
$xprops = array( "X-LIC-LOCATION" => date_default_timezone_get());                // required of some calendar software
iCalUtilityFunctions::createTimezone( $v, date_default_timezone_get(), $xprops);
$v->setProperty( 'X-PUBLISHED-TTL', 'PT1M');


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
if($user->checkDroit('tasks_view_specific_users')) {
	$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = pd.user_id AND rou.owner_id = " . val2sql($user->user_id);
}
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

// CHARGEMENT DES LIGNES (PROJET SI INVERSE)
$lines = new GCollection('Projet');
$sql = "SELECT *
		FROM planning_projet ";
if(isset($_SESSION['filtreGroupeProjet']) && count($_SESSION['filtreGroupeProjet']) > 0) {
	$sql.= " WHERE projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
}
$sql .= " ORDER BY livraison";
$lines->db_loadSQL($sql);

// FIN CHARGEMENT DES LIGNES (USERS SI NORMAL, PROJET SI INVERSE)
$nbLine = 0;
while($lineTmp = $lines->fetch()) {
	$nbLine++;
	$ligneId = $lineTmp->projet_id;

	// on charge les jours occupés pour cette ligne
	$periodes = new GCollection('Periode');
	$sql = "SELECT planning_periode.*, planning_user.*, planning_user.nom AS nom_user, planning_projet.nom AS nom_projet, planning_lieu.nom as nom_lieu
			FROM planning_periode
			INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id
			INNER JOIN planning_projet ON planning_periode.projet_id = planning_projet.projet_id
			LEFT JOIN planning_lieu ON planning_periode.lieu_id = planning_lieu.lieu_id ";
	if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
		// on filtre sur les projets de l'équipe de ce user
		$sql .= " INNER JOIN planning_user AS pu ON planning_periode.user_id = pu.user_id ";
	}
	$sql .= "  WHERE planning_periode.projet_id = " . val2sql($ligneId);
	$sql .= "	AND (
						(planning_periode.date_debut <= '" . $dateDebut->format('Y-m-d') . "' AND planning_periode.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
								OR
							(planning_periode.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
						)";

	if(isset($_SESSION['filtreGroupeProjet']) && count($_SESSION['filtreGroupeProjet']) > 0) {
		$sql.= " AND planning_periode.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
	}
	if(isset($_SESSION['filtreUser']) && count($_SESSION['filtreUser']) > 0) {
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
	if (isset($_GET['projets'])) {
		// on filtre sur les projets de l'équipe de ce user
		$liste = explode('-', $_GET['projets']);
		if(count($liste) > 0) {
			$listeFinale = implode("','", $liste);
			$sql .= " AND planning_projet.projet_id IN ('" . $listeFinale . "')";
		}
	}
	if (isset($_GET['users'])) {
		// on filtre sur les projets de l'équipe de ce user
		$liste = explode('-', str_replace(array("'", '"'), array('', ''), $_GET['users']));
		if(count($liste) > 0) {
			$listeFinale = implode("','", $liste);
			$sql .= " AND planning_periode.user_id IN ('" . $listeFinale . "')";
		}
	}
	$sql.= " ORDER BY planning_periode.date_debut";
	$periodes->db_loadSQL($sql);
	//echo $sql . '<br>';

	$joursOccupes = array();
	// pour chaque période de cette ligne, on remplie le tableau des jours occupés

	while ($periode = $periodes->fetch()) {
		$nomTache = $periode->nom_projet;
		if(!is_null($periode->titre)) {
			$nomTache .= ' : ' . $periode->titre;
		}
		$nomTache .= ' (' . $periode->nom_user . ')';
		$e = $v->newComponent('vevent');
		$e->setProperty('categories' , 'PLANNING');
		$v->setProperty( 'X-WR-TIMEZONE', date_default_timezone_get());
		$data = $periode->getHeureDebutFin();
		if(!is_null($data)) {
			$e->setProperty('dtstart', substr($periode->date_debut, 0, 4), substr($periode->date_debut, 5, 2), substr($periode->date_debut, 8, 2), substr($data['duree_details_heure_debut'],0,2), substr($data['duree_details_heure_debut'],3,2), 00);
			//$e->setProperty('duration', 0, 0, (int)substr($periode->duree, 0, 2));
			$e->setProperty('dtend', substr($periode->date_debut, 0, 4), substr($periode->date_debut, 5, 2), substr($periode->date_debut, 8, 2), substr($data['duree_details_heure_fin'],0,2), substr($data['duree_details_heure_fin'],3,2), 00);
		}elseif($periode->duree_details == 'AM') {
			$e->setProperty('dtstart', substr($periode->date_debut, 0, 4), substr($periode->date_debut, 5, 2), substr($periode->date_debut, 8, 2), 9, 00, 00);
			$e->setProperty('dtend', substr($periode->date_debut, 0, 4), substr($periode->date_debut, 5, 2), substr($periode->date_debut, 8, 2), 13, 00, 00);
		} elseif($periode->duree_details == 'PM') {
			$e->setProperty('dtstart', substr($periode->date_debut, 0, 4), substr($periode->date_debut, 5, 2), substr($periode->date_debut, 8, 2), 14, 00, 00);
			$e->setProperty('dtend', substr($periode->date_debut, 0, 4), substr($periode->date_debut, 5, 2), substr($periode->date_debut, 8, 2), 18, 00, 00);
		} elseif(!is_null($periode->duree)) {
			$e->setProperty('dtstart', substr($periode->date_debut, 0, 4), substr($periode->date_debut, 5, 2), substr($periode->date_debut, 8, 2), 9, 00, 00);
			$final = ajouterDuree('09:00', $periode->duree);
			$e->setProperty('dtend', substr($periode->date_debut, 0, 4), substr($periode->date_debut, 5, 2), substr($periode->date_debut, 8, 2), substr($final, 0, 2), substr($final, 3, 2), 00);
		} else {
			$e->setProperty('dtstart', substr($periode->date_debut, 0, 4), substr($periode->date_debut, 5, 2), substr($periode->date_debut, 8, 2), 9, 00, 00);
			$e->setProperty('dtend', substr($periode->date_fin, 0, 4), substr($periode->date_fin, 5, 2), substr($periode->date_fin, 8, 2), 18, 00, 00);
		}

		$e->setProperty('summary' , utf8_encode($nomTache));
		$e->setProperty('description', $smarty->getConfigVars('tab_commentaires') . ' : ' . utf8_encode($periode->notes));
		if(!is_null($periode->nom_lieu)) {
			$e->setProperty('location', utf8_encode($periode->nom_lieu));
		}
		$periode->getData();
	}
}

if(isset($_GET['debug'])) {
	echo nl2br($v->createCalendar());
	die;
}

$v->returnCalendar();

?>
