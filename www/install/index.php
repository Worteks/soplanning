<?php

require('./base.inc');
require(BASE . '/../config.inc');

$smarty = new MySmarty();

// on fait ce check avant la declaration de smarty pour faire le check d'écriture du repertoire templates_c
$version = new Version();
$checkInstall = $version->checkInstall(true);

if($checkInstall === TRUE) {
	header('Location: ' . BASE . '/');
	exit;
}

if(!isset($_SESSION['installEnCours'])) {
	$_SESSION['message'] = 'start_install';
}

// valeur à tester dans le fichier de process pour s'assurer que les données viennent de la personne qui accède à cette page
$_SESSION['installEnCours'] = 1;
$smarty->assign('xajax', $xajax->getJavascript("", BASE . "/assets/js/xajax.js"));
$smarty->assign('checkInstall', $checkInstall);
$smarty->assign('cfgHostname', $cfgHostname);
$smarty->assign('cfgDatabase', $cfgDatabase);
$smarty->assign('cfgUsername', $cfgUsername);
$smarty->assign('cfgPassword', $cfgPassword);
$smarty->assign('phpversion',phpversion());
$smarty->clearAllCache();
$smarty->clearCompiledTemplate();
$smarty->display('install_index.tpl');
?>