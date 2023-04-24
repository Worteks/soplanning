<?php
require 'base.inc';
require BASE . '/../config.inc';
require BASE . '/../includes/header.inc';

$filename=$_POST['export_nom_sauvegarde'];
$type="save";
$save_dir = SAVE_DIR."$filename".".tmp";
$zip_file = "$filename".".zip";

// Si on fait une sauvegarde de fichiers
if ($type=='save') {
    @mkdir($save_dir);
    if (isset($_POST['export_configuration']) && $_POST['export_configuration']==1) {
        export_parametres();
        export_status();
        export_feries();
    }
    if (isset($_POST['export_projets']) && $_POST['export_projets']==1) {
        export_projets();
        export_groupes_projets();
    }
    if (isset($_POST['export_taches']) && $_POST['export_taches']==1) {
        export_taches();
    }
    if (isset($_POST['export_users']) && $_POST['export_users']==1) {
        export_users();
        export_right_on_user();
        export_groupes_users();
    }
    if (isset($_POST['export_lieux']) && $_POST['export_lieux']==1) {
        export_lieux();
    }
    if (isset($_POST['export_ressources']) && $_POST['export_ressources']==1) {
        export_ressources();
    }
    export_version();
    zip($zip_file);
}

function export_version()
{
    global $save_dir;
    $file=$save_dir.'/version.info';
    $fp = fopen($file, 'w');
    fwrite($fp, 'version;'.CONFIG_CURRENT_VERSION);
    fclose($fp);
}

function export_parametres()
{
    global $save_dir;
    $file=$save_dir.'/config.csv';
    $config = new GCollection('Config');
    $fields=array('cle','valeur','commentaire');
    $sql="select cle,valeur,commentaire from planning_config";
    $config->db_loadSQL($sql);
    $data=$config->getData();
    write_csv($file, $fields, $data);
}

// Exportation des projets
function export_projets()
{
    global $save_dir;
    $file=$save_dir.'/projet.csv';
    $projets = new GCollection('Projet');
    $fields=array('projet_id','nom','iteration','couleur','charge','livraison','lien','statut','groupe_id','createur_id');
    $sql="select projet_id,nom,iteration,couleur,charge,livraison,lien,statut,groupe_id,createur_id from planning_projet";
    $projets->db_loadSQL($sql);
    $data=$projets->getData();
    write_csv($file, $fields, $data);
}

// Exportation des groupes projets
function export_groupes_projets()
{
    global $save_dir;
    $file=$save_dir.'/groupe.csv';
    $projets = new GCollection('Groupe');
    $fields=array('groupe_id','nom','ordre');
    $sql="select groupe_id,nom,ordre from planning_groupe";
    $projets->db_loadSQL($sql);
    $data=$projets->getData();
    write_csv($file, $fields, $data);
}

// Exportation des tâches
function export_taches()
{
    global $save_dir;
    $file=$save_dir.'/periode.csv';
    $periode = new GCollection('Periode');
    $fields=array('periode_id','parent_id','projet_id','user_id','link_id','date_debut','date_fin','duree','duree_details','titre','notes','lien','statut_tache','lieu_id','ressource_id','livrable','fichiers','createur_id','date_creation','modifier_id','date_modif','custom');
    $sql="select periode_id,parent_id,projet_id,user_id,link_id,date_debut,date_fin,duree,duree_details,titre,notes,lien,statut_tache,lieu_id,ressource_id,livrable,fichiers,createur_id,date_creation,modifier_id,date_modif,custom from planning_periode";
    $periode->db_loadSQL($sql);
    $data=$periode->getData();

    write_csv($file, $fields, $data);
}

// Exportation des utilisateurs
function export_users()
{
    global $save_dir;
    $file=$save_dir.'/user.csv';
    $users = new GCollection('User');
    $fields=array('user_id','user_groupe_id','nom','login','password','email','visible_planning','couleur','droits','cle','notifications','adresse','telephone','mobile','metier','commentaire','date_dernier_login','preferences','login_actif','google_2fa','date_creation','date_modif');
    $sql="select user_id,user_groupe_id,nom,login,password,email,visible_planning,couleur,droits,cle,notifications,adresse,telephone,mobile,metier,commentaire,date_dernier_login,preferences,login_actif,google_2fa,date_creation,date_modif from planning_user";
    $users->db_loadSQL($sql);
    $data=$users->getData();
    write_csv($file, $fields, $data);
}


// Exportation des utilisateurs
function export_right_on_user()
{
    global $save_dir;
    $file=$save_dir.'/right_on_user.csv';
    $droits = new GCollection('Right_on_user');
    $fields=array('right_id','owner_id','allowed_id');
    $sql="select right_id,owner_id,allowed_id from planning_right_on_user";
    $droits->db_loadSQL($sql);
    $data=$droits->getData();
    write_csv($file, $fields, $data);
}

// Exportation des groupes utilisateurs
function export_groupes_users()
{
    global $save_dir;
    $file=$save_dir.'/user_groupe.csv';
    $groupes = new GCollection('User_groupe');
    $fields=array('user_groupe_id','nom');
    $sql="select user_groupe_id,nom from planning_user_groupe";
    $groupes->db_loadSQL($sql);
    $data=$groupes->getData();
    write_csv($file, $fields, $data);
}

// Exportation des lieux
function export_lieux()
{
    global $save_dir;
    $file=$save_dir.'/lieu.csv';
    $lieux = new GCollection('Lieu');
    $fields=array('lieu_id','nom','commentaire','exclusif');
    $sql="select lieu_id,nom,commentaire,exclusif from planning_lieu";
    $lieux->db_loadSQL($sql);
    $data=$lieux->getData();
    write_csv($file, $fields, $data);
}

// Exportation des ressources
function export_ressources()
{
    global $save_dir;
    $file=$save_dir.'/ressource.csv';
    $ressources = new GCollection('Ressource');
    $fields=array('ressource_id','nom','commentaire','exclusif');
    $sql="select ressource_id,nom,commentaire,exclusif from planning_ressource";
    $ressources->db_loadSQL($sql);
    $data=$ressources->getData();
    write_csv($file, $fields, $data);
}

// Exportation des status
function export_status()
{
    global $save_dir;
    $file=$save_dir.'/status.csv';
    $status = new GCollection('Status');
    $fields=array('status_id','nom','commentaire','affichage','barre','gras','italique','souligne','defaut','pourcentage','couleur','priorite');
    $sql="select status_id,nom,commentaire,affichage,barre,gras,italique,souligne,defaut,pourcentage,couleur,priorite from planning_status";
    $status->db_loadSQL($sql);
    $data=$status->getData();
    write_csv($file, $fields, $data);
}

// Exportation des fériés
function export_feries()
{
    global $save_dir;
    $file=$save_dir.'/feries.csv';
    $feries = new GCollection('Ferie');
    $fields=array('date_ferie','libelle','couleur');
    $sql="select date_ferie,libelle,couleur from planning_ferie";
    $feries->db_loadSQL($sql);
    $data=$feries->getData();
    write_csv($file, $fields, $data);
}

// Ecriture du fichier .csv
function write_csv($file, $fields, $data)
{
    $fp = fopen($file, 'w');
    fputcsv($fp, $fields, ";");
    foreach ($data as $fields) {
        @array_pop($fields);
        @array_pop($fields);
        @fputcsv($fp, $fields, ";");
    }
    fclose($fp);
}

function encodeFunc($value)
{
    return "\"$value\"";
}

// Compression du fichier
function zip($fichier)
{
    global $save_dir;
    $fichiers=array();
    if ($handle = opendir($save_dir)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $fichiers[]=$entry;
            }
        }
        closedir($handle);
    }
    
    $zip = new ZipArchive();
    if ($zip->open(SAVE_DIR."/$fichier", ZipArchive::CREATE) === true) {
        // Ajout de tous les fichiers
        foreach ($fichiers as $f) {
            $zip->addFile($save_dir."/$f", "$f");
        }
        $zip->close();
    } else {
        echo 'Erreur de compression<br/>';
    }
      
    // http headers for zip downloads
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"".$fichier."\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize(SAVE_DIR."/$fichier"));
    ob_end_flush();
    @readfile(SAVE_DIR."/$fichier");

    unlink(SAVE_DIR."/$fichier");
}
