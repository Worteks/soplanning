<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

if(!$user->checkDroit('projectgroups_manage_all')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: index.php');
	exit;
}

$groupe = new groupe();

if (isset($_GET['groupe_id']) && !$groupe->db_load(array('groupe_id', '=', $_GET['groupe_id']))) {
	$_SESSION['erreur'] = 'error_invalidParameters';
    header('Location: ' . BASE . '/index.php');
    exit();
} elseif (isset($_SESSION['error_groupe'])) {
    $groupe->setData($_SESSION['error_groupe']);
    unset($_SESSION['error_groupe']);
	$smarty->assign('error_fields', $_SESSION['error_fields']);
    unset($_SESSION['error_fields']);
}

$smarty->assign('groupe', $groupe->getSmartyData());

$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));

$smarty->display('www_groupe_form.tpl')
?>
