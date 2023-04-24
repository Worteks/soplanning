<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

if(!$user->checkDroit('users_manage_all') && !$user->checkDroit('users_manage_team')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: index.php');
	exit;
}

if (isset($_GET['order']) && in_array($_GET['order'], array('nom', 'email', 'user_id', 'login', 'visible_planning'))) {
	$order = $_GET['order'];
} elseif (isset($_SESSION['user_order'])) {
	$order = $_SESSION['user_order'];
} else {
	$order = 'nom';
}

if (isset($_GET['filtreEquipe'])) {
} elseif (isset($_SESSION['user_filtreEquipe'])) {
	$filtreEquipe = $_SESSION['user_filtreEquipe'];
} else {
	$filtreEquipe = array();
}

if (isset($_GET['by']) && in_array($_GET['by'], array('asc', 'desc'))) {
	$by = $_GET['by'];
} elseif (isset($_SESSION['user_by'])) {
	$by = $_SESSION['user_by'];
} else {
	$by = 'asc';
}

if(isset($_GET['desactiverfiltreEquipe'])) {
	$filtreEquipe = array();
	$_SESSION['user_filtreEquipe'] = $filtreEquipe;
}
if (isset($_POST['filtreEquipe'])) {
	$filtreEquipe = array();
	if(isset($_POST['gu'])) {
		$filtreEquipe = $_POST['gu'];
	}
	if(isset($_POST['gu0'])) {
		$filtreEquipe[] = 'gu0';
	}
} elseif (isset($_SESSION['user_filtreEquipe'])) {
	$filtreEquipe = $_SESSION['user_filtreEquipe'];
} else {
	$filtreEquipe = array();
}

$filtreUser="";
if(isset($_POST['rechercheUser']))
{
	 $filtreUser=$_POST['rechercheUser'];
	 $_SESSION['rechercheUser'] = $_POST['rechercheUser'];
} elseif(isset($_SESSION['rechercheUser'])){
	 $filtreUser=$_SESSION['rechercheUser'];
}

$users = new GCollection('User');

$sql = 'SELECT distinct pu.nom, pu.email, pu.user_id, pu.login, pu.visible_planning, pu.couleur, pu.droits, pug.nom AS nom_groupe, pu.adresse, pu.telephone, pu.mobile, pu.metier, pu.commentaire, pu.date_dernier_login, pu.login_actif, pu.date_creation, pu.date_modif, COUNT(pp.periode_id) AS totalPeriodes
		FROM planning_user pu
		LEFT JOIN planning_periode pp ON pu.user_id = pp.user_id
		LEFT JOIN planning_user_groupe pug ON pug.user_groupe_id = pu.user_groupe_id
		WHERE pu.user_id <> "publicspl" ';
if(!empty($filtreEquipe)) {
	$sql .= "		AND (pu.user_groupe_id IN ('" . implode("','", $filtreEquipe) . "')";
	if(in_array('gu0', $filtreEquipe)) {
		$sql .= '	OR pug.user_groupe_id IS NULL ';
	}
	$sql .= ' )';
}
if($filtreUser<>""){	
	$sql .= "		AND ( (pu.nom like '%$filtreUser%') or (pu.login like '%$filtreUser%') or (pu.user_id like '%$filtreUser%') or (pu.email like '%$filtreUser%')  or (pu.adresse like '%$filtreUser%') or (pu.telephone like '%$filtreUser%') or (pu.mobile like '%$filtreUser%') or (pu.metier like '%$filtreUser%') or (pu.commentaire like '%$filtreUser%'))";
}	
if ($user->checkDroit('users_manage_team'))
	{
			$sql .= " AND pu.user_groupe_id = " . val2sql($user->user_groupe_id);			
	}
$sql .= '			GROUP BY pu.nom, pu.user_id, pu.login, pu.visible_planning, pu.couleur, pu.droits, nom_groupe
                    ORDER BY '. $order . ' ' . $by;
$users->db_loadSQL($sql);

$users->setPagination(NB_RESULT_PER_PAGE);

if (!empty($_GET['page'])) {
	$users->setCurrentPage($_GET['page']);
} elseif (isset($_SESSION['user_currentPage'])) {
	$users->setCurrentPage($_SESSION['user_currentPage']);
} else {
	$users->setCurrentPage(1);
}

$smarty->assign('rechercheUser', $filtreUser);
$smarty->assign('filtreEquipe', $filtreEquipe);
$smarty->assign('order', $order);
$smarty->assign('by', $by);
$smarty->assign('currentPage', $users->getCurrentPage());
$smarty->assign('nbPages', $users->getNbPages());

$_SESSION['user_filtreEquipe'] = $filtreEquipe;
$_SESSION['user_order'] = $order;
$_SESSION['user_by'] = $by;
$_SESSION['user_currentPage'] = $users->getCurrentPage();

$smarty->assign('users', $users->getSmartyData(TRUE));

$equipes = new GCollection('User_groupe');
$equipes->db_load(array(), array('nom' => 'ASC'));
$smarty->assign('equipes', $equipes->getSmartyData());


$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));

$smarty->display('www_user_list.tpl');
?>
