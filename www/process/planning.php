<?php

require 'base.inc';
require BASE . '/../config.inc';
require BASE . '/../includes/header.inc';

// Conversion des dates en mode mobile au format french
if(isset($_GET['date_fin_affiche'])){
	$_GET['date_fin_affiche'] = forceUserDateFormat($_GET['date_fin_affiche']);
	$_GET['date_debut_affiche'] = forceUserDateFormat($_GET['date_debut_affiche']);
}

 if(isset($_GET['date_debut_custom']) && $_GET['date_debut_custom'] != '') {
	$dateDebut = initDateTime($_GET['date_debut_affiche']);
	$dateFin = initDateTime($_GET['date_fin_affiche']);

	if($_GET['date_debut_custom'] == 'aujourdhui') {
		$dateDebut = new Datetime();

		// si date de fin inférieure é nouvelle date de début, on recupere l'interval de jour initial
		if($dateFin < $dateDebut) {
			$dateFin = new DateTime();
			$dateFin->modify('+ ' . $interval . ' days');
			$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
		}
	} elseif($_GET['date_debut_custom'] == 'semaine_derniere') {
		$dateDebut = new Datetime();
		$dateDebut->modify('- 7 days');

		// si date de fin inférieure é nouvelle date de début, on recupere l'interval de jour initial
		if($dateFin < $dateDebut) {
			$dateFin = new DateTime();
			$dateFin->modify('+ ' . $interval . ' days');
			$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
		}
	} elseif($_GET['date_debut_custom'] == 'mois_dernier') {
		$dateDebut = new Datetime();
		$dateDebut->modify('- 1 month');

		// si date de fin inférieure é nouvelle date de début, on recupere l'interval de jour initial
		if($dateFin < $dateDebut) {
			$dateFin = new DateTime();
			$dateFin->modify('+ ' . $interval . ' days');
			$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
		}
	} elseif($_GET['date_debut_custom'] == 'debut_semaine') {
		$dateDebut = new Datetime();
		$dateDebut->modify('-' . ($dateDebut->format('w')-1) . ' days');

		// si date de fin inférieure é nouvelle date de début, on recupere l'interval de jour initial
		if($dateFin < $dateDebut) {
			$dateFin = new DateTime();
			$dateFin->modify('+ ' . $interval . ' days');
			$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
		}
	} elseif($_GET['date_debut_custom'] == 'debut_mois') {
		$dateDebut = new Datetime();
		$dateDebut->modify('-' . ($dateDebut->format('d')-1) . ' days');
		// si date de fin inférieure é nouvelle date de début, on recupere l'interval de jour initial
		if($dateFin < $dateDebut) {
			$dateFin = new DateTime();
			$dateFin->modify('+ ' . $interval . ' days');
			$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
		}
	}
	$_GET['date_debut_affiche'] = $dateDebut->format(CONFIG_DATE_LONG);
}

if(isset($_GET['date_fin_custom']) && $_GET['date_fin_custom'] != '') {
	$dateDebut = initDateTime($_GET['date_debut_affiche']);
	$dateFin =initDateTime($_GET['date_fin_affiche']);
	$interval = date_diff2($dateDebut,$dateFin);

	if($_GET['date_fin_custom'] == '1_semaine') {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+ 6 days');
	} elseif($_GET['date_fin_custom'] == '2_semaines') {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+ 13 days');
	} elseif($_GET['date_fin_custom'] == '3_semaines') {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+ 20 days');
	} elseif($_GET['date_fin_custom'] == '1_mois') {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+ 1 month');
		$dateFin->modify('- 1 day');
	} elseif($_GET['date_fin_custom'] == '2_mois') {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+ 2 months');
		$dateFin->modify('- 1 day');
	} elseif($_GET['date_fin_custom'] == '3_mois') {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+ 3 months');
		$dateFin->modify('- 1 day');
	} elseif($_GET['date_fin_custom'] == '4_mois') {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+ 4 months');
		$dateFin->modify('- 1 day');
	} elseif($_GET['date_fin_custom'] == '5_mois') {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+ 5 months');
		$dateFin->modify('- 1 day');
	} elseif($_GET['date_fin_custom'] == '6_mois') {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+ 6 months');
		$dateFin->modify('- 1 day');
		}
	$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
}

// modif avancer ou reculer de l'intervalle existant
if(isset($_GET['raccourci_date'])) {
	$dateDebut = initDateTime($_SESSION['date_debut_affiche']);
	$dateFin =initDateTime($_SESSION['date_fin_affiche']);
	$interval = date_diff2($dateDebut,$dateFin);
	if($_GET['raccourci_date'] == 'aujourdhui') {
		$dateDebut = new Datetime();
		$dateFin = clone $dateDebut;
		// si vue par heures, on ne saisit que la date de début
		if($_SESSION['baseColonne'] == 'heures'){
			$dateFin->modify('+ '.CONFIG_DEFAULT_NB_DAYS_DISPLAYED.' days');
		}else $dateFin->modify('+ '.CONFIG_DEFAULT_NB_MONTHS_DISPLAYED.' month');
		$_GET['date_debut_affiche'] = $dateDebut->format(CONFIG_DATE_LONG);
		$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
	}elseif($_GET['raccourci_date'] == 'moisSuivant') {
		$dateDebut->modify('+ 1 month');
		$dateFin->modify('+ 1 month');
		$_GET['date_debut_affiche'] = $dateDebut->format(CONFIG_DATE_LONG);
		$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
	}elseif($_GET['raccourci_date'] == 'moisPrecedent') {
		$dateDebut->modify('- 1 month');
		$dateFin->modify('- 1 month');
		$_GET['date_debut_affiche'] = $dateDebut->format(CONFIG_DATE_LONG);
		$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
	}else{
		if(substr($_GET['raccourci_date'], 0, 1) == '-'){
			$dateFin = clone $dateDebut;
			$dateFin->modify('-1 days');
			$dateDebut = clone $dateFin;
			$dateDebut->modify(($_GET['raccourci_date']+1) . 'days');
		} else{
			//echo $dateDebut->format('Y-m-d') . ' - ' . $dateFin->format('Y-m-d') . "<br>";
			$dateDebut = clone $dateFin;
			$dateDebut->modify('+1 days');
			$dateFin = clone $dateDebut;
			$dateFin->modify(($_GET['raccourci_date']-1) . 'days');
			//echo $dateDebut->format('Y-m-d') . ' - ' . $dateFin->format('Y-m-d') . "<br>";
		}
		$_GET['date_debut_affiche'] = $dateDebut->format(CONFIG_DATE_LONG);
		$_GET['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
		//echo $_GET['date_debut_affiche'] . ' - ' . $_GET['date_fin_affiche'];
		//die;
	}
}

// si vue par heures, on ne saisit que la date de début
if($_SESSION['baseLigne'] == 'heures'){
	$_GET['date_fin_affiche'] = $_GET['date_debut_affiche'];
}

// changement date de début et fin
if(isset($_GET['date_debut_affiche']) && isset($_GET['date_fin_affiche'])) {
	$dateDebut = initDateTime($_GET['date_debut_affiche']);
	$dateFin = initDateTime($_GET['date_fin_affiche']);
	if((!$dateFin || !$dateDebut) || ($dateFin < $dateDebut)) {
			header('Location: ../planning.php');
			exit;
	}
	$_SESSION['date_debut_affiche'] = $_GET['date_debut_affiche'];
	$_SESSION['date_fin_affiche'] = $_GET['date_fin_affiche'];

	if ($_SESSION['baseColonne']<>"users")
	{	
		setcookie('date_debut_affiche', $_SESSION['date_debut_affiche'], time()+60*60*24*500, '/');
		setcookie('date_fin_affiche', $_SESSION['date_fin_affiche'], time()+60*60*24*500, '/');
	}else
	{
		setcookie('date_debut_affiche_horaire', $_SESSION['date_debut_affiche'], time()+60*60*24*500, '/');
	}
}

// changement nb mois affichés
if (isset($_GET['nb_mois']) && is_numeric($_GET['nb_mois']) && round($_GET['nb_mois']) > 0) {
	$nbMois = $_GET['nb_mois'];
	$_SESSION['nb_mois'] = $_GET['nb_mois'];
	if($_SESSION['nb_mois'] > 24){
		$_SESSION['nb_mois'] = 24;
	}
	setcookie('nb_mois', $_SESSION['nb_mois'], time()+60*60*24*500, '/');
}

// changement nb jours affichés
if (isset($_GET['nb_jours']) && is_numeric($_GET['nb_jours']) && round($_GET['nb_jours']) > 0) {
	$nbMois = $_GET['nb_jours'];
	$_SESSION['nb_jours'] = $_GET['nb_jours'];
	setcookie('nb_jours', $_SESSION['nb_jours'], time()+60*60*24*500, '/');
}

if(isset($_GET['nb_lignes'])  && is_numeric($_GET['nb_lignes']) && round($_GET['nb_lignes']) > 0) {
	$_SESSION['nb_lignes'] = $_GET['nb_lignes'];
	$_SESSION['page_lignes'] = 1;
	setcookie('nb_lignes', $_SESSION['nb_lignes'], time()+60*60*24*500, '/');
}

if(isset($_GET['page_lignes'])  && is_numeric($_GET['page_lignes']) && round($_GET['page_lignes']) > 0) {
	$_SESSION['page_lignes'] = $_GET['page_lignes'];
}

if(isset($_POST['filtreGroupeProjet'])) {
	// si filtre sur les projets, on boucle pour recuperer l'ensemble des projets choisis
	$projetsFiltre = array();
	foreach ($_POST as $keyPost => $valPost) {
		if(strpos($keyPost, 'projet_') === 0) {
			$check = new Projet();
			if(!$check->db_load(array('projet_id', '=', $valPost))){
				continue;
			}
			$projetsFiltre[] = $valPost;
		}
	}
	$_SESSION['filtreGroupeProjet'] = $projetsFiltre;
	setcookie('filtreGroupeProjet', json_encode($projetsFiltre), time()+60*60*24*500, '/');	
}

if(isset($_POST['filtreGroupeLieu'])) {
	// si filtre sur les lieux, on boucle pour recuperer l'ensemble des lieux choisis
	$filtre = $_POST['lieu'];
	$_SESSION['filtreGroupeLieu'] = $filtre;
	if (!empty($filtre))
	{
		setcookie('filtreGroupeLieu', json_encode($filtre), time()+60*60*24*500, '/');	
	}else
	{
		// suppression des cookies
		unset($_COOKIE["filtreGroupeLieu"]);
		setcookie('filtreGroupeLieu', '', time() - 3600, '/');
	}
}

if(isset($_POST['filtreGroupeRessource'])) {
	// si filtre sur les ressources de tache, on boucle pour recuperer l'ensemble des ressources choisies
	$filtre = $_POST['ressource'];
	$_SESSION['filtreGroupeRessource'] = $filtre;
	if (!empty($filtre))
	{
		setcookie('filtreGroupeRessource', json_encode($filtre), time()+60*60*24*500, '/');	
	}else
	{
		// suppression des cookies
		unset($_COOKIE["filtreGroupeRessource"]);
		setcookie('filtreGroupeRessource', '', time() - 3600, '/');
	}
}

if(isset($_GET['filtreSurRessource'])) {
	$_SESSION['filtreGroupeRessource'] = array($_GET['filtreSurRessource']);
}

if(isset($_GET['filtreSurLieu'])) {
	$_SESSION['filtreGroupeLieu'] = array($_GET['filtreSurLieu']);
}

if(isset($_GET['filtreSurProjet'])) {
	$check = new Projet();
	if(!$check->db_load(array('projet_id', '=', $_GET['filtreSurProjet']))){
		header('Location: ../planning.php');
		exit;
	}
	$_SESSION['filtreGroupeProjet'] = array($_GET['filtreSurProjet']);
	// we change planning dates to first and last task for this project
	$sql = "SELECT MIN(date_debut) AS le_min, MAX(date_debut) AS le_max1, MAX(date_fin) AS le_max2
			FROM planning_periode
			WHERE projet_id = " . val2sql($_GET['filtreSurProjet']);
	$res = db_query($sql);
	$row = db_fetch_array($res);
	if($row['le_min'] != ''){
		$_SESSION['date_debut_affiche'] = sqldate2userdate($row['le_min']);
		$_SESSION['date_fin_affiche'] = sqldate2userdate($row['le_min']);
		if($row['le_max1'] != '') {
			$_SESSION['date_fin_affiche'] = sqldate2userdate($row['le_max1']);
		}
		if($row['le_max2'] != '' && $row['le_max2'] > $row['le_max1']) {
			$_SESSION['date_fin_affiche'] = sqldate2userdate($row['le_max2']);
		}
		$dateDebut = Datetime::createFromFormat('d/m/Y', $_SESSION['date_debut_affiche']);
		$dateFin = Datetime::createFromFormat('d/m/Y', $_SESSION['date_fin_affiche']);
		$diff = $dateDebut->diff($dateFin);
		if($diff->format('%a days') > 730){
			$dateDebut->modify('+750 days');
			$_SESSION['date_fin_affiche'] = $dateDebut->format('d/m/Y');
		}

		setcookie('date_debut_affiche', $_SESSION['date_debut_affiche'], time()+60*60*24*500, '/');
		setcookie('date_fin_affiche', $_SESSION['date_fin_affiche'], time()+60*60*24*500, '/');
		header('Location: ../planning.php');
		exit;
	}
}

if(isset($_POST['filtreTexte'])) {
	$_SESSION['filtreTexte'] = $_POST['filtreTexte'];
}

if(isset($_GET['desactiverFiltreGroupeProjet'])) {
	$_SESSION['filtreGroupeProjet'] = array();
	setcookie('filtreGroupeProjet', '', time() - 3600, '/');
	unset($_COOKIE["filtreGroupeProjet"]);
}

if(isset($_GET['desactiverFiltreAvances'])) {
	$_SESSION['filtreGroupeLieu'] = array();
	$_SESSION['filtreGroupeRessource'] = array();
	// suppression des cookies
	unset($_COOKIE["filtreGroupeLieu"]);
	unset($_COOKIE["filtreGroupeRessource"]);
	setcookie('filtreGroupeLieu', '', time() - 3600, '/');
	setcookie('filtreGroupeRessource', '', time() - 3600, '/');
}

if(isset($_GET['desactiverFiltreTexte'])) {
	$_SESSION['filtreTexte'] = "";
}

if(isset($_POST['filtreUser'])) {
	// si filtre sur les Users, on boucle pour recuperer l'ensemble des Users choisis
	$UsersFiltre = array();
	foreach ($_POST as $keyPost => $valPost) {
		if(strpos($keyPost, 'user_') === 0) {
			$check = new User();
			if(!$check->db_load(array('user_id', '=', $valPost))){
				continue;
			}
			$UsersFiltre[] = $valPost;
		}
	}
    setcookie('filtreUser', implode(",", $UsersFiltre), time() + 60*60*24*365, '/');
	$_SESSION['filtreUser'] = $UsersFiltre;
}

if(isset($_GET['filtreSurUser'])) {
    setcookie('filtreUser', implode(",", array($_GET['filtreSurUser'])), time() + 60*60*24*365, '/');
	$_SESSION['filtreUser'] = array($_GET['filtreSurUser']);
}

if(isset($_GET['desactiverFiltreUser'])) {
	$_SESSION['filtreUser'] = array();
	// suppression des cookies
	unset($_COOKIE["filtreUser"]);
	setcookie('filtreUser', '', time() - 3600, '/');
}

if(isset($_GET['masquerLigneVide'])) {
	$_SESSION['masquerLigneVide'] = $_GET['masquerLigneVide'];
	setcookie('masquerLigneVide', $_SESSION['masquerLigneVide'], time()+60*60*24*500, '/');
	$pageLignes = 1;
	$_SESSION['page_lignes'] = $pageLignes;
}

if(isset($_GET['afficherLigneTotal'])) {
	$_SESSION['afficherLigneTotal'] = $_GET['afficherLigneTotal'];
	setcookie('afficherLigneTotal', $_SESSION['afficherLigneTotal'], time()+60*60*24*500, '/');
}

if(isset($_GET['afficherLigneTotalTaches'])) {
	$_SESSION['afficherLigneTotalTaches'] = $_GET['afficherLigneTotalTaches'];
	setcookie('afficherLigneTotalTaches', $_SESSION['afficherLigneTotalTaches'], time()+60*60*24*500, '/');
}

if(isset($_GET['afficherTableauRecap'])) {
	$_SESSION['afficherTableauRecap'] = $_GET['afficherTableauRecap'];
	setcookie('afficherTableauRecap', $_SESSION['afficherTableauRecap'], time()+60*60*24*500, '/');
}

if(isset($_POST['filtreStatutTache'])) {
	// si filtre sur les statuts de tache, on boucle pour recuperer l'ensemble des projets choisis
	$filtre = $_POST['statutsTache'];
	// si tous les status sont cochés, revient à desactiver le filtre
	$statuts = new GCollection('Status');
	$statuts->db_load(array('affichage', 'IN', array('t', 'tp')));
	if(count($filtre) >= $statuts->getCount() || !isset($_POST['statutsTache'])) {
		$filtre = array();
	}
	if (!empty($filtre))
	{
		setcookie('filtreStatutTache', json_encode($filtre), time()+60*60*24*500, '/');	
	}else
	{
		// suppression des cookies
		unset($_COOKIE["filtreStatutTache"]);
		setcookie('filtreStatutTache', '', time() - 3600, '/');
	}
	$_SESSION['filtreStatutTache'] = $filtre;
}

if(isset($_POST['filtreStatutProjet'])) {
	// si filtre sur les statuts de projet, on boucle pour recuperer l'ensemble des projets choisis
	$filtre = $_POST['statutsProjet'];
	// si tous les status sont cochés, revient à desactiver le filtre
	$statuts = new GCollection('Status');
	$statuts->db_load(array('affichage', 'IN', array('p', 'tp')));
	if(count($filtre) >= $statuts->getCount() || !isset($_POST['statutsProjet'])) {
		$filtre = array();
	}
	$_SESSION['filtreStatutProjet'] = $filtre;
	if (!empty($filtre))
	{
		setcookie('filtreStatutProjet', json_encode($filtre), time()+60*60*24*500, '/');	
	}else
	{
		// suppression des cookies
		unset($_COOKIE["filtreStatutProjet"]);
		setcookie('filtreStatutProjet', '', time() - 3600, '/');
	}
}

if(isset($_GET['baseLigne'])) {
		$_SESSION['baseLigne'] = $_GET['baseLigne'];
	} elseif (!isset($_SESSION['baseLigne'])) {
		$_SESSION['baseLigne'] = 'users';
	}
	setcookie('baseLigne', $_SESSION['baseLigne'], time()+60*60*24*500, '/');

if(isset($_GET['baseColonne'])) {
		$_SESSION['baseColonne'] = $_GET['baseColonne'];
	}elseif (!isset($_SESSION['baseColonne'])) {
		$_SESSION['baseColonne'] = 'jours';
	}elseif ($_SESSION['baseColonne']=='users' && $_SESSION['baseLigne']<>'heures')
	{
		$_SESSION['baseColonne'] = 'jours';
	}; 
setcookie('baseColonne', $_SESSION['baseColonne'], time()+60*60*24*500, '/');

if(isset($_GET['fleches'])) {
		$_SESSION['fleches'] = $_GET['fleches'];
		setcookie('fleches', $_SESSION['fleches'], time()+60*60*24*500, '/');
}

//modif tri planning pour sauvegarde selection
if(isset($_GET['triPlanning'])) {
	if (($_SESSION['baseLigne'] == "users") &&  (in_array($_GET['triPlanning'], $triPlanningPossibleUser)))
	{
		$_SESSION['triPlanningUser'] = $_GET['triPlanning'];
		setcookie('triPlanningUser', $_SESSION['triPlanningUser'], time()+60*60*24*500, '/');		
	}
	if (($_SESSION['baseLigne'] == "projets") &&  (in_array($_GET['triPlanning'], $triPlanningPossibleProjet)))
	{
		$_SESSION['triPlanningProjet'] = $_GET['triPlanning'];
		setcookie('triPlanningProjet', $_SESSION['triPlanningProjet'], time()+60*60*24*500, '/');		
	}
	if ($_SESSION['baseLigne'] == "lieux")
	{
		$_SESSION['triPlanningLieu'] = $_GET['triPlanning'];
		setcookie('triPlanningLieu', $_SESSION['triPlanningLieu'], time()+60*60*24*500, '/');		
	}
	if ($_SESSION['baseLigne'] == "ressources")
	{
		$_SESSION['triPlanningRessource'] = $_GET['triPlanning'];
		setcookie('triPlanningRessource', $_SESSION['triPlanningRessource'], time()+60*60*24*500, '/');		
	}
	if ($_SESSION['baseLigne'] == "heures")
	{
		$_SESSION['triPlanningAgenda'] = $_GET['triPlanning'];
		setcookie('triPlanningAgenda', $_SESSION['triPlanningAgenda'], time()+60*60*24*500, '/');		
	}
}

// Modif affichage Zoom
if(isset($_GET["dimensionCase"])){
	$_SESSION['dimensionCase'] = $_GET["dimensionCase"];
}
setcookie('dimensionCase', $_SESSION['dimensionCase'], time()+60*60*24*500, '/');


// we limit the number of days displayed in order to avoid memory limit crash on the server
$dateDebut = initDateTime($_SESSION['date_debut_affiche']);
$dateFin = initDateTime($_SESSION['date_fin_affiche']);
$diff = $dateDebut->diff($dateFin);
if($_SESSION['baseColonne'] == 'heures'){
	if($diff->format('%a') > 60){
		$dateDebut->modify('+60 days');
		$_SESSION['date_fin_affiche'] = $dateDebut->format('d/m/Y');
		setcookie('date_fin_affiche', $_SESSION['date_fin_affiche'], time()+60*60*24*500, '/');
	}
} else{
	if($diff->format('%a') > 730){
		$dateDebut->modify('+730 days');
		$_SESSION['date_fin_affiche'] = $dateDebut->format('d/m/Y');
		setcookie('date_fin_affiche', $_SESSION['date_fin_affiche'], time()+60*60*24*500, '/');
	}
}

header('Location: ../planning.php');
exit;

?>
