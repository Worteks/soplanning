<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

if(!$user->checkDroit('parameters_all')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: index.php');
	exit;
}

if (!extension_loaded('zip')) {
    {
        echo "Need php-zip extension";
        exit;
    }
}

$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));
$smarty->display('www_backup.tpl');
?>