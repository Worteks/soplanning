<?php

// Init des variables
$html = '';
$js = '';
// jours fériés
$joursFeries = getJoursFeries();
// Jours inclus
$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);

// Base ligne
if(isset($_COOKIE['baseLigne']) && !isset($_SESSION['baseLigne'])) {
	$_SESSION['baseLigne'] = $_COOKIE['baseLigne'];
}
if (!isset($_SESSION['baseLigne'])) 
{
	$_SESSION['baseLigne'] = 'projets';
}
$base_ligne = $_SESSION['baseLigne'];
$smarty->assign('baseLigne', $base_ligne);

// Base colonne
if (!isset($_SESSION['baseColonne']))
{
	if(isset($_COOKIE['baseColonne']) && ($base_ligne<>"users" && $base_ligne<>"projets" && $base_ligne<>"lieux" && $base_ligne<>"ressources")) {
		$_SESSION['baseColonne'] = $_COOKIE['baseColonne'];
	}else{
		$_SESSION['baseColonne'] = 'jours';
	}
}
$base_colonne = $_SESSION['baseColonne'];
$smarty->assign('baseColonne', $base_colonne);

if ($base_ligne=="users") {
	$linkswitch="process/planning.php?baseLigne=projets&baseColonne=$base_colonne";
} else {
	$linkswitch="process/planning.php?baseLigne=users";
}
// Autres variables
$droitAjoutPeriode = false;
$_SESSION['lastURL'] = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
// Conversion de la durée maximale du jour en quantième
$TotalMaxJourExplode= explode (':',CONFIG_DURATION_DAY);
$TotalMaxJourH = $TotalMaxJourExplode[0];
if(count($TotalMaxJourExplode) > 1) {
	$TotalMaxJourM = $TotalMaxJourExplode[1];
} else {
	$TotalMaxJourM = 0;
}
$TotalMaxJour = ($TotalMaxJourH+$TotalMaxJourM/60);

// PARAMÈTRES ET FILTRES ////////////////////////////////

// Variables de base pour le calcul des jours du planning
// Aujourd'hui
$now = new DateTime();

// Date de début d'affichage du planning
$dateDebut = new DateTime();
$dateFin = new DateTime();

// Dans le cas d'une colonne user, on force l'intervalle au premier jour
if ($_SESSION['baseColonne']=="users" and ($base_ligne=="heures")) {
	if (isset($_COOKIE['date_debut_affiche_horaire']))
	{
		$_COOKIE['date_fin_affiche_horaire'] = $_COOKIE['date_debut_affiche_horaire'];
		$_SESSION['date_debut_affiche'] = $_COOKIE['date_debut_affiche_horaire'];
		$_SESSION['date_fin_affiche'] = $_COOKIE['date_debut_affiche_horaire'];
	}
	if (isset($_SESSION['date_debut_affiche']))
	{	
		$_SESSION['date_fin_affiche'] = $_SESSION['date_debut_affiche'];
	}else{
		$_SESSION['date_debut_affiche'] = $dateDebut->format(CONFIG_DATE_LONG);
	}
	$dateDebut = initDateTime($_SESSION['date_debut_affiche']);
	$dateFin = initDateTime($_SESSION['date_fin_affiche']);
	$smarty->assign('dateDebut', $dateDebut->format(CONFIG_DATE_LONG));
	$smarty->assign('dateDebutTexte', $smarty->getConfigVars('day_' . $dateDebut->format('w')) . ' ' . $dateDebut->format(CONFIG_DATE_LONG));
	$smarty->assign('dateDebutTexte1', $smarty->getConfigVars('day_' . $dateDebut->format('w')));
	$smarty->assign('dateDebutTexte2', $dateDebut->format(CONFIG_DATE_LONG));

} else {
	if(isset($_COOKIE['date_debut_affiche'])) {
		$_SESSION['date_debut_affiche'] = $_COOKIE['date_debut_affiche'];
	}
	if (isset($_SESSION['date_debut_affiche'])) {
		$dateDebut = initDateTime($_SESSION['date_debut_affiche']);
	}else{
		//$dateDebut->modify('-' . CONFIG_DEFAULT_NB_PAST_DAYS . ' days');
		$_SESSION['date_debut_affiche'] = $dateDebut->format(CONFIG_DATE_LONG);
	}
	$smarty->assign('dateDebut', $dateDebut->format(CONFIG_DATE_LONG));
	$smarty->assign('dateDebutTexte', $smarty->getConfigVars('day_' . $dateDebut->format('w')) . ' ' . $dateDebut->format(CONFIG_DATE_LONG));
	$smarty->assign('dateDebutTexte1', $smarty->getConfigVars('day_' . $dateDebut->format('w')));
	$smarty->assign('dateDebutTexte2', $dateDebut->format(CONFIG_DATE_LONG));
	
	// Date de fin d'affichage du planning
	if(isset($_COOKIE['date_fin_affiche'])) {
		$_SESSION['date_fin_affiche'] = $_COOKIE['date_fin_affiche'];
	}
	if (isset($_SESSION['date_fin_affiche'])) {
		$dateFin = initDateTime($_SESSION['date_fin_affiche']);
	} else {
		$dateFin = clone $dateDebut;
		$dateFin->modify('+' . CONFIG_DEFAULT_NB_MONTHS_DISPLAYED . ' months');
		$_SESSION['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
	}
}

$smarty->assign('dateFin', $dateFin->format(CONFIG_DATE_LONG));
$smarty->assign('dateFinTexte', $smarty->getConfigVars('day_' . $dateFin->format('w')) . ' ' . $dateFin->format(CONFIG_DATE_LONG));
$smarty->assign('dateFinTexte1', $smarty->getConfigVars('day_' . $dateFin->format('w')));
$smarty->assign('dateFinTexte2', $dateFin->format(CONFIG_DATE_LONG));
$dateToday = new Datetime();
$smarty->assign('dateToday', $dateToday->format(CONFIG_DATE_LONG));
// Intervalle actuel
$nbJours = getNbJoursFull($dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d'));
$smarty->assign('nbJours', $nbJours);

// Période précédente et suivante
$dateBoutonInferieur = clone $dateDebut;
$dateBoutonInferieur->modify('-' . $nbJours . 'days');
$smarty->assign('dateBoutonInferieur', $dateBoutonInferieur->format(CONFIG_DATE_LONG));
$dateBoutonSuperieur = clone $dateDebut;
$dateBoutonSuperieur->modify('+' . $nbJours . 'days');
$smarty->assign('dateBoutonSuperieur', $dateBoutonSuperieur->format(CONFIG_DATE_LONG));

// Date de livraison
// si param livraison existe, veut dire qu'on vient des projets et qu'on affiche la semaine demandée
if(isset($_GET['livraison'])) {
	if($_GET['livraison'] != '') {
		$dateDebut = initDateTime($_GET['livraison']);
		// on affiche 5 jours avant la semaine voulue
		$dateDebut->modify('-5 days');
		$_SESSION['date_debut_affiche'] = $dateDebut->format(CONFIG_DATE_LONG);
	} else {
		$dateDebut->modify('-5 days');
		$_SESSION['date_debut_affiche'] = $dateDebut->format(CONFIG_DATE_LONG);
	}
}

// Fleches
if(isset($_COOKIE['fleches'])) {
	$_SESSION['fleches'] = $_COOKIE['fleches'];
} elseif (!isset($_SESSION['fleches'])) {
	$_SESSION['fleches'] = 0;
}
$smarty->assign('fleches', $_SESSION['fleches']);

// Filtre Groupe Projet
if(isset($_COOKIE['filtreGroupeProjet'])) {
	$_SESSION['filtreGroupeProjet'] = json_decode($_COOKIE['filtreGroupeProjet']);
} elseif (!isset($_SESSION['filtreGroupeProjet'])) {
	$_SESSION['filtreGroupeProjet'] = array();
}
$smarty->assign('filtreGroupeProjet', $_SESSION['filtreGroupeProjet']);

// Filtre Groupe User
if(!isset($_SESSION['filtreGroupeUser'])) {
	$_SESSION['filtreGroupeUser'] = array();
}
$smarty->assign('filtreGroupeUser', $_SESSION['filtreGroupeUser']);

// Filtre Groupe Lieu
if(isset($_COOKIE['filtreGroupeLieu'])) {
	$_SESSION['filtreGroupeLieu'] = json_decode($_COOKIE['filtreGroupeLieu']);
} elseif (!isset($_SESSION['filtreGroupeLieu'])) {
	$_SESSION['filtreGroupeLieu'] = array();
}
$smarty->assign('filtreGroupeLieu', $_SESSION['filtreGroupeLieu']);

// Filtre Groupe Ressource
if(isset($_COOKIE['filtreGroupeRessource'])) {
	$_SESSION['filtreGroupeRessource'] = json_decode($_COOKIE['filtreGroupeRessource']);
} elseif (!isset($_SESSION['filtreGroupeRessource'])) {
	$_SESSION['filtreGroupeRessource'] = array();
}
$smarty->assign('filtreGroupeRessource', $_SESSION['filtreGroupeRessource']);

// Filtre sur un user spécifique
if(!isset($_SESSION['filtreUser'])) {
	if (isset($_COOKIE['filtreUser'])) {
		$_SESSION['filtreUser'] = explode(",", $_COOKIE['filtreUser']);
	} else {
		$_SESSION['filtreUser'] = array();
	}
}
$smarty->assign('filtreUser', $_SESSION['filtreUser']);

// Filtre par texte
if(!isset($_SESSION['filtreTexte'])) {
	$_SESSION['filtreTexte'] = '';
}
$smarty->assign('filtreTexte', $_SESSION['filtreTexte']);

// Filtre par statut de tache
if(isset($_COOKIE['filtreStatutTache'])) {
	$_SESSION['filtreStatutTache'] = json_decode($_COOKIE['filtreStatutTache']);
} elseif (!isset($_SESSION['filtreStatutTache'])) {
	if(isset($_SESSION['status_taches_par_defaut'])){
		$_SESSION['filtreStatutTache'] = $_SESSION['status_taches_par_defaut'];
	} else{
		$_SESSION['filtreStatutTache'] = array();
	}
}
$smarty->assign('filtreStatutTache', $_SESSION['filtreStatutTache']);

// Filtre par statut de projet
if(isset($_COOKIE['filtreStatutProjet'])) {
	$_SESSION['filtreStatutProjet'] = json_decode($_COOKIE['filtreStatutProjet']);
} elseif (!isset($_SESSION['filtreStatutProjet'])) {
	if(isset($_SESSION['status_projets_par_defaut'])){
		$_SESSION['filtreStatutProjet'] = $_SESSION['status_projets_par_defaut'];
	} else{
		$_SESSION['filtreStatutProjet'] = array();
	}
}
$smarty->assign('filtreStatutProjet', $_SESSION['filtreStatutProjet']);

// Tri Planning User
if((isset($_COOKIE['triPlanningUser']) && (in_array($_COOKIE['triPlanningUser'], $triPlanningPossibleUser)))) {
	$_SESSION['triPlanningUser'] = $_COOKIE['triPlanningUser'];
}else
{
	$_SESSION['triPlanningUser'] = 'nom asc';
}
$smarty->assign('triPlanningPossibleUser', $triPlanningPossibleUser);
$smarty->assign('triPlanningUser', $_SESSION['triPlanningUser']);

// Tri planning Projet
if((isset($_COOKIE['triPlanningProjet']) && (in_array($_COOKIE['triPlanningProjet'], $triPlanningPossibleProjet)))) {
	$_SESSION['triPlanningProjet'] = $_COOKIE['triPlanningProjet'];
}else
{
	$_SESSION['triPlanningProjet'] = 'nom asc';
}
$smarty->assign('triPlanningPossibleProjet', $triPlanningPossibleProjet);
$smarty->assign('triPlanningProjet', $_SESSION['triPlanningProjet']);

// Tri planning Lieux
if(isset($_COOKIE['triPlanningLieu']) && (in_array($_COOKIE['triPlanningLieu'], $triPlanningPossibleAutre))) {
	$_SESSION['triPlanningLieu'] = $_COOKIE['triPlanningLieu'];
}else
{
	$_SESSION['triPlanningLieu'] = 'nom asc';
}
$smarty->assign('triPlanningLieu', $_SESSION['triPlanningLieu']);

// Tri planning Ressources
if(isset($_COOKIE['triPlanningRessource']) && (in_array($_COOKIE['triPlanningRessource'], $triPlanningPossibleAutre))) {
	$_SESSION['triPlanningRessource'] = $_COOKIE['triPlanningRessource'];
}else
{
	$_SESSION['triPlanningRessource'] = 'nom asc';
}
$smarty->assign('triPlanningRessource', $_SESSION['triPlanningRessource']);

// Tri planning Agenda
if(isset($_COOKIE['triPlanningAgenda']) && (in_array($_COOKIE['triPlanningAgenda'], $triPlanningPossibleAutre))) {
	$_SESSION['triPlanningAgenda'] = $_COOKIE['triPlanningAgenda'];
}else
{
	$_SESSION['triPlanningAgenda'] = 'nom asc';
}
$smarty->assign('triPlanningAgenda', $_SESSION['triPlanningAgenda']);
$smarty->assign('triPlanningPossibleAutre', $triPlanningPossibleAutre);

// Tri planning par défaut
if($_SESSION['baseLigne'] == "projets") {
	$_SESSION['triPlanning'] = $_SESSION['triPlanningProjet'];
}elseif($_SESSION['baseLigne'] == "users"){
	$_SESSION['triPlanning'] = $_SESSION['triPlanningUser'];
}elseif($_SESSION['baseLigne'] == "lieux"){
	$_SESSION['triPlanning'] = $_SESSION['triPlanningLieu'];
}elseif($_SESSION['baseLigne'] == "ressources"){
	$_SESSION['triPlanning'] = $_SESSION['triPlanningRessource'];
}elseif($_SESSION['baseLigne'] == "heures"){
	$_SESSION['triPlanning'] = $_SESSION['triPlanningAgenda'];
}
$smarty->assign('triPlanning', $_SESSION['triPlanning']);

// Nombre de lignes affichées
if(isset($_COOKIE['nb_lignes']) && is_numeric($_COOKIE['nb_lignes'])) {
	$_SESSION['nb_lignes'] = $_COOKIE['nb_lignes'];
}
if (isset($_SESSION['nb_lignes'])) {
	$nbLignes = $_SESSION['nb_lignes'];
} else {
	$nbLignes = CONFIG_DEFAULT_NB_ROWS_DISPLAYED;
	$_SESSION['nb_lignes'] = $nbLignes;
}
$smarty->assign('nbLignes', $nbLignes);

// Nombre de lignes par pages
if (isset($_SESSION['page_lignes'])) {
	$pageLignes = $_SESSION['page_lignes'];
} else {
	$pageLignes = 1;
	$_SESSION['page_lignes'] = $pageLignes;
}
$smarty->assign('pageLignes', $pageLignes);

// Lignes vides ou pas
if(isset($_COOKIE['masquerLigneVide'])) {
	$_SESSION['masquerLigneVide'] = $_COOKIE['masquerLigneVide'];
}
if (isset($_SESSION['masquerLigneVide'])) {
	$masquerLigneVide = $_SESSION['masquerLigneVide'];
} else {
	$masquerLigneVide = 0;
	$_SESSION['masquerLigneVide'] = $masquerLigneVide;
}
$smarty->assign('masquerLigneVide', $masquerLigneVide);

// Affichage tableau recap
if(isset($_COOKIE['afficherTableauRecap'])) {
	$_SESSION['afficherTableauRecap'] = $_COOKIE['afficherTableauRecap'];
}
if (isset($_SESSION['afficherTableauRecap'])) {
	$afficherTableauRecap = $_SESSION['afficherTableauRecap'];
} else {
	$afficherTableauRecap = 1;
	$_SESSION['afficherTableauRecap'] = $afficherTableauRecap;
}
$smarty->assign('afficherTableauRecap', $afficherTableauRecap);

// Lignes des totaux
if(isset($_COOKIE['afficherLigneTotal'])) {
	$_SESSION['afficherLigneTotal'] = $_COOKIE['afficherLigneTotal'];
}
if (isset($_SESSION['afficherLigneTotal'])) {
	$afficherLigneTotal = $_SESSION['afficherLigneTotal'];
} else {
	$afficherLigneTotal = 0;
	$_SESSION['afficherLigneTotal'] = $afficherLigneTotal;
}
$smarty->assign('afficherLigneTotal', $afficherLigneTotal);

if(isset($_COOKIE['afficherLigneTotalTaches'])) {
	$_SESSION['afficherLigneTotalTaches'] = $_COOKIE['afficherLigneTotalTaches'];
}
if (isset($_SESSION['afficherLigneTotalTaches'])) {
	$afficherLigneTotalTaches = $_SESSION['afficherLigneTotalTaches'];
} else {
	$afficherLigneTotalTaches = 0;
	$_SESSION['afficherLigneTotalTaches'] = $afficherLigneTotalTaches;
}
$smarty->assign('afficherLigneTotalTaches', $afficherLigneTotalTaches);

if ($_SESSION['baseColonne']=="jours")
{
	$_SESSION['planningView'] = 'mois';
}elseif ($_SESSION['baseColonne']=="heures")
{
	$_SESSION['planningView'] = 'jour';
}else $_SESSION['planningView'] = 'mois';

// Affichage large ou reduit
if(isset($_SESSION['dimensionCase']) and in_array($_SESSION['dimensionCase'],array('large','reduit'))) {
	$dimensionCase = $_SESSION['dimensionCase'];
}else{
	$_SESSION['dimensionCase'] = 'reduit';
	$dimensionCase = $_SESSION['dimensionCase'];
}

// Direct period
if(isset($_SESSION['direct_periode_id'])) {
	$smarty->assign('direct_periode_id', $_SESSION['direct_periode_id']);
	unset($_SESSION['direct_periode_id']);
}

// Liste des projets couvrant la période, pour le filtre de projets
$projetsFiltre = new GCollection('Projet');
$sql = "SELECT distinct pp.*, pg.nom AS groupe_nom, pp.nom as projet_nom, pp.couleur as projet_couleur, pp.createur_id as projet_createur_id
		FROM planning_projet pp
		LEFT JOIN planning_periode pd ON pp.projet_id = pd.projet_id
		LEFT JOIN planning_groupe AS pg ON pp.groupe_id = pg.groupe_id ";
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'équipe de ce user
	$sql .= " LEFT JOIN planning_user AS pu ON pd.user_id = pu.user_id ";
}
$sql .= "WHERE ((0 = 0";
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
$sql .= ')';
$sql .= " OR pp.createur_id = " . val2sql($user->user_id) . ')';
// Si filtre sur statut de projet
if(count($_SESSION['filtreStatutProjet']) > 0) {
	$sql.= " AND pp.statut IN ('" . implode("','", $_SESSION['filtreStatutProjet']) . "')";
}
$sql.= " GROUP BY pp.nom, pp.projet_id
		ORDER BY pg.nom, pp.nom";
$projetsFiltre->db_loadSQL($sql);
//echo $sql;die;
$smarty->assign('listeProjets', $projetsFiltre->getSmartyData());

// Liste des projets possibles
if($user->checkDroit('tasks_view_own_projects')) {
	$listeProjetsPossibles = $projetsFiltre->get('projet_id');
}
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	$listeProjetsPossibles = $projetsFiltre->get('projet_id');
}

// calcul des heures de début et de fin en fonction des heures cochées dans les params
$tabTranchesHoraires = explode(',', CONFIG_HOURS_DISPLAYED);
$heureDebutMatin = $tabTranchesHoraires[0] . ':00';
$heureDebutAprem = '13:00';
foreach ($tabTranchesHoraires as $tmp){
	if($tmp > 13){
		$heureDebutAprem = $tmp . '.00';
		break;
	}
}

////// DONNEES POUR LES FILTRES

// Liste des utilisateurs pour filtre sur users
$usersFiltre = new GCollection('User');
$sql = "SELECT pu.*, pug.nom AS groupe_nom
		FROM planning_user pu ";
if($user->checkDroit('tasks_view_specific_users')) {
	$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = pu.user_id AND rou.owner_id = " . val2sql($user->user_id);
}
$sql .= " LEFT JOIN planning_user_groupe pug ON pu.user_groupe_id = pug.user_groupe_id
		WHERE visible_planning = 'oui' ";
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	$sql .= " AND pu.user_groupe_id = " . val2sql($user->user_groupe_id);
}
if ($user->checkDroit('tasks_view_only_own')) {
	$sql .= " AND pu.user_id = " . val2sql($user->user_id);
}
// Si filtre sur son équipe
if($user->checkDroit('droits_tasks_view_team_users')) {
	$sql.= " AND pu.user_groupe_id = '".$_SESSION['user_groupe_id']."'";
}
$sql .=	" ORDER BY groupe_nom, pu.nom";
$usersFiltre->db_loadSQL($sql);
$smarty->assign('listeUsers', $usersFiltre->getSmartyData());

// Filtre pour les lieux
if (CONFIG_SOPLANNING_OPTION_LIEUX == 1)
{
	$listeLieux = new GCollection('Lieu');
	if ($_SESSION['triPlanning'] == "nom desc")
	{
	 $listeLieux->db_load(array(), array('nom' => 'DESC'));
	}else $listeLieux->db_load(array(), array('nom' => 'ASC'));
	$smarty->assign('listeLieux', $listeLieux->getSmartyData());
}

// Filtre pour les ressources
if (CONFIG_SOPLANNING_OPTION_RESSOURCES == 1)
{
	$listeRessources = new GCollection('Ressource');
	if ($_SESSION['triPlanning'] == "nom desc")
	{
		$listeRessources->db_load(array(), array('nom' => 'DESC'));
	}else $listeRessources->db_load(array(), array('nom' => 'ASC'));
	$smarty->assign('listeRessources', $listeRessources->getSmartyData());
}

// liste des status pour tâches
$status = new GCollection('Status');
$status->db_load(array('affichage', 'IN', array('t', 'tp')), array('priorite' => 'ASC', 'nom' => 'ASC'));
$smarty->assign('listeStatusTaches', $status->getSmartyData());
$liste_status=$status->getSmartyData();

// liste des status pour projets
$status = new GCollection('Status');
$status->db_load(array('affichage', 'IN', array('p', 'tp')), array('priorite' => 'ASC', 'nom' => 'ASC'));
$smarty->assign('listeStatusProjets', $status->getSmartyData());
