<?php
require('./base.inc');
require(BASE . '/../config.inc');
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
if (isset($_SESSION['restore'])) {
    unset($_SESSION['restore']);
    $smarty->assign('restore_elements', $_SESSION['restore_elements']);
    $smarty->assign('fichier', $_SESSION['restore_fichier']);
    $smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));
    $smarty->display('www_restore_result.tpl');
} else {
    $smarty->display('www_restore.tpl');
}
