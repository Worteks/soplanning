<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

$html = '';
$js = '';

$joursFeries = getJoursFeries();

// PARAMÈTRES ////////////////////////////////
$dateDebut = initDateTime($_SESSION['date_debut_affiche']);
$dateFin = initDateTime($_SESSION['date_fin_affiche']);

$nbLignes = $_SESSION['nb_lignes'];
$pageLignes = $_SESSION['page_lignes'];

$masquerLigneVide = $_SESSION['masquerLigneVide'];

$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);

// FIN PARAMÈTRES ////////////////////////////////

$now = new DateTime();

$headerNomJours = '';
$tmpDate = clone $dateDebut;

$texte = '"' . $smarty->getConfigVars('date') . '";"' . $smarty->getConfigVars('tab_duree') . '";"' . $smarty->getConfigVars('creneau_horaire') . '";"' . $smarty->getConfigVars('winPeriode_user') . '";"' . $smarty->getConfigVars('winPeriode_projet') . '";"' . $smarty->getConfigVars('winPeriode_titre') . '";"' . $smarty->getConfigVars('winPeriode_lieu') . '";"' . $smarty->getConfigVars('winPeriode_ressource') . '";"' . $smarty->getConfigVars('winPeriode_statut') . '";"' . $smarty->getConfigVars('winPeriode_livrable') . '";"' . $smarty->getConfigVars('winPeriode_lien') . '";"' . $smarty->getConfigVars('winPeriode_custom') . '"' . "\r\n";

// on charge les jours occupés pour cette periode
$periodes = new GCollection('Periode');
	$sql = "SELECT planning_periode.*, planning_projet.nom AS nom_projet, pu.nom AS nom_personne, planning_lieu.nom AS nom_lieu, planning_status. nom AS nom_statut, planning_groupe.nom AS nom_groupe, planning_ressource.nom AS nom_ressource
			FROM planning_periode
			INNER JOIN planning_projet	ON planning_periode.projet_id = planning_projet.projet_id
			LEFT JOIN planning_lieu ON planning_lieu.lieu_id = planning_periode.lieu_id
			LEFT JOIN planning_ressource ON planning_ressource.ressource_id = planning_periode.ressource_id
			LEFT JOIN planning_user AS pu ON pu.user_id = planning_periode.user_id
			LEFT JOIN planning_status ON planning_status.status_id = planning_periode.statut_tache
			LEFT JOIN planning_groupe ON planning_groupe.groupe_id = planning_projet.groupe_id
			";
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'équipe de ce user
	$sql .= " INNER JOIN planning_user AS pu ON planning_periode.user_id = pu.user_id ";
}
$sql .= " WHERE (
				(planning_periode.date_debut <= '" . $dateDebut->format('Y-m-d') . "' AND planning_periode.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
					OR
				(planning_periode.date_debut <= '" . $dateFin->format('Y-m-d') . "' AND planning_periode.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
					)";
if(count($_SESSION['filtreGroupeProjet']) > 0) {
	$sql.= " AND planning_periode.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
}
if(count($_SESSION['filtreUser']) > 0) {
	$sql.= " AND planning_periode.user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
}
if($user->checkDroit('tasks_view_own_projects')) {
	$sql .= " AND planning_periode.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
}
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'équipe de ce user
	$sql .= " AND pu.user_groupe_id = " . val2sql($user->user_groupe_id);
}
if ($user->checkDroit('tasks_view_only_own')) {
	$sql .= " AND planning_periode.user_id = " . val2sql($user->user_id);
}
$sql.= " ORDER BY planning_periode.date_debut";
$periodes->db_loadSQL($sql);

//echo $sql . '<br>';
//$joursOccupes = array();
// pour chaque période de cette ligne, on remplie le tableau des jours occupés
while ($periode = $periodes->fetch()) {
	if (!is_null($periode->date_fin)) {
		$dateDebut_projet = new DateTime();
		$dateDebut_projet->setDate(substr($periode->date_debut,0,4), substr($periode->date_debut,5,2), substr($periode->date_debut,8,2));
		$dateFin_projet = new DateTime();
		$tmpDate = clone $dateDebut_projet;
		$dateFin_projet->setDate(substr($periode->date_fin,0,4), substr($periode->date_fin,5,2), substr($periode->date_fin,8,2));

		
		while ($tmpDate <= $dateFin_projet) {
			if (in_array($tmpDate->format('w'), explode(',', CONFIG_DAYS_INCLUDED)) && !array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
				$duree = CONFIG_DURATION_DAY;
				$texte .= '"' . $tmpDate->format('Y-m-d') . '";"';
				$texte .= usertime2sqltime($duree) . '";"';
				$heures = $periode->getHeureDebutFin();
				if(!is_null($heures)){
					$texte .= $heures['duree_details_heure_debut'] . ' - ' . $heures['duree_details_heure_fin'] . '";"';
				} else{
					$texte .= $smarty->getConfigVars('journee') . '";"';
				}
				$texte .=  str_replace('"', "'", $periode->nom_personne) . '";"';
				$texte .= str_replace('"', "'", $periode->nom_projet) . '";"';
				$texte .= str_replace('"', "'", $periode->titre) . '";"';
				$texte .= str_replace('"', "'", $periode->nom_lieu) . '";"';
				$texte .= str_replace('"', "'", $periode->nom_ressource) . '";"';
				$texte .= str_replace('"', "'", $periode->nom_statut) . '";"';
				$texte .= str_replace('"', "'", $periode->livrable) . '";"';
				$texte .= str_replace('"', "'", $periode->lien) . '";"';
				$texte .= str_replace('"', "'", $periode->custom) . '"';
				$texte .= "\r\n";
			}
			$tmpDate->modify('+1 day');
		}
	} else{
		$texte .= '"' . $periode->date_debut . '";"';
		$duree = $periode->duree;
		$texte .= usertime2sqltime($duree) . '";"';
		$heures = $periode->getHeureDebutFin();
		if(!is_null($heures)){
			$texte .= $heures['duree_details_heure_debut'] . ' - ' . $heures['duree_details_heure_fin'] . '";"';
		} elseif($periode->duree_details == 'AM') {
			$texte .=  $smarty->getConfigVars('tab_matin') .'";"';
		} elseif($periode->duree_details == 'PM') {
			$texte .=  $smarty->getConfigVars('tab_apresmidi') . '";"';
		} else {
			$texte .= $smarty->getConfigVars('journee') . '";"';
		}
		$texte .=  str_replace('"', "'", $periode->nom_personne) . '";"';
		$texte .= str_replace('"', "'", $periode->nom_projet) . '";"';
		$texte .= str_replace('"', "'", $periode->titre) . '";"';
		$texte .= str_replace('"', "'", $periode->nom_lieu) . '";"';
		$texte .= str_replace('"', "'", $periode->nom_ressource) . '";"';
		$texte .= str_replace('"', "'", $periode->nom_statut) . '";"';
		$texte .= str_replace('"', "'", $periode->livrable) . '";"';
		$texte .= str_replace('"', "'", $periode->lien) . '";"';
		$texte .= str_replace('"', "'", $periode->custom) . '"';
		$texte .= "\r\n";
	}
}

$nomFichier = 'export_soplanning_' . date('Y-m-d-H-i') . '.csv';

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='. $nomFichier);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . strlen($texte));
echo $texte;

?>
