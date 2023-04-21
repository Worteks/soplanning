<?php
require 'base.inc';
require BASE . '/../config.inc';
require BASE . '/../includes/header.inc';

$type=$_POST['type'];;
// securise link_id
$linkid=preg_replace( '/[^a-z0-9]+/', '0', strtolower($_POST['linkid']));
$upload_dir = UPLOAD_DIR."$linkid/"; // upload directory 

// Si on fait un upload de fichiers
if ($type=='upload')
{
	// Pour tous les fichiers, on tente de les uploader
	for($i=0; $i<count($_FILES); $i++){	
	$filename = replaceAccents(utf8_decode($_FILES["fichier-$i"]['name']));
	$tmp_dir = $_FILES["fichier-$i"]['tmp_name'];
	$fileSize = $_FILES["fichier-$i"]['size'];
	     
	// Verification du répertoire
	if(!file_exists(UPLOAD_DIR) || !is__writable(UPLOAD_DIR)) {
		$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_ecriture_repertoire'));
		echo $msg;
		exit;
	}else
	{
		// Création du répertoire si nécessaire
		@mkdir($upload_dir);
		
		// Si il existe déjà, on ne l'écrase pas
		if (file_exists($upload_dir.$filename))
		{
			$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_existe_deja'));
			echo $msg;
		}else
		{
			// vérification de la taille du fichier
			if ($fileSize > MAX_SIZE_UPLOAD)
			{
				$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_taille'));
				echo $msg;
				exit;
			}
			
			// chargement du fichier
			if(!(move_uploaded_file($tmp_dir,$upload_dir.$filename)))
			{
				$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_chargement'));
				echo $msg;
				exit;
			}else
			{
				if (!file_exists($upload_dir.$filename))
				{
					$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_chargement'));
					echo $msg;
					exit;
				}else
				{
					$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_chargement_ok'));
					echo $msg;
				}
			}
		}
	}
	}
}

// Si on fait un delete de fichiers
if ($type=='delete')
{
		$filename=utf8_decode($_POST['fichier_to_delete']);
		if (file_exists($upload_dir.$filename))
		{
			if (@unlink($upload_dir.$filename))
			{
				// Suppression du repertoire si le dossier n'est pas vide
				if(!glob($upload_dir."*"))
				{
					rrmdir($upload_dir);
				}
				$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_delete_ok'));
				echo $msg;
			}else
			{
				$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_delete_ko'));
				echo $msg;
				exit;
			}
		}
}

// Si on fait un delete de tous les fichiers (annulation d'une nouvelle tâche non enregistrée)
if ($type=='deletenew')
{
	rrmdir($upload_dir);
}

// update de la tâche si periode_id
if (!empty($_POST['periodeid']))
{
		// Mise à jour de la tâche actuelle
		$periode = new Periode();
		$periode->db_load(array('periode_id', '=', $_POST['periodeid']));		
		if (!empty($_POST['fichiers']))
		{
			$periode->fichiers=replaceAccents($_POST['fichiers']);
		}else 
		{
			$periode->fichiers = null;
		}
		$periode->db_save();
		
		// Mise à jour de toutes les tâches liées
		$sql = 'UPDATE planning_periode SET fichiers = "' . $periode->fichiers . '" WHERE link_id = ' . val2sql($linkid);
		db_query($sql);
}	

?>