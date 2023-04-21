<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

if(!$user->checkDroit('users_manage_all') && !$user->checkDroit('users_manage_team')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: index.php');
	exit;
}

if (isset($_GET['order']) && in_array($_GET['order'], array('nom'))) {
	$order = $_GET['order'];
} elseif (isset($_SESSION['user_groupe_order'])) {
	$order = $_SESSION['user_groupe_order'];
} else {
	$order = 'nom';
}

if (isset($_GET['by']) && in_array($_GET['by'], array('asc','desc'))) {
	$by = $_GET['by'];
} elseif (isset($_SESSION['user_groupe_by'])) {
	$by = $_SESSION['user_groupe_by'];
} else {
	$by = 'asc';
}

$groupes = new GCollection('User_groupe');

if($user->checkDroit('users_manage_team'))
{
$groupes->db_loadSQL('SELECT distinct g.user_groupe_id, g.nom, COUNT(u.user_id) as "totalUsers"
						FROM planning_user_groupe g LEFT JOIN planning_user u ON g.user_groupe_id = u.user_groupe_id
						WHERE g.user_groupe_id='.$_SESSION['user_groupe_id'].'
						GROUP BY g.user_groupe_id, g.nom
						ORDER BY '. $order . ' ' . $by);
$smarty->assign('users_manage_team', 1);
}else 
{
$groupes->db_loadSQL('SELECT distinct g.user_groupe_id, g.nom, COUNT(u.user_id) as "totalUsers"
						FROM planning_user_groupe g LEFT JOIN planning_user u ON g.user_groupe_id = u.user_groupe_id
						GROUP BY g.user_groupe_id, g.nom
						ORDER BY '. $order . ' ' . $by);
$smarty->assign('users_manage_team', 0);
}
$groupes->setPagination(1000);

$smarty->assign('order', $order);
$smarty->assign('by', $by);

$_SESSION['user_groupe_order'] = $order;
$_SESSION['user_groupe_by'] = $by;

$smarty->assign('groupes', $groupes->getSmartyData(TRUE));

$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));

$smarty->display('www_user_groupes.tpl');
?>