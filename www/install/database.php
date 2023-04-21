<?php

require 'base.inc';
require BASE . '/../config.inc';

// permet de checker que quelqu'un ne tente pas d'acc�der � la page directement
if(!isset($_SESSION['installEnCours'])) {
	header('Location: ' . BASE . '/');
	exit;
}

// on ecrase les params
if(!isset($_POST['cfgHostname']) || !isset($_POST['cfgUsername']) || !isset($_POST['cfgPassword']) || !isset($_POST['cfgDatabase'])) {
	header('Location: ' . BASE . '/');
	exit;
}
$cfgHostname = addslashes($_POST['cfgHostname']);
$cfgUsername = addslashes($_POST['cfgUsername']);
$cfgPassword = addslashes($_POST['cfgPassword']);
$cfgDatabase = addslashes($_POST['cfgDatabase']);

// installation de la base
$version = new Version();
$smarty = new MySmarty();
$res = $version->importDatabase();

if($res !== TRUE) {
	$_SESSION['erreur'] = $res;
	header('Location: ' . BASE . '/install/');
	exit;
} else {
	header('Location: ' . BASE . '/install/install_ok.php');
	exit;
}

?>
