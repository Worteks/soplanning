<?php

require('./base.inc');
require(BASE . '/../config.inc');

$smarty = new MySmarty();

$_REQUEST = sanitize($_REQUEST);
$_GET = sanitize($_GET);
$_POST = sanitize($_POST);

require BASE . '/../includes/header.inc';

if(!$user->checkDroit('audit_restore_own') && !$user->checkDroit('audit_restore') ) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: ../index.php');
	exit;
}
if(isset($_GET['desactiverFiltreUserAudit']) || (!isset($_SESSION['filtreUserAudit']))) {
	$_SESSION['filtreUserAudit'] = array();
}
if(isset($_GET['desactiverFiltreGroupeProjetAudit']) || (!isset($_SESSION['filtreGroupeProjetAudit']))) {
	$_SESSION['filtreGroupeProjetAudit'] = array();
}
if(isset($_POST['desactiverfiltreGroupeAudit'])) {
	$filtreGroupeProjet = array();
	$_SESSION['groupe_filtreEquipeProjetAudit'] = $filtreGroupeProjet;
}
if (isset($_REQUEST['filtreGroupeProjetAudit'])) {
	$filtreGroupeProjet = array();
	if(isset($_REQUEST['gp'])) {
		$filtreGroupeProjet = $_REQUEST['gp'];
	}
	if(isset($_REQUEST['gp0'])) {
		$filtreGroupeProjet[] = 'gp0';
	}
} elseif (isset($_SESSION['groupe_filtreGroupeProjetAudit'])) {
	$filtreGroupeProjet = $_SESSION['groupe_filtreGroupeProjetAudit'];
} else {
	$filtreGroupeProjet = array();
}
if(isset($_POST['filtreGroupeProjetAudit'])) {
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
	$_SESSION['filtreGroupeProjetAudit'] = $projetsFiltre;
}
if(isset($_POST['desactiverfiltreUserAudit'])) {
	$filtreUser = array();
	$_SESSION['groupe_filtreUserAudit'] = $filtreUser;
}
// Purge de l'audit avec la rétention prévue
$audit_purge = new GCollection('audit');
$sqlPurge="DELETE FROM planning_audit WHERE date_modif <= DATE_ADD(CURDATE(), INTERVAL -".CONFIG_SOPLANNING_OPTION_AUDIT_RETENTION." DAY);";
$audit_purge->db_loadSQL($sqlPurge);

if(isset($_POST['filtreUserAudit'])) {
	// si filtre sur les users, on boucle pour recuperer l'ensemble des users choisis
	$usersFiltre = array();
	foreach ($_POST as $keyPost => $valPost) {
		if(strpos($keyPost, 'user_') === 0) {
			$check = new User();
			if(!$check->db_load(array('user_id', '=', $valPost))){
				continue;
			}
			$usersFiltre[] = $valPost;
		}
	}
	$_SESSION['filtreUserAudit'] = $usersFiltre;
}

// Affichage de l'audit
$audit = new GCollection('audit');
$sql = "SELECT pa.*, pu.nom as modif_nom, pu2.nom as user_nom, pp.nom as projet_nom, pl.nom as lieu_nom, pr.nom as ressource_nom, ps.nom as status_nom, pe.nom as equipe_nom, pg.nom as groupe_nom, ppe.date_debut, pu3.nom as periode_user_nom
		FROM planning_audit as pa
		LEFT JOIN planning_user as pu ON pu.user_id = pa.user_modif
		LEFT JOIN planning_user as pu2 ON pu2.user_id = pa.user_id
		LEFT JOIN planning_projet as pp ON pp.projet_id = pa.projet_id
		LEFT JOIN planning_lieu as pl ON pl.lieu_id = pa.lieu_id
		LEFT JOIN planning_ressource as pr ON pr.ressource_id = pa.ressource_id
		LEFT JOIN planning_status as ps ON ps.status_id = pa.lieu_id
		LEFT JOIN planning_periode as ppe ON ppe.periode_id = pa.periode_id
		LEFT JOIN planning_user as pu3 ON pu3.user_id = ppe.user_id
		LEFT JOIN planning_user_groupe as pe ON pe.user_groupe_id = pa.equipe_id
		LEFT JOIN planning_groupe as pg ON pg.groupe_id = pa.groupe_id
		WHERE 1=1";
	if(count($_SESSION['filtreUserAudit']) > 0)	{
		$sql.= " AND pa.user_modif IN ('" . implode("','", $_SESSION['filtreUserAudit']) . "')";
	}
	if(count($_SESSION['filtreGroupeProjetAudit']) > 0)	{
		$sql.= " AND pa.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjetAudit']) . "')";
	}	
	// 
	if ($user->checkDroit('audit_restore_own'))
	{
		$sql.= " AND pa.user_modif = '".$user->user_id."' AND not pa.type IN('C','D')";
	}
	$sql.=" ORDER by audit_id DESC";
$audit->db_loadSQL($sql);
$smarty->assign('audit', $audit->getSmartyData());

// recuperation des projets couvrant la période, pour le filtre de projets
$projetsFiltre = new GCollection('Projet');
$sql = "SELECT distinct pp.*, pg.nom AS groupe_nom
		FROM planning_projet pp
		LEFT JOIN planning_periode pd ON pp.projet_id = pd.projet_id
		LEFT JOIN planning_groupe AS pg ON pp.groupe_id = pg.groupe_id ";
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'équipe de ce user
	$sql .= " INNER JOIN planning_user AS pu ON pd.user_id = pu.user_id ";
}
if($user->checkDroit('tasks_view_own_projects')) {
	// on filtre sur les projets dont le user courant est propriétaire ou assigné
	$sql .= " AND (pp.createur_id = " . val2sql($user->user_id) . " OR pd.user_id = " . val2sql($user->user_id) . ")";
}
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'équipe de ce user
	$sql .= " AND pu.user_groupe_id = " . val2sql($user->user_groupe_id);
}
$sql .= "	GROUP BY pp.nom, pp.projet_id
			ORDER BY pg.nom, pp.nom";
$projetsFiltre->db_loadSQL($sql);


$groupeProjets = new GCollection('Groupe');
$groupeProjets->db_load(array(), array('nom' => 'ASC'));
$smarty->assign('groupeProjetsAudit', $groupeProjets->getSmartyData());

$users = new GCollection('User');
$sql = "SELECT pu.*, pug.nom AS groupe_nom
FROM planning_user pu
LEFT JOIN planning_user_groupe pug ON pu.user_groupe_id = pug.user_groupe_id ";
if($user->checkDroit('tasks_view_specific_users')) {
	$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = pu.user_id AND rou.owner_id = " . val2sql($user->user_id);
}
$sql .= "	WHERE visible_planning = 'oui' ";
if ($user->checkDroit('tasks_view_only_own')) {
	$sql .= " AND pu.user_id = " . val2sql($user->user_id);
}
$sql .=	" ORDER BY groupe_nom, pu.nom";
$users->db_loadSQL($sql);
$smarty->assign('users', $users->getSmartyData());
$smarty->assign('filtreGroupeProjetAudit', $_SESSION['filtreGroupeProjetAudit']);
$smarty->assign('filtreUserAudit', $_SESSION['filtreUserAudit']);
$smarty->assign('listeProjets', $projetsFiltre->getSmartyData());
$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));

$smarty->display('www_audit.tpl');

?>