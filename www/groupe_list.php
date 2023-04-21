<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

if(!$user->checkDroit('projectgroups_manage_all')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: index.php');
	exit;
}

// PARAMÈTRES
if (isset($_GET['statut']) && is_array($_GET['statut'])) {
	$listeStatuts = $_GET['statut'];
} elseif (isset($_SESSION['statut']) && is_array($_SESSION['statut'])) {
	$listeStatuts = $_SESSION['statut'];
} else {
	$listeStatuts = $_SESSION['status_projets_par_defaut'];
}
$_SESSION['statut'] = $listeStatuts;

if (isset($_GET['order']) && in_array($_GET['order'], array('nom'))) {
	$order = $_GET['order'];
} elseif (isset($_SESSION['groupe_order'])) {
	$order = $_SESSION['groupe_order'];
} else {
	$order = 'nom';
}

if (isset($_GET['by']) && in_array($_GET['by'], array('asc','desc'))) {
	$by = $_GET['by'];
} elseif (isset($_SESSION['groupe_by'])) {
	$by = $_SESSION['groupe_by'];
} else {
	$by = 'asc';
}

// FIN PARAMÈTRES


$groupes = new GCollection('Groupe');

if(isset($_GET['rechercheProjet']) && $_GET['rechercheProjet'] != ''){
	$search = $_GET['rechercheProjet'];
	$search = explode( ' ', $search );

	$isLike = array('0');

	foreach($search as $word){
		$isLike[] = 'pg.nom LIKE '.val2sql('%' . $word . '%');
	}

	$isLike = implode(" OR ", $isLike);

	$groupesSQL = "SELECT distinct pg.groupe_id, pg.nom, pg.ordre, COUNT(pp.projet_id) as totalProjets
			FROM planning_groupe pg
			LEFT JOIN planning_projet pp ON pg.groupe_id = pp.groupe_id
			WHERE " . $isLike;
	if(count($listeStatuts) > 0){
		$groupesSQL .= " AND (pp.statut in ('" . implode("','", $listeStatuts) . "') OR pp.statut IS NULL)";
	}
	$groupesSQL .= " GROUP BY pg.groupe_id, pg.nom, pg.ordre
					ORDER BY ". $order . ' ' . $by;
	$smarty->assign('rechercheProjet', $_GET['rechercheProjet']);
}  else {
	$groupesSQL = "SELECT distinct pg.groupe_id, pg.nom, pg.ordre, COUNT(pp.projet_id) as totalProjets
			FROM planning_groupe pg
			LEFT JOIN planning_projet pp ON pg.groupe_id = pp.groupe_id";
	$groupesSQL .= " WHERE 0 = 0";
	if(count($listeStatuts) > 0){
		$groupesSQL .= " AND (pp.statut in ('" . implode("','", $listeStatuts) . "') OR pp.statut IS NULL)";
	}
	$groupesSQL .= "GROUP BY pg.groupe_id, pg.nom, pg.ordre
					ORDER BY ". $order . ' ' . $by;
	$smarty->assign('rechercheProjet', '');
}

$groupes->db_loadSQL($groupesSQL);
$groupes->setPagination(1000);

// liste des status
	$status = new GCollection('Status');
	$sql = "SELECT status_id,nom from planning_status where affichage in ('p','tp') order by priorite asc";
	$status->db_loadSQL($sql);
	$smarty->assign('listeStatus', $status->getSmartyData());
	
if (!empty($_GET['page'])) {
	$groupes->setCurrentPage($_GET['page']);
} elseif (isset($_SESSION['groupe_currentPage'])) {
	$groupes->setCurrentPage($_SESSION['groupe_currentPage']);
} else {
	$groupes->setCurrentPage(1);
}

$smarty->assign('order', $order);
$smarty->assign('by', $by);
$smarty->assign('currentPage', $groupes->getCurrentPage());
$smarty->assign('nbPages', $groupes->getNbPages());
$smarty->assign('listeStatuts', $listeStatuts);

$_SESSION['groupe_order'] = $order;
$_SESSION['groupe_by'] = $by;
$_SESSION['groupe_currentPage'] = $groupes->getCurrentPage();

$smarty->assign('groupes', $groupes->getSmartyData(TRUE));

$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));

$smarty->display('www_groupe_list.tpl');
?>