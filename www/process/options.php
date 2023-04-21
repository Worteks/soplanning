<?php

require 'base.inc';
require BASE . '/../config.inc';
require BASE . '/../includes/header.inc';

if(!$user->checkDroit('parameters_all')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: ../index.php');
	exit;
}


if(isset($_POST['SOPLANNING_TITLE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_TITLE'));
	$config->valeur = ($_POST['SOPLANNING_TITLE'] != '' ? $_POST['SOPLANNING_TITLE'] : NULL);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_URL'));
	$config->valeur = ($_POST['SOPLANNING_URL'] != '' ? $_POST['SOPLANNING_URL'] : NULL);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if((isset($_FILES['SOPLANNING_LOGO']) && !empty($_FILES['SOPLANNING_LOGO']['name'])) || isset($_POST['SOPLANNING_LOGO_SUPPRESSION'])) {	
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_LOGO'));
	if (isset($_POST['SOPLANNING_LOGO_SUPPRESSION']))
	{
		$config->valeur = NULL;
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
		# Effacement de l'ancien logo
		if (!empty($_POST['old_logo']))
		{
			if (!is_dir(BASE.'/upload/logo/'.$_POST['old_logo']) && file_exists(BASE.'/upload/logo/'.$_POST['old_logo'])) {
				unlink(BASE.'/upload/logo/'.$_POST['old_logo']);
				@unlink(BASE.'/upload/logo/icon.png');
			}
		}
	}else
	{
		# Vérification que le répertoire upload/logo est accessible en écriture
		if(!is__writable(BASE.'/upload/logo') && !is__writable(ini_get('upload_tmp_dir')))
		{
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;			
		}
		$res=upload_image(BASE.'/upload/logo',$_FILES['SOPLANNING_LOGO']);
		if ($res != "")
		{
			switch ($res)
			{
			case 1 : $_SESSION['message'] = 'changeNotOKImageSize';break;
			case 2 : $_SESSION['message'] = 'changeNotOKImageRepertoire';break;
			default : $_SESSION['message'] = 'changeNotOKImageErreur';break;
			}
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
		$config->valeur = $_FILES['SOPLANNING_LOGO']['name'];
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
		copy(BASE.'/upload/logo/' . $config->valeur, BASE.'/upload/logo/icon.png');
		generer_icone_a2hs(BASE.'/upload/logo/icon.png');

		# Effacement de l'ancien logo
		if ($_POST['old_logo'] <> $_FILES['SOPLANNING_LOGO']['name']) {
			 if (file_exists(BASE.'/upload/logo/'.$_POST['old_logo'])) {
				 unlink(BASE.'/upload/logo/'.$_POST['old_logo']);
			}
		}
	}
}

if(isset($_POST['SOPLANNING_THEME'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_THEME'));
	$config->valeur=$_POST['SOPLANNING_THEME'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_OPTION_ACCES'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_ACCES'));
	$config->valeur=$_POST['SOPLANNING_OPTION_ACCES'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['CONFIG_SECURE_KEY'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SECURE_KEY'));
	$config->valeur=$_POST['CONFIG_SECURE_KEY'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_OPTION_LIEUX'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_LIEUX'));
	if($_POST['SOPLANNING_OPTION_LIEUX'] == 0 || $_POST['SOPLANNING_OPTION_LIEUX'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_LIEUX'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_OPTION_RESSOURCES'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_RESSOURCES'));
	if($_POST['SOPLANNING_OPTION_RESSOURCES'] == 0 || $_POST['SOPLANNING_OPTION_RESSOURCES'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_RESSOURCES'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_OPTION_AUDIT'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT'));
	if($_POST['SOPLANNING_OPTION_AUDIT'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['SOPLANNING_OPTION_AUDIT_TACHES'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_TACHES'));
	if($_POST['SOPLANNING_OPTION_AUDIT_TACHES'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT_TACHES'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_TACHES'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['SOPLANNING_OPTION_AUDIT_PROJETS'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_PROJETS'));
	if($_POST['SOPLANNING_OPTION_AUDIT_PROJETS'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT_PROJETS'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_PROJETS'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['SOPLANNING_OPTION_AUDIT_UTILISATEURS'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_UTILISATEURS'));
	if($_POST['SOPLANNING_OPTION_AUDIT_UTILISATEURS'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT_UTILISATEURS'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_UTILISATEURS'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['SOPLANNING_OPTION_AUDIT_LIEUX'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_LIEUX'));
	if($_POST['SOPLANNING_OPTION_AUDIT_LIEUX'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT_LIEUX'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_LIEUX'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['SOPLANNING_OPTION_AUDIT_RESSOURCES'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_RESSOURCES'));
	if($_POST['SOPLANNING_OPTION_AUDIT_RESSOURCES'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT_RESSOURCES'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_RESSOURCES'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_OPTION_AUDIT_STATUTS'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_STATUTS'));
	if($_POST['SOPLANNING_OPTION_AUDIT_STATUTS'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT_STATUTS'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_STATUTS'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_OPTION_AUDIT_EQUIPES'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_EQUIPES'));
	if($_POST['SOPLANNING_OPTION_AUDIT_EQUIPES'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT_EQUIPES'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_EQUIPES'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_OPTION_AUDIT_GROUPES'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_GROUPES'));
	if($_POST['SOPLANNING_OPTION_AUDIT_GROUPES'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT_GROUPES'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_GROUPES'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_OPTION_AUDIT_RETENTION'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_RETENTION'));
	$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_RETENTION'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['SOPLANNING_OPTION_AUDIT_CONNEXIONS'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_AUDIT_CONNEXIONS'));
	if($_POST['SOPLANNING_OPTION_AUDIT_CONNEXIONS'] == 0 || $_POST['SOPLANNING_OPTION_AUDIT_CONNEXIONS'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_AUDIT_CONNEXIONS'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['SOPLANNING_OPTION_TACHES'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_TACHES'));
	if($_POST['SOPLANNING_OPTION_TACHES'] == 0 || $_POST['SOPLANNING_OPTION_TACHES'] == 1) {
		$config->valeur = $_POST['SOPLANNING_OPTION_TACHES'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_OPTION_VISITEUR_checkbox'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_VISITEUR'));
	if ($_POST['SOPLANNING_OPTION_VISITEUR_checkbox']=='on')
	{
		$droits='["tasks_modify_all","tasks_view_all_projects"]';
		$config->valeur=1;
	}
	else{
		$droits='["tasks_readonly","tasks_view_all_projects"]';
		$config->valeur=0;
	}
	// on reassigne les droits du user guest
	$sql = "UPDATE planning_user
			SET droits = '$droits'
			WHERE user_id='publicspl'";
	db_query($sql);
	
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}else
{
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_OPTION_VISITEUR'));
	$droits='["tasks_readonly","tasks_view_all_projects"]';
	$config->valeur=0;
	// on reassigne les droits du user guest
	$sql = "UPDATE planning_user
			SET droits = '$droits'
			WHERE user_id='publicspl'";
	db_query($sql);
	
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}	
}

if(isset($_POST['DAYS_INCLUDED'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'DAYS_INCLUDED'));
	$config->valeur = implode(',', $_POST['DAYS_INCLUDED']);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['HOURS_DISPLAYED'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'HOURS_DISPLAYED'));
	$config->valeur = implode(',', $_POST['HOURS_DISPLAYED']);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_DUREE_CRENEAU_HORAIRE'])) {
	if(is_numeric($_POST['PLANNING_DUREE_CRENEAU_HORAIRE'])) {
		$config = new Config();
		$config->db_load(array('cle', '=', 'PLANNING_DUREE_CRENEAU_HORAIRE'));
		$config->valeur = $_POST['PLANNING_DUREE_CRENEAU_HORAIRE'];
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
		// on change aussi la valeur en session
		$_SESSION['nb_mois'] = $config->valeur;
	} else {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['DEFAULT_NB_MONTHS_DISPLAYED'])) {
	if(is_numeric($_POST['DEFAULT_NB_MONTHS_DISPLAYED']) && round($_POST['DEFAULT_NB_MONTHS_DISPLAYED']) > 0  && round($_POST['DEFAULT_NB_MONTHS_DISPLAYED']) < 50) {
		$config = new Config();
		$config->db_load(array('cle', '=', 'DEFAULT_NB_MONTHS_DISPLAYED'));
		$config->valeur = $_POST['DEFAULT_NB_MONTHS_DISPLAYED'];
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
		// on change aussi la valeur en session
		$_SESSION['nb_mois'] = $config->valeur;
	} else {
		$_SESSION['erreur'] = 'changeNotOK';
		$_SESSION['erreur'] = $smarty->getConfigVars('options_nbMoisDefaut_erreur');
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['DEFAULT_NB_DAYS_DISPLAYED'])) {
	if(is_numeric($_POST['DEFAULT_NB_DAYS_DISPLAYED']) && round($_POST['DEFAULT_NB_DAYS_DISPLAYED']) > 0  && round($_POST['DEFAULT_NB_DAYS_DISPLAYED']) < 30) {
		$config = new Config();
		$config->db_load(array('cle', '=', 'DEFAULT_NB_DAYS_DISPLAYED'));
		$config->valeur = $_POST['DEFAULT_NB_DAYS_DISPLAYED'];
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
		// on change aussi la valeur en session
		$_SESSION['nb_jours'] = $config->valeur;
	} else {
		$_SESSION['erreur'] = 'changeNotOK';
		$_SESSION['erreur'] = $smarty->getConfigVars('options_nbjoursDefaut_erreur');
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['DEFAULT_NB_ROWS_DISPLAYED'])) {
	if(is_numeric($_POST['DEFAULT_NB_ROWS_DISPLAYED']) && round($_POST['DEFAULT_NB_ROWS_DISPLAYED']) > 0) {
		$config = new Config();
		$config->db_load(array('cle', '=', 'DEFAULT_NB_ROWS_DISPLAYED'));
		$config->valeur = $_POST['DEFAULT_NB_ROWS_DISPLAYED'];
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
		// on change aussi la valeur en session
		$_SESSION['nb_lignes'] = $config->valeur;
	} else {
		$_SESSION['erreur'] = 'changeNotOK';
		$_SESSION['erreur'] = $smarty->getConfigVars('options_nbLignes_erreur');
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_COULEUR_TACHE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_COULEUR_TACHE'));
	$config->valeur = $_POST['PLANNING_COULEUR_TACHE'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_TEXTE_TACHES_PROJET'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_TEXTE_TACHES_PROJET'));
	$config->valeur = $_POST['PLANNING_TEXTE_TACHES_PROJET'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_TEXTE_TACHES_PERSONNE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_TEXTE_TACHES_PERSONNE'));
	$config->valeur = $_POST['PLANNING_TEXTE_TACHES_PERSONNE'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_TEXTE_TACHES_LIEU'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_TEXTE_TACHES_LIEU'));
	$config->valeur = $_POST['PLANNING_TEXTE_TACHES_LIEU'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_TEXTE_TACHES_RESSOURCE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_TEXTE_TACHES_RESSOURCE'));
	$config->valeur = $_POST['PLANNING_TEXTE_TACHES_RESSOURCE'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_CELL_FONTSIZE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_CELL_FONTSIZE'));
	$config->valeur = $_POST['PLANNING_CELL_FONTSIZE'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_DIFFERENCIE_WEEKEND'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_DIFFERENCIE_WEEKEND'));
	if($_POST['PLANNING_DIFFERENCIE_WEEKEND'] == 0 || $_POST['PLANNING_DIFFERENCIE_WEEKEND'] == 1) {
		$config->valeur = $_POST['PLANNING_DIFFERENCIE_WEEKEND'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['PLANNING_DIFFERENCIE_TACHE_LIEN'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_DIFFERENCIE_TACHE_LIEN'));
	if($_POST['PLANNING_DIFFERENCIE_TACHE_LIEN'] == 0 || $_POST['PLANNING_DIFFERENCIE_TACHE_LIEN'] == 1) {
		$config->valeur = $_POST['PLANNING_DIFFERENCIE_TACHE_LIEN'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}


if(isset($_POST['PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE'));
	if($_POST['PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE'] == 0 || $_POST['PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE'] == 1) {
		$config->valeur = $_POST['PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_DIFFERENCIE_TACHE_PARTIELLE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_DIFFERENCIE_TACHE_PARTIELLE'));
	if($_POST['PLANNING_DIFFERENCIE_TACHE_PARTIELLE'] == 0 || $_POST['PLANNING_DIFFERENCIE_TACHE_PARTIELLE'] == 1) {
		$config->valeur = $_POST['PLANNING_DIFFERENCIE_TACHE_PARTIELLE'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_MASQUER_FERIES'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_MASQUER_FERIES'));
	if($_POST['PLANNING_MASQUER_FERIES'] == 0 || $_POST['PLANNING_MASQUER_FERIES'] == 1) {
		$config->valeur = $_POST['PLANNING_MASQUER_FERIES'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_HIDE_WEEKEND_TASK'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_HIDE_WEEKEND_TASK'));
	if($_POST['PLANNING_HIDE_WEEKEND_TASK'] == 0 || $_POST['PLANNING_HIDE_WEEKEND_TASK'] == 1) {
		$config->valeur = $_POST['PLANNING_HIDE_WEEKEND_TASK'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_LINE_HEIGHT'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_LINE_HEIGHT'));
	if(is_numeric($_POST['PLANNING_LINE_HEIGHT']) && round($_POST['PLANNING_LINE_HEIGHT']) > 0) {
		$config->valeur = $_POST['PLANNING_LINE_HEIGHT'];
	} else {
		$config->valeur = null;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_COL_WIDTH'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_COL_WIDTH'));
	if(is_numeric($_POST['PLANNING_COL_WIDTH']) && round($_POST['PLANNING_COL_WIDTH']) > 0) {
		$config->valeur = $_POST['PLANNING_COL_WIDTH'];
	} else {
		$config->valeur = null;
	}
	if (($_POST['PLANNING_COL_WIDTH'] < MIN_CELL_SIZE) or ($_POST['PLANNING_COL_WIDTH'] > MAX_CELL_SIZE))
	{
		$_SESSION['erreur'] = 'changeNotOK';
		$_SESSION['erreur'] = $smarty->getConfigVars('options_largeurColonne_erreur');
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;		
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_COL_WIDTH_LARGE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_COL_WIDTH_LARGE'));
	if(is_numeric($_POST['PLANNING_COL_WIDTH_LARGE']) && round($_POST['PLANNING_COL_WIDTH_LARGE']) > 0) {
		$config->valeur = $_POST['PLANNING_COL_WIDTH_LARGE'];
	} else {
		$config->valeur = null;
	}
	if (($_POST['PLANNING_COL_WIDTH_LARGE'] < MIN_CELL_SIZE) or ($_POST['PLANNING_COL_WIDTH_LARGE'] > MAX_CELL_SIZE))
	{
		$_SESSION['erreur'] = 'changeNotOK';
		$_SESSION['erreur'] = $smarty->getConfigVars('options_largeurColonneLarge_erreur');
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;		
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_CODE_WIDTH'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_CODE_WIDTH'));
	if(is_numeric($_POST['PLANNING_CODE_WIDTH']) && round($_POST['PLANNING_CODE_WIDTH']) > 0) {
		$config->valeur = $_POST['PLANNING_CODE_WIDTH'];
	} else {
		$config->valeur = null;
	}
	if (($_POST['PLANNING_CODE_WIDTH'] < MIN_CODE_SIZE) or ($_POST['PLANNING_CODE_WIDTH'] > MAX_CODE_SIZE))
	{
		$_SESSION['erreur'] = 'changeNotOK';
		$_SESSION['erreur'] = $smarty->getConfigVars('options_largeurCode_erreur');
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;		
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_CODE_WIDTH_LARGE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_CODE_WIDTH_LARGE'));
	if(is_numeric($_POST['PLANNING_CODE_WIDTH_LARGE']) && round($_POST['PLANNING_CODE_WIDTH_LARGE']) > 0) {
		$config->valeur = $_POST['PLANNING_CODE_WIDTH_LARGE'];
	} else {
		$config->valeur = null;
	}
	if (($_POST['PLANNING_CODE_WIDTH_LARGE'] < MIN_CODE_SIZE) or ($_POST['PLANNING_CODE_WIDTH_LARGE'] > MAX_CODE_SIZE))
	{
		$_SESSION['erreur'] = 'changeNotOK';
		$_SESSION['erreur'] = $smarty->getConfigVars('options_largeurCodeLarge_erreur');
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;		
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}
if(isset($_POST['PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY'));
	if($_POST['PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY'] == 0 || $_POST['PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY'] == 1) {
		$config->valeur = $_POST['PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY'];
	} else {
		$config->valeur = 0;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PLANNING_AFFICHAGE_STATUS'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_AFFICHAGE_STATUS'));
	$config->valeur = $_POST['PLANNING_AFFICHAGE_STATUS'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['REFRESH_TIMER'])) {
	if(is_numeric($_POST['REFRESH_TIMER']) && round($_POST['REFRESH_TIMER']) > 0) {
		$config = new Config();
		$config->db_load(array('cle', '=', 'REFRESH_TIMER'));
		$config->valeur = $_POST['REFRESH_TIMER'];
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
	} else {
		$_SESSION['erreur'] = 'changeNotOK';
		$_SESSION['erreur'] = $smarty->getConfigVars('options_raffraichissement_erreur');
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['PROJECT_COLORS_POSSIBLE'])) {
	if(strlen($_POST['PROJECT_COLORS_POSSIBLE']) == 0 || strlen($_POST['PROJECT_COLORS_POSSIBLE']) > 6) { 
		$config = new Config();
		$config->db_load(array('cle', '=', 'PROJECT_COLORS_POSSIBLE'));
		if(strlen($_POST['PROJECT_COLORS_POSSIBLE']) == 0) {
			$config->valeur = null;
		} else {
			$liste_couleurs=preg_replace('/\s/','',$_POST['PROJECT_COLORS_POSSIBLE']);
			$config->valeur = $liste_couleurs;
		}
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
	} else {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['DEFAULT_PERIOD_LINK'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'DEFAULT_PERIOD_LINK'));
	if(strlen($_POST['DEFAULT_PERIOD_LINK']) == 0) {
		$config->valeur = null;
	} else {
		$config->valeur = $_POST['DEFAULT_PERIOD_LINK'];
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['LOGOUT_REDIRECT'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'LOGOUT_REDIRECT'));
	if(strlen($_POST['LOGOUT_REDIRECT']) == 0) {
		$config->valeur = null;
	} else {
		$config->valeur = $_POST['LOGOUT_REDIRECT'];
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['DURATION_DAY'])) {
	$TotalJourExplode = explode (':',$_POST['DURATION_DAY']);
	$TotalJourH=$TotalJourExplode[0];
	$TotalJourM=$TotalJourExplode[1];
	if((is_numeric($TotalJourH) && round($TotalJourH) > 0)&&(is_numeric($TotalJourM) && round($TotalJourM) >= 0)) {
		$config = new Config();
		$config->db_load(array('cle', '=', 'DURATION_DAY'));
		$config->valeur = $_POST['DURATION_DAY'];
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
	} else {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['DURATION_AM'])) {
	$TotalJourAMExplode = explode (':',$_POST['DURATION_AM']);
	$TotalJourAMH=$TotalJourAMExplode[0];
	$TotalJourAMM=$TotalJourAMExplode[1];
	if((is_numeric($TotalJourAMH) && round($TotalJourAMH) > 0)&&(is_numeric($TotalJourAMM) && round($TotalJourAMM) >= 0)) {
		$config = new Config();
		$config->db_load(array('cle', '=', 'DURATION_AM'));
		$config->valeur = $_POST['DURATION_AM'];
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
	} else {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['DURATION_PM'])) {
	$TotalJourPMExplode = explode (':',$_POST['DURATION_PM']);
	$TotalJourPMH=$TotalJourPMExplode[0];
	$TotalJourPMM=$TotalJourPMExplode[1];
	if((is_numeric($TotalJourPMH) && round($TotalJourPMH) > 0)&&(is_numeric($TotalJourPMM) && round($TotalJourPMM) >= 0)) {
		$config = new Config();
		$config->db_load(array('cle', '=', 'DURATION_PM'));
		$config->valeur = $_POST['DURATION_PM'];
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
	} else {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SMTP_HOST'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SMTP_HOST'));
	$config->valeur = ($_POST['SMTP_HOST'] != '' ? $_POST['SMTP_HOST'] : NULL);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
	$config = new Config();
	$config->db_load(array('cle', '=', 'SMTP_PORT'));
	$config->valeur = ($_POST['SMTP_PORT'] != '' ? $_POST['SMTP_PORT'] : NULL);
	if(!$config->db_save()) {
		die;
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
	$config = new Config();
	$config->db_load(array('cle', '=', 'SMTP_SECURE'));
	$config->valeur = ($_POST['SMTP_SECURE'] != '' ? $_POST['SMTP_SECURE'] : NULL);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
	$config = new Config();
	$config->db_load(array('cle', '=', 'SMTP_FROM'));
	$config->valeur = ($_POST['SMTP_FROM'] != '' ? $_POST['SMTP_FROM'] : NULL);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
	$config = new Config();
	$config->db_load(array('cle', '=', 'SMTP_LOGIN'));
	$config->valeur = ($_POST['SMTP_LOGIN'] != '' ? $_POST['SMTP_LOGIN'] : NULL);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
	if($_POST['SMTP_PASSWORD'] != 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX') {
		// hack pour ne pas écraser le password si submit tel quel
		$config = new Config();
		$config->db_load(array('cle', '=', 'SMTP_PASSWORD'));
		$config->valeur = ($_POST['SMTP_PASSWORD'] != '' ? $_POST['SMTP_PASSWORD'] : NULL);
		if(!$config->db_save()) {
			$_SESSION['erreur'] = 'changeNotOK';
			header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
			exit;
		}
	}
}

if(isset($_POST['PLANNING_REPEAT_HEADER'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'PLANNING_REPEAT_HEADER'));
	if(is_numeric($_POST['PLANNING_REPEAT_HEADER']) && round($_POST['PLANNING_REPEAT_HEADER']) > 0) {
		$config->valeur = $_POST['PLANNING_REPEAT_HEADER'];
	} else {
		$config->valeur = null;
	}
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['mailTestDestinataire'])) {
	echo "<html><head><title>Test smtp</title><style>body{padding:10px;}</style></head><body><h2>Test smtp</h2><hr><pre>";
	$mail = new Mailer($_POST['mailTestDestinataire'], 'SOPLANNING - test email', 'OK');
	if(isset($_POST['smtp_traces'])) {
		$mail->SMTPDebug = 3;
	}
	
	if(!$mail->send()) {
		echo 'error while sending the email :';
		echo '<pre></body></html>';
		die;
	}
	echo "</pre>";
	if(isset($_POST['smtp_traces'])) {
		echo '<hr>' . $smarty->getConfigVars('options_envoyerMailTest_envoye');
		echo '<br><br><a href="../options.php">' . $smarty->getConfigVars('back_to_soplanning') . '<a>';
		exit;
	}

	$_SESSION['message'] = 'options_envoyerMailTest_envoye';
	header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
	exit;
}

if(isset($_POST['TIMEZONE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'TIMEZONE'));
	$config->valeur = ($_POST['TIMEZONE'] != '' ? $_POST['TIMEZONE'] : NULL);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_API_KEY_NAME'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_API_KEY_NAME'));
	$config->valeur = ($_POST['SOPLANNING_API_KEY_NAME'] != '' ? $_POST['SOPLANNING_API_KEY_NAME'] : NULL);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['SOPLANNING_API_KEY_VALUE'])) {
	$config = new Config();
	$config->db_load(array('cle', '=', 'SOPLANNING_API_KEY_VALUE'));
	$config->valeur = ($_POST['SOPLANNING_API_KEY_VALUE'] != '' ? $_POST['SOPLANNING_API_KEY_VALUE'] : NULL);
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['GOOGLE_OAUTH_CLIENT_ID'])) {
	if(isset($_POST['GOOGLE_OAUTH_ACTIVE']) && $_POST['GOOGLE_OAUTH_ACTIVE'] == 1 && CONFIG_SOPLANNING_URL == ''){
		$_SESSION['erreur'] = 'google_sso_return_url_need_setup';
		header('Location: ../options.php?tab=google-login');
		exit;
	}
	$config = new Config();
	$config->db_load(array('cle', '=', 'GOOGLE_OAUTH_ACTIVE'));
	$config->valeur= (isset($_POST['GOOGLE_OAUTH_ACTIVE']) ? '1' : '0');
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
	$config = new Config();
	$config->db_load(array('cle', '=', 'GOOGLE_OAUTH_CLIENT_ID'));
	$config->valeur=$_POST['GOOGLE_OAUTH_CLIENT_ID'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
	$config = new Config();
	$config->db_load(array('cle', '=', 'GOOGLE_OAUTH_CLIENT_SECRET'));
	$config->valeur=$_POST['GOOGLE_OAUTH_CLIENT_SECRET'];
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
}

if(isset($_POST['tab']) && $_POST['tab'] == 'google-2fa') {
	$config = new Config();
	$config->db_load(array('cle', '=', 'GOOGLE_2FA_ACTIVE'));
	$config->valeur = (isset($_POST['GOOGLE_2FA_ACTIVE']) != '' ? '1' : '0');
	if(!$config->db_save()) {
		$_SESSION['erreur'] = 'changeNotOK';
		header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
		exit;
	}
	if($config->valeur == '0'){
		db_query("UPDATE planning_user SET google_2fa = 'setup'");
	}
}


$_SESSION['message'] = 'changeOK';
header('Location: ../options.php' . (isset($_POST['tab']) ? '?tab=' . $_POST['tab'] : ''));
exit;

?>