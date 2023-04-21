<?php
require 'base.inc';
require BASE . '/../config.inc';
require BASE . '/../includes/header.inc';

$filename=$_POST['fichier'];
$fichier_import_seul=$_POST['$filename'];
$type="save";
$save_dir = SAVE_DIR."$filename".".tmp";
$zip_file = "$filename".".zip";
$elements_restauration = array();

// Vérification des options
if (isset($_POST['import_options_ecrasement']) && $_POST['import_options_ecrasement']==1) {$type_restauration=0;}else $type_restauration=1;
if (isset($_POST['import_options_configuration']) && $_POST['import_options_configuration']==1) {$type_ecrasement_configuration=0;}else $type_ecrasement_configuration=1;

// Restauration des fichiers
if (isset($_POST['export_configuration']) && $_POST['export_configuration']==1) {restore_parametres();restore_status();restore_feries();}
if (isset($_POST['export_projets']) && $_POST['export_projets']==1) {restore_projets();restore_groupes_projets();}
if (isset($_POST['export_taches']) && $_POST['export_taches']==1) restore_taches();
if (isset($_POST['export_users']) && $_POST['export_users']==1) {restore_users();restore_groupes_users();restore_user_on_right();}
if (isset($_POST['export_lieux']) && $_POST['export_lieux']==1) restore_lieux();
if (isset($_POST['export_ressources']) && $_POST['export_ressources']==1) restore_ressources();

$_SESSION['restore_fichier'] = $filename;
$_SESSION['restore_elements'] = $elements_restauration;
$_SESSION['restore'] = 'restoreOK';
header('Location: ../restore.php');

// Restauration des paramètres
function restore_parametres()
{
	global $save_dir,$type_restauration,$elements_restauration,$type_ecrasement_configuration,$smarty;
	$file=$save_dir.'/config.csv';
	if (!file_exists($file)) return true;
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$config = new Config(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"cle" || $data[1]<>"valeur" ||$data[2]<>"commentaire")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}
		if(trim($data[0]) == ''){
			continue;
		}
        // Vérification de l'existence de l'enregistrement
		if ($config->db_load(array('cle', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['config'][]=array("id"=>$config->cle,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($config->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		
		// 
		if (($type_ecrasement_configuration==0) && (in_array($data[0],array('SOPLANNING_TITLE','SOPLANNING_URL','SOPLANNING_LOGO','SOPLANNING_THEME','SOPLANNING_OPTION_ACCES','SOPLANNING_OPTION_ACCES_PUBLIC','SOPLANNING_OPTION_ACCES_PUBLICCLE','CONFIG_SECURE_KEY','SOPLANNING_OPTION_VISITEUR','TIMEZONE','SOPLANNING_API_KEY_NAME','SOPLANNING_API_KEY_VALUE','GOOGLE_OAUTH_CLIENT_ID','GOOGLE_OAUTH_CLIENT_SECRET','GOOGLE_OAUTH_ACTIVE','GOOGLE_2FA_ACTIVE'))))
		{
			$type="ignore";
		}
		// on ajoute ou met à jour les données
		$config->cle = $data[0];
		$config->valeur = ($data[1] != '' ? $data[1] : null);
		$config->commentaire = ($data[1] != '' ? $data[1] : null);

		if ($type<>"ignore")
		{
			if ($config->db_save())
			{
				$elements_restauration['config'][]=array("id"=>$config->cle,"type"=>$type,"status"=>"OK");
			}else
			{
				$elements_restauration['config'][]=array("id"=>$config->cle,"type"=>$type,"status"=>"KO","error"=>$config->fields_on_error);
			}
		}else $elements_restauration['config'][]=array("id"=>$config->cle,"type"=>$type,"status"=>"OK");
	}
	ini_set('auto_detect_line_endings',FALSE);

}

// Restauration des projets
function restore_projets()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	$file=$save_dir.'/projet.csv';
	if (!file_exists($file)) return true;
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$projet = new Projet(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"projet_id" || $data[1]<>"nom" || $data[2]<>"iteration" || $data[3]<>"couleur" || $data[4]<>"charge" || $data[5]<>"livraison" || $data[6]<>"lien" || $data[7]<>"statut" || $data[8]<>"groupe_id" || $data[9]<>"createur_id")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}
		if(trim($data[0]) == ''){
			continue;
		}
        // Vérification de l'existence de l'enregistrement
		if ($projet->db_load(array('projet_id', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['projet'][]=array("id"=>$projet->projet_id,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($projet->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		
		// on ajoute ou met à jour les données
		$projet->projet_id = $data[0];
		$projet->nom = ($data[1] != '' ? substr($data[1],0, $projet->getFieldSize('nom')) : null);
		$projet->iteration = ($data[2] != '' ? substr($data[2],0, $projet->getFieldSize('iteration')) : null);
		$projet->couleur = ($data[3] != '' ? $data[3] : null);
		$projet->charge = ($data[4] != '' ? $data[4] : null);
		$projet->livraison = ($data[5] != '' ? $data[5] : null);
		$projet->lien = ($data[6] != '' ? $data[6] : null);
		$projet->statut = ($data[7] != '' ? $data[7] : null);
		$projet->groupe_id = ($data[8] != '' ? $data[8] : null);
		$projet->createur_id = ($data[9] != '' ? $data[9] : null);

		if ($type<>"ignore")
		{
			if ($projet->db_save())
			{
				$elements_restauration['projet'][]=array("id"=>$projet->projet_id,"type"=>$type,"status"=>"OK");
			}else
			{
				$elements_restauration['projet'][]=array("id"=>$projet->projet_id,"type"=>$type,"status"=>"KO","error"=>$projet->fields_on_error);
			}
		} else $elements_restauration['projet'][]=array("id"=>$projet->projet_id,"type"=>$type,"status"=>"OK");
	}
	ini_set('auto_detect_line_endings',FALSE);
}

// Restauration des groupes projets
function restore_groupes_projets()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	$file=$save_dir.'/groupe.csv';
	if (!file_exists($file)) return true;
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$groupe = new Groupe(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"groupe_id" || $data[1]<>"nom" || $data[2]<>"ordre")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}
		if(trim($data[0]) == ''){
			continue;
		}
        // Vérification de l'existence de l'enregistrement
		if ($groupe->db_load(array('groupe_id', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['groupe'][]=array("id"=>$groupe->groupe_id,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($groupe->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		
		// on ajoute ou met à jour les données
		$groupe->groupe_id = $data[0];
		$groupe->nom = ($data[1] != '' ? substr($data[1],0, $groupe->getFieldSize('nom')) : null);
		$groupe->ordre = ($data[2] != '' ? substr($data[2],0, $groupe->getFieldSize('ordre')) : null);
		if ($type<>"ignore")
		{
			if ($groupe->db_save(array(),array(),true))
			{
				$elements_restauration['groupe'][]=array("id"=>$groupe->groupe_id,"type"=>$type,"status"=>"OK","auto_increment"=>"1");
			}else
			{
				$elements_restauration['groupe'][]=array("id"=>$groupe->groupe_id,"type"=>$type,"status"=>"KO","error"=>$groupe->fields_on_error,"auto_increment"=>"1");
			}
		}else $elements_restauration['groupe'][]=array("id"=>$groupe->groupe_id,"type"=>$type,"status"=>"OK","auto_increment"=>"1");
	}
	ini_set('auto_detect_line_endings',FALSE);
}

// Restauration des tÃ¢ches
function restore_taches()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	$file=$save_dir.'/periode.csv';
	if (!file_exists($file)) return true;
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$periode = new Periode(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"periode_id" || $data[1]<>"parent_id" || $data[2]<>"projet_id" || $data[3]<>"user_id" || $data[4]<>"link_id" || $data[5]<>"date_debut" || $data[6]<>"date_fin" || $data[7]<>"duree" || $data[8]<>"duree_details" || $data[9]<>"titre" || $data[10]<>"notes" || $data[11]<>"lien" || $data[12]<>"statut_tache" || $data[13]<>"lieu_id" || $data[14]<>"ressource_id" || $data[15]<>"livrable" || $data[16]<>"fichiers" || $data[17]<>"createur_id" || $data[18]<>"date_creation" || $data[19]<>"modifier_id" || $data[20]<>"date_modif" || $data[21]<>"custom")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}
		if(trim($data[0]) == ''){
			continue;
		}
        // Vérification de l'existence de l'enregistrement
		if ($periode->db_load(array('periode_id', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['periode'][]=array("id"=>$periode->periode_id,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($periode->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		
		// on ajoute ou met à jour les données
		$periode->periode_id = $data[0];
		$periode->parent_id = ($data[1] != '' ? $data[1] : null);
		$periode->projet_id = ($data[2] != '' ? $data[2] : null);
		$periode->user_id = ($data[3] != '' ? $data[3] : null);
		$periode->link_id = ($data[4] != '' ? $data[4] : uniqid(mt_rand()));
		$periode->date_debut = ($data[5] != '' ? $data[5] : null);
		$periode->date_fin = ($data[6] != '' ? $data[6] : null);
		$periode->duree = ($data[7] != '' ? $data[7] : null);
		$periode->duree_details = ($data[8] != '' ? $data[8] : null);
		$periode->titre = ($data[9] != '' ? substr($data[9],0, $periode->getFieldSize('titre')) : null);
		$periode->notes = ($data[10] != '' ? substr($data[10],0, $periode->getFieldSize('notes')) : null);
		$periode->lien = ($data[11] != '' ? $data[11] : null);
		$periode->statut_tache = ($data[12] != '' ? $data[12] : null);
		$periode->lieu_id = ($data[13] != '' ? $data[13] : null);
		$periode->ressource_id = ($data[14] != '' ? $data[14] : null);
		$periode->livrable = ($data[15] != '' ? $data[15] : 'non');
		$periode->fichiers = ($data[16] != '' ? $data[16] : null);
		if($data[17] != ''){
			$periode->createur_id = ($data[17] != '' ? $data[17] : null);
		} else{
			$periode->createur_id = 'ADM';
		}
		if($data[18] != ''){
			$periode->date_creation = ($data[18] != '' ? $data[18] : null);
		} else{
			$periode->date_creation = date('Y-m-d H:i:s');
		}
		$periode->modifier_id = ($data[19] != '' ? $data[19] : null);
		$periode->date_modif = ($data[20] != '' ? $data[20] : null);
		$periode->custom = ($data[20] != '' ? $data[20] : null);
		if ($type<>"ignore")
		{
			if ($periode->db_save(array(),array(),true))
			{
				$elements_restauration['periode'][]=array("id"=>$periode->periode_id,"type"=>$type,"status"=>"OK","auto_increment"=>"1");
			}else
			{
				$elements_restauration['periode'][]=array("id"=>$periode->periode_id,"type"=>$type,"status"=>"KO","error"=>$periode->fields_on_error,"auto_increment"=>"1");
			}
		}else $elements_restauration['periode'][]=array("id"=>$periode->periode_id,"type"=>$type,"status"=>"OK","auto_increment"=>"1");
	}
	ini_set('auto_detect_line_endings',FALSE);
}

// Restauration des utilisateurs
function restore_users()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	// Fichier users
	$file=$save_dir.'/user.csv';
	if (!file_exists($file)) return true;
	ini_set('auto_detect_line_endings',TRUE);
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 2000, ";") ) !== FALSE ) {
		$user_form = new User(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"user_id" || $data[1]<>"user_groupe_id" || $data[2]<>"nom" || $data[3]<>"login" || $data[4]<>"password" || $data[5]<>"email" || $data[6]<>"visible_planning" || $data[7]<>"couleur" || $data[8]<>"droits" || $data[9]<>"cle" || $data[10]<>"notifications" || $data[11]<>"adresse" || $data[12]<>"telephone" || $data[13]<>"mobile" || $data[14]<>"metier" || $data[15]<>"commentaire" || $data[16]<>"date_dernier_login" || $data[17]<>"preferences" || $data[18]<>"login_actif" || $data[19]<>"google_2fa" || $data[20]<>"date_creation" || $data[21]<>"date_modif")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}        
		if(trim($data[0]) == ''){
			continue;
		}
		// Vérification de l'existence de l'enregistrement
		if ($user_form->db_load(array('user_id', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['user'][]=array("id"=>$user_form->user_id,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($user_form->isSaved())
				{
					if ($user_form->user_id<>"ADM" && $user_form->user_id<>"publicspl" )
					{
						$type="update";
					}else $type="ignore";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}

		// on ajoute ou met à jour les données
		$user_form->user_id = $data[0];
		$user_form->user_groupe_id = ($data[1] != '' ? $data[1] : null);
		$user_form->nom = substr($data[2],0, $user_form->getFieldSize('nom'));
		$user_form->login = ($data[3] != '' ? $data[3] : null);
		$user_form->password = ($data[4] != '' ? $data[4] : null);
		$user_form->email = ($data[5] != '' ? $data[5] : null);
		$user_form->visible_planning = $data[6];
		$user_form->couleur = ($data[7] != '' ? $data[7] : null);
		$user_form->droits = ($data[8] != '' ? $data[8] : null);
		if($data[9] != ''){
			$user_form->cle = $data[9];
		}
		if($data[10] != ''){
			$user_form->notifications = ($data[10] != '' ? $data[10] : null);
		}
		$user_form->adresse = ($data[11] != '' ? $data[11] : null);
		$user_form->telephone = ($data[12] != '' ? $data[12] : null);
		$user_form->mobile = ($data[13] != '' ? $data[13] : null);
		$user_form->metier = ($data[14] != '' ? $data[14] : null);
		$user_form->commentaire = ($data[15] != '' ? $data[15] : null);
		$user_form->date_dernier_login = ($data[16] != '' ? $data[16] : null);
		$user_form->preferences = ($data[17] != '' ? $data[17] : null);
		if($data[18] != ''){
			$user_form->login_actif = ($data[18] != '' ? $data[18] : null);
		}
		if($data[19] != ''){
			$user_form->google_2fa = ($data[19] != '' ? $data[19] : null);
		}
		$user_form->date_creation = ($data[20] != '' ? $data[20] : date('Y-m-d H:i:s'));
		$user_form->date_modif = ($data[21] != '' ? $data[21] : null);
		if ($type<>"ignore")
		{
			if ($user_form->db_save(array(),array(),true))
			{
				$elements_restauration['user'][]=array("id"=>$user_form->user_id,"type"=>$type,"status"=>"OK");
			}else
			{
				$elements_restauration['user'][]=array("id"=>$user_form->user_id,"type"=>$type,"status"=>"KO","error"=>$user_form->fields_on_error);
			}	
		}else $elements_restauration['user'][]=array("id"=>$user_form->user_id,"type"=>$type,"status"=>"OK");
	}
	ini_set('auto_detect_line_endings',FALSE);
}


// Restauration des droits utilisateurs
function restore_user_on_right()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	// Fichier users
	$file=$save_dir.'/right_on_user.csv';
	if (!file_exists($file)) return true;
	ini_set('auto_detect_line_endings',TRUE);
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$user_onright = new Right_on_user(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"right_id" || $data[1]<>"owner_id" || $data[2]<>"allowed_id")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}        
        // Vérification de l'existence de l'enregistrement
		if ($user_onright->db_load(array('right_id', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['user_right'][]=array("id"=>$user_onright->right_id,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($user_onright->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		// on ajoute ou met à jour les données
		$user_onright->right_id = $data[0];
		$user_onright->owner_id = ($data[1] != '' ? $data[1] : null);
		$user_onright->allowed_id = ($data[1] != '' ? $data[1] : null);
		if ($type<>"ignore")
		{
			if ($user_onright->db_save(array(),array(),true))
			{
				$elements_restauration['user_right'][]=array("id"=>$user_onright->right_id,"type"=>$type,"status"=>"OK");
			}else
			{
				$elements_restauration['user_right'][]=array("id"=>$user_onright->right_id,"type"=>$type,"status"=>"KO","error"=>$user_onright->fields_on_error);
			}
		}else $elements_restauration['user_right'][]=array("id"=>$user_onright->right_id,"type"=>$type,"status"=>"OK");
	}
	ini_set('auto_detect_line_endings',FALSE);
}


// Restauration des groupes utilisateurs
function restore_groupes_users()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	$file=$save_dir.'/user_groupe.csv';
	if (!file_exists($file)) return true;
	ini_set('auto_detect_line_endings',TRUE);
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$groupe = new User_groupe(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"user_groupe_id" || $data[1]<>"nom")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}        
		if(trim($data[0]) == ''){
			continue;
		}
        // Vérification de l'existence de l'enregistrement
		if ($groupe->db_load(array('user_groupe_id', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['user'][]=array("id"=>$groupe->user_groupe_id,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($groupe->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		
		// on ajoute ou met à jour les données
		$groupe->user_groupe_id = $data[0];
		$groupe->nom = ($data[1] != '' ? substr($data[1],0, $groupe->getFieldSize('nom')) : null);
		if ($type<>"ignore")
		{
			if ($periode->db_save(array(),array(),true))
			{
				$elements_restauration['user_groupe'][]=array("id"=>$groupe->user_groupe_id,"type"=>$type,"status"=>"OK");
			}else
			{
				$elements_restauration['user_groupe'][]=array("id"=>$groupe->user_groupe_id,"type"=>$type,"status"=>"KO","error"=>$groupe->fields_on_error);
			}
		}else $elements_restauration['user_groupe'][]=array("id"=>$groupe->user_groupe_id,"type"=>$type,"status"=>"OK");
	}
	ini_set('auto_detect_line_endings',FALSE);
}

// Restauration des lieux
function restore_lieux()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	$file=$save_dir.'/lieu.csv';
	if (!file_exists($file)) return true;
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$lieu = new Lieu(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"lieu_id" || $data[1]<>"nom" || $data[2]<>"commentaire" || $data[3]<>"exclusif")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}        
		if(trim($data[0]) == ''){
			continue;
		}
        // Vérification de l'existence de l'enregistrement
		if ($lieu->db_load(array('lieu_id', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['lieu'][]=array("id"=>$lieu->lieu_id,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($lieu->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		
		// on ajoute ou met à jour les données
		$lieu->lieu_id = $data[0];
		$lieu->nom = ($data[1] != '' ? substr($data[1],0, $lieu->getFieldSize('nom')) : null);
		$lieu->commentaire = ($data[2] != '' ? substr($data[2],0, $lieu->getFieldSize('commentaire')) : null);
		$lieu->exclusif = ($data[3] != '' ? $data[3] : null);
		if ($type<>"ignore")
		{
			if ($lieu->db_save())
			{
				$elements_restauration['lieu'][]=array("id"=>$lieu->lieu_id,"type"=>$type,"status"=>"OK");
			}else
			{
				$elements_restauration['lieu'][]=array("id"=>$lieu->lieu_id,"type"=>$type,"status"=>"KO","error"=>$lieu->fields_on_error);
			}
		}else $elements_restauration['lieu'][]=array("id"=>$lieu->lieu_id,"type"=>$type,"status"=>"OK");
	}
	ini_set('auto_detect_line_endings',FALSE);
}

// Restauration des ressources
function restore_ressources()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	$file=$save_dir.'/ressource.csv';
	if (!file_exists($file)) return true;
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$ressource = new Ressource(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"ressource_id" || $data[1]<>"nom" || $data[2]<>"commentaire" || $data[3]<>"exclusif")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}        
		if(trim($data[0]) == ''){
			continue;
		}
        // Vérification de l'existence de l'enregistrement
		if ($ressource->db_load(array('ressource_id', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['ressource'][]=array("id"=>$ressource->ressource_id,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($ressource->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		
		// on ajoute ou met à jour les données
		$ressource->ressource_id = $data[0];
		$ressource->nom = ($data[1] != '' ? substr($data[1],0, $ressource->getFieldSize('nom')) : null);
		$ressource->commentaire = ($data[2] != '' ? substr($data[2],0, $ressource->getFieldSize('commentaire')) : null);
		$ressource->exclusif = ($data[3] != '' ? $data[3] : null);
		if ($type<>"ignore")
		{
			if ($ressource->db_save())
			{
				$elements_restauration['ressource'][]=array("id"=>$ressource->ressource_id,"type"=>$type,"status"=>"OK");
			}else
			{
				$elements_restauration['ressource'][]=array("id"=>$ressource->ressource_id,"type"=>$type,"status"=>"KO","error"=>$ressource->fields_on_error);
			}
		}else $elements_restauration['ressource'][]=array("id"=>$ressource->ressource_id,"type"=>$type,"status"=>"OK");
	}
	ini_set('auto_detect_line_endings',FALSE);
}

// Restauration des status
function restore_status()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	$file=$save_dir.'/status.csv';
	if (!file_exists($file)) return true;
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$status = new Status(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"status_id" || $data[1]<>"nom" || $data[2]<>"commentaire" || $data[3]<>"affichage" || $data[4]<>"barre" || $data[5]<>"gras" || $data[6]<>"italique" || $data[7]<>"souligne" || $data[8]<>"affichage_filtre" || $data[9]<>"defaut" || $data[10]<>"affichage_liste" || $data[11]<>"pourcentage" || $data[12]<>"couleur" || $data[13]<>"priorite")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}     
		if(trim($data[0]) == ''){
			continue;
		}
		// Vérification de l'existence de l'enregistrement
		if ($status->db_load(array('status_id', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['status'][]=array("id"=>$status->status_id,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($status->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		
		// on ajoute ou met à jour les données
		$status->status_id = $data[0];
		$status->nom = ($data[1] != '' ? substr($data[1],0, $status->getFieldSize('nom')) : null);
		$status->commentaire = ($data[2] != '' ? $data[2] : null);
		$status->affichage = ($data[3] != '' ? $data[3] : null);
		$status->barre = ($data[4] != '' ? $data[4] : null);
		$status->gras = ($data[5] != '' ? $data[5] : null);
		$status->italique = ($data[6] != '' ? $data[6] : null);
		$status->souligne = ($data[7] != '' ? $data[7] : null);
		$status->defaut = ($data[8] != '' ? $data[8] : null);
		$status->affichage_liste = ($data[9] != '' ? $data[9] : null);
		$status->pourcentage = ($data[10] != '' ? $data[10] : null);
		$status->couleur = ($data[11] != '' ? $data[11] : null);
		$status->priorite = ($data[12] != '' ? $data[12] : null);

		if ($type<>"ignore")
		{
			if ($status->db_save())
			{
				$elements_restauration['status'][]=array("id"=>$status->status_id,"type"=>$type,"status"=>"OK");
			}else
			{
				$elements_restauration['status'][]=array("id"=>$status->status_id,"type"=>$type,"status"=>"KO","error"=>$status->fields_on_error);
			}
		}else $elements_restauration['status'][]=array("id"=>$status->status_id,"type"=>$type,"status"=>"OK");
	}
	ini_set('auto_detect_line_endings',FALSE);
}


// Restauration des feries
function restore_feries()
{
	global $save_dir,$type_restauration,$elements_restauration,$smarty;
	$file=$save_dir.'/feries.csv';
	if (!file_exists($file)) return true;
	$entete=true;
	$type="add";
	$handle = fopen($file,'r');
	while ( ($data = fgetcsv($handle, 1000, ";") ) !== FALSE ) {
		$feries = new Ferie(); 
		// Contrôle de l'entête du fichier
		if ($entete) {
			$entete=false;
			if ($data[0]<>"date_ferie" || $data[1]<>"libelle" || $data[2]<>"couleur")
			{
				$msg=preg_replace('/filename/',$file,$smarty->getConfigVars('upload_fichier_mauvais_format_fichier'));
				echo $msg;
				exit;
			}
			continue;
		}     
		if(trim($data[0]) == ''){
			continue;
		}
        // Vérification de l'existence de l'enregistrement
		if ($feries->db_load(array('date_ferie', '=', $data[0]))) {
			// pas d'écrasement, on ignore l'enregistrement
			if ($type_restauration==1)
			{
				$elements_restauration['feries'][]=array("id"=>$feries->date_ferie,"type"=>"ignore","status"=>"OK");
				continue;
			}else
			{
				if ($feries->isSaved())
				{
					$type="update";
				}else $type="add";
			};
		}else
		{
			$type="add";
		}
		
		// on ajoute ou met à jour les données
		$feries->date_ferie = $data[0];
		$feries->libelle = ($data[1] != '' ? $data[1] : null);
		$feries->couleur = ($data[2] != '' ? $data[2] : null);
		if ($type<>"ignore")
		{
			if ($feries->db_save())
			{
				$elements_restauration['feries'][]=array("id"=>$feries->date_ferie,"type"=>$type,"status"=>"OK");
			}else
			{
				$elements_restauration['feries'][]=array("id"=>$feries->date_ferie,"type"=>$type,"status"=>"KO","error"=>$feries->fields_on_error);
			}
		}else $elements_restauration['feries'][]=array("id"=>$feries->date_ferie,"type"=>$type,"status"=>"OK");
	}
	ini_set('auto_detect_line_endings',FALSE);
}

?>