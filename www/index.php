<?php

require_once('./base.inc');
require_once(BASE . '/../config.inc');

if(isset($_COOKIE['direct_auth'])){
	header('Location: ' . BASE . '/planning.php');
	exit;
}

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
		$_SESSION['user_groupe_id']=$user->user_groupe_id;
		header('Location: planning.php');
		exit;
	}
}

$smarty = new MySmarty();


if(CONFIG_GOOGLE_OAUTH_ACTIVE == 1){
	if(isset($_GET["code"])) {
		header('Location: process/login.php?google_code=' . urlencode($_GET["code"]));
		exit;
	}
	$google_client = new Google_oauth();
	$smarty->assign('google_auth_url', $google_client->getLink());
}

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