<?php
// Accès public
if ((CONFIG_SOPLANNING_OPTION_ACCES == 1) && isset($_GET['public']))
{
  $_SESSION['public']=1;
  $_SESSION['user_id']='publicspl';
}
// Accès public avec clé
if ( CONFIG_SOPLANNING_OPTION_ACCES == 2  && isset($_GET['public']) && isset($_GET['cle']))
{
  if (strcmp($_GET['cle'],CONFIG_SECURE_KEY)==0)
  {
   $_SESSION['public']=1;
   $_SESSION['user_id']='publicspl';
  }
}
if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
	$_SESSION['message'] = 'erreur_fin_connexion';
	header('Location: ' . BASE);
	exit;
  }

 $user = New User();
 if(!$user->db_load(array('user_id', '=', $_SESSION['user_id']))) {
	$_SESSION['message'] = 'erreur_fin_connexion';
	header('Location: ' . BASE);
	exit;
 }

if(isset($smarty)) {
	// Si on n'est pas dans un cas de page publique
	$userdata=$user->getSmartyData();
	$smarty->assign('user', $userdata);
	$_SESSION['preferences']=$userdata['tabPreferences'];
	$version = new Version();
	$infoVersion = $version->getVersion();
	$smarty->assign('infoVersion', $infoVersion);

	$tabAide = array('planning.php' => 'planning.php', 'ressources.php' => 'ressources.php', 'lieux.php' => 'lieux.php', 'user_list.php' => 'utilisateurs.php', 'user_groupes.php' => 'equipes.php', 'projets.php' => 'projets.php', 'groupe_list.php' => 'groupes.php');

	$urlCourante = $_SERVER['REQUEST_URI'];
	
	if(strpos($urlCourante, '/aide/') !== FALSE){
		$lienAide = 'index.php';
	} else{
		foreach ($tabAide as $pageCourante => $urlAide){
			if(strpos($urlCourante, $pageCourante) !== FALSE){
				$lienAide = $urlAide;
			}
		}
		if(!isset($lienAide)){			
			$lienAide = 'index.php';
		}
	}
	$smarty->assign('lienAide', BASE . '/aide/' . $lienAide);
}

?>