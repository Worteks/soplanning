<?php

require 'base.inc';
require BASE . '/../config.inc';

require BASE . '/../includes/header.inc';

if(!$user->checkDroit('projectgroups_manage_all')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: index.php');
	exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'delete'){
	if (!isset($_GET['groupe_id'])){
		die('Index introuvable, suppression impossible');
	} else {
		$groupe = new groupe();
		
		if (!$groupe->db_load(array('groupe_id', '=', $_GET['groupe_id']))) {
			echo 'probl?me de chargement de cet enregistrement';
			die();
		}else
		{
			$groupeSave = clone $groupe;
		}

		$groupe->db_delete();
	
		// Audit
		if (CONFIG_SOPLANNING_OPTION_AUDIT == 1 && CONFIG_SOPLANNING_OPTION_AUDIT_GROUPES == 1)
		{
			$old_data=$groupeSave->getData();
			$action="DG";
			$infos['new_data']=null;
			$infos['old_data']=$old_data;
			$infos['groupe'] = $old_data['nom'];
			logAction($action,$infos);
		}
		
		$_SESSION['message'] = 'traitementOK';

		header('Location: ' . BASE . '/groupe_list.php');
		exit();
	}
} else {
	
	$groupe = new groupe();
	if($_POST['groupe_id'] != '' && $_POST['groupe_id'] != 0) {
		$groupe->db_load(array('groupe_id', '=', $_POST['groupe_id']));
		$groupeSave = clone $groupe;
	}

	$groupe->loadArray($_POST);

	if (is_array($groupe->check())) {
		$_SESSION['message'] = 'error_someWrongData';
		$_SESSION['error_fields'] = $groupe->check();
		$_SESSION['error_groupe'] = $groupe->getData();
		header('Location: ' . BASE . '/groupe_form.php?rand=' . rand());
		exit();
	}

	// on checke que le groupe_id n'existe pas d?j?
	if(!$groupe->isSaved()) {
		$groupeTest = new groupe();
		if($groupeTest->db_load(array('groupe_id', '=', $_POST['groupe_id']))) {
			$_SESSION['message'] = 'groupe_id_existant';
			$_SESSION['error_fields'] = array('groupe_id');
			$_SESSION['error_groupe'] = $groupe->getData();
			header('Location: ' . BASE . '/groupe_form.php?rand=' . rand());
			exit();
		}
	}

	$groupe->db_save();
	
	// Audit
	if (CONFIG_SOPLANNING_OPTION_AUDIT == 1 && CONFIG_SOPLANNING_OPTION_AUDIT_GROUPES == 1)
	{
		$new_data=$groupe->getData();
		$infos['new_data']=$new_data;
		if (isset($groupeSave))
		{
			$old_data=$groupeSave->getData();
			$infos['old_data']=$old_data;
			$infos['informations']=$old_data['nom'];
			$action="MG";
		}else 
		{
			$infos['old_data']=null;
			$infos['informations']=$new_data['nom'];
			$action="AG";
		}
		$infos['groupe']=$groupe->groupe_id;
		logAction($action,$infos);
	}
	
	$_SESSION['message'] = 'changeOK';	
	header('Location: ' . BASE . '/groupe_list.php');
	exit();
}

?>