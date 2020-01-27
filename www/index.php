<?php

require_once('./base.inc');
require_once(BASE . '/../config.inc');


// redirection possible vers l'installeur / upgrade
$checkInstall = $version->checkInstall();

if(!$checkInstall) {
	header('Location: ' . BASE . '/install/');
	exit;
}

/* autoconnect if already opened session */
if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != '') {
	$user = New User();
	if($user->db_load(array('user_id', '=', $_SESSION['user_id']))) {
		if (!isset($_SESSION['preferences']['vueJourMois'])||($_SESSION['preferences']['vueJourMois']=='vueMois')) {
			$_SESSION['baseColonne'] = 'jours';	
		}else
		{
			$_SESSION['baseColonne'] = 'heures';
		}
		header('Location: planning.php');
		exit;
	}
}

$smarty = new MySmarty();

// header connecté non inclus sur la page de login, check de version ici
$version = new Version();
$smarty->assign('infoVersion', $version->getVersion());

if(is_file(BASE . '/../alert.txt')) {
	$alerte = file_get_contents(BASE . '/../alert.txt');
	$smarty->assign('alerte', $alerte);
}

if(is_file(BASE . '/../blocked.txt')) {
	$blocked = file_get_contents(BASE . '/../blocked.txt');
	$smarty->assign('blocked', $blocked);
}

$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));

$smarty->display('www_index.tpl');

?>