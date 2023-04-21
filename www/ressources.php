<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

if (!$user->checkDroit('ressources_all')) {
    $_SESSION['erreur'] = 'droitsInsuffisants';
    header('Location: ../index.php');
    exit;
}
$ressources = new GCollection('Ressource');
$ressources->db_load(array(), array('nom' => 'ASC'));
$smarty->assign('ressources', $ressources->getSmartyData());
$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));
$smarty->display('www_ressources.tpl');
