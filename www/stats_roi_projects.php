<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

if(!$user->checkDroit('stats_roi_projects')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: index.php');
	exit;
}

// PARAMÈTRES
$dateDebut = new DateTime();
$pattern = '/^([1-9]|0[1-9]|1[0-9]|2[0-9]|3[01])\/([1-9]|0[1-9]|1[012])\/(19[0-9][0-9]|20[0-9][0-9])$/';

if(isset($_POST['go'])) {
	$_SESSION['stats_roi_projects'] = $_POST;
} elseif(isset($_SESSION['stats_roi_projects'])) {

} else {
	$_SESSION['stats_roi_projects'] = array();
}

if (isset($_REQUEST['statut']) && is_array($_REQUEST['statut'])) {
	$statutsChoisis = $_REQUEST['statut'];
} elseif (isset($_SESSION['statut_projet']) && is_array($_SESSION['statut_projet'])) {
	$statutsChoisis = $_SESSION['statut_projet'];
} else {
	$statutsChoisis = $_SESSION['status_projets_par_defaut'];
}
$_SESSION['statut_projet'] = $statutsChoisis;
setcookie('statut_projet', json_encode($statutsChoisis), time()+60*60*24*500, '/');
$smarty->assign('statutsChoisis', $statutsChoisis);


$status = new GCollection('Status');
$status->db_load(array('affichage', 'IN', array('p','tp')), array('priorite' => 'ASC'));
$smarty->assign('listeStatus', $status->getSmartyData());


//$chaineFiltre = implode("','", $statutsChoisis));
$projets = new GCollection('Projet');
$sql = "SELECT planning_projet.*, planning_groupe.nom AS nom_groupe
			FROM planning_projet
			LEFT JOIN planning_groupe ON planning_groupe.groupe_id = planning_projet.groupe_id
			WHERE 0 = 0
			AND planning_projet.statut in ('" . implode("','", array_map('addslashes', $statutsChoisis)) . "')";
$sql .=" ORDER BY nom_groupe ASC, planning_projet.nom ASC";
$projets->db_loadSQL($sql);
$smarty->assign('projets', $projets->getSmartyData());


$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));

$smarty->display('www_stats_roi_projects.tpl');

?>