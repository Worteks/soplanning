<?php
require 'base.inc';
require BASE . '/../config.inc';
require BASE . '/../includes/header.inc';

$type=$_POST['type'];
$type_restauration=$_POST['type_restauration'];
$type_fichier_import_seul=$_POST['type_fichier_import'];
$upload_dir = SAVE_DIR; // upload directory 
// Si on fait un upload de fichiers
if ($type=='upload')
{
	// Pour tous les fichiers, on tente de les uploader
	for($i=0; $i<count($_FILES); $i++){	
	$filename = replaceAccents(utf8_decode($_FILES["fichier-$i"]['name']));
	$tmp_dir = $_FILES["fichier-$i"]['tmp_name'];
	$fileSize = $_FILES["fichier-$i"]['size'];
	 
	$dest_dir=$upload_dir.$filename.".tmp";
	
	// Vidage du contenu d'uploaddir sans suppresion du répertoire
	rrmdir($upload_dir,false);

	// Verification du répertoire
	if(!file_exists(SAVE_DIR) || !is__writable(SAVE_DIR)) {
		$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_ecriture_repertoire'));
		echo $msg;
		exit;
	}else
	{
		// Si le fichier existe, on l'efface
		if (file_exists($upload_dir.$filename))
		{
			@unlink($upload_dir.$filename);
		}
		// Vérification de la taille du fichier
		if ($fileSize > MAX_SIZE_UPLOAD)
		{
			$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_taille'));
			echo $msg;
			exit;
		}
		
		// Chargement du fichier
		if(!(move_uploaded_file($tmp_dir,$upload_dir.$filename)))
		{
			$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_chargement'));
			echo $msg;
			exit;
		}else
		{
			// Vérification du bon chargement du fichier
			if (!file_exists($upload_dir.$filename))
			{
				$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_chargement'));
				echo $msg;
				exit;
			}else
			{
				@mkdir($dest_dir);
				$info = pathinfo($upload_dir.$filename);

				// Test si c'est une archive on l'extrait
				if ($type_restauration=="sauvegarde" && $info["extension"] == "zip") 
				{ 
					// Extraction de l'archive
					$zip = new ZipArchive(); 
					if($zip->open($upload_dir.$filename) === true)
					{
						$zip->extractTo($dest_dir);
    					$zip->close();
  					} else {
						@unlink($upload_dir.$filename);
						$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('erreur_extraction_sauvegarde'));
						echo $msg;
						exit;
					exit;
					}
				    // Vérification de la compatibilité des versions
					$file=$dest_dir.'/version.info';
					if (file_exists($file))
					{
						$contenu = file_get_contents($file, true);
						$found_version=explode(";",$contenu);
						if ($found_version[1] <> CONFIG_CURRENT_VERSION)
						{
							@unlink($upload_dir.$filename);
							@rrmdir($dest_dir);
							$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_version'));
							echo $msg;
							exit;
						}
					}
				}

				// Test si c'est un fichier .csv
				if ($type_restauration=="import" && $info["extension"] == "csv") 
				{
					if (!in_array($type_fichier_import_seul, array('config','feries','projet_groupe','periode','projet','ressource','lieu','right_on_user','status','user','user_groupe')))
					{
						// Suppression des fichiers
						@unlink($upload_dir.$filename);
						@rrmdir($dest_dir);
						$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_non_autorise'));
						echo $msg;
						exit;
					}
					
					if(!copy($upload_dir.$filename, $dest_dir.'/'.$filename))
					{
						// Suppression des fichiers
						@unlink($upload_dir.$filename);
						@rrmdir($dest_dir);
						$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_fichier_erreur_chargement'));
						echo $msg;
						exit;
					};

					// Vérification du nom des fichiers
					$erreur_upload_fichier_seul=false;
					switch ($type_fichier_import_seul) {
						case 'config':
							$fichier_attendu='config.csv';
							break;
						case 'feries':
							$fichier_attendu='feries.csv';
							break;
						case 'projet_groupe':
							$fichier_attendu='groupe.csv';
							break;	
						case 'periode':
							$fichier_attendu='periode.csv';
							break;
						case 'projet':
							$fichier_attendu='projet.csv';
							break;
						case 'ressource':
							$fichier_attendu='ressource.csv';
							break;
						case 'lieu':
							$fichier_attendu='lieu.csv';
							break;
						case 'right_on_user':
							$fichier_attendu='right_on_user.csv';
							break;
						case 'status':
							$fichier_attendu='status.csv';
							break;
						case 'user':
							$fichier_attendu='user.csv';
							break;
						case 'user_groupe':
							$fichier_attendu='user_groupe.csv';
							break;
					}
					if ($filename<>$fichier_attendu) 
					{
						$msg=preg_replace('/attendu/',$fichier_attendu,$smarty->getConfigVars('upload_fichier_erreur_nom_fichier'));
						$msg=preg_replace('/filename/',$filename,$msg);
						echo $msg;
						exit;
					}
		
				}
					
				// Traitement des fichiers importés
				$fichiers=array();$nb_fichiers=0;
				if ($handle = opendir($dest_dir)) {
					while (false !== ($entry = readdir($handle))) {
						if ($entry != "." && $entry != ".." && $entry != "version.info") {
							$info_fichier = pathinfo($entry);
							if (in_array($info_fichier["filename"], array('config','feries','audit','groupe','periode','projet','ressource','lieu','right_on_user','status','user','user_groupe')))
							{
								$contenu_fichier = file_get_contents($dest_dir.'/'.$entry);
								$nb_lignes=substr_count($contenu_fichier, "\n");
								$fichiers[]=array('file'=>$entry,'nb'=>($nb_lignes-1));
								$nb_fichiers++;	
							}
					}
				}
				closedir($handle);
				}

				// Si aucun fichier valide
				if ($nb_fichiers==0)
				{
					@unlink($upload_dir.$filename);
					@rrmdir($dest_dir);
					$msg=preg_replace('/filename/',$filename,$smarty->getConfigVars('upload_aucun_fichier_valide'));
					echo $msg;
					exit;
				}

				// Suppression des fichiers
				@unlink($upload_dir.$filename);
				//@rrmdir($dest_dir);

				echo json_encode($fichiers);
				}
			}
	}
	}
}
?>