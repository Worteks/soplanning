<?php
// Include
require('./base.inc');
require(BASE . '/../config.inc');
$smarty = new MySmarty();
require(BASE . '/../includes/header.inc');
require(BASE . '/planning_param.php');

$planning=array();
$planning['lignes']=array();
$planning['colonnes']=array();
$planning['users']=array();
$planning['projets']=array();
$planning['periodes']=array();
$planning['lieux']=array();
$planning['ressources']=array();

//////////////////////////
// RECHERCHE DES TRANCHES HORAIRES POSSIBLES
//////////////////////////
$planning['heures']=array();
$tabTranchesHoraires = explode(',', CONFIG_HOURS_DISPLAYED);
$derniereTranche=end($tabTranchesHoraires)+1;
$i=0;
foreach ($tabTranchesHoraires as $trancheHeureCourante) {
		$i++;
		if ($trancheHeureCourante<$derniereTranche)
		{
			$trancheFin = $trancheHeureCourante + 1;
			if($trancheFin == 24) {
				$trancheFin = 0;
			}
			// Heure pleine
			$heure=sprintf("%'.02d:00", $trancheHeureCourante);
			$planning['heures'][]=$heure;
			if ($base_colonne<>"heures")
			{
				// Demie heure
				if (($trancheHeureCourante+0.5)<$derniereTranche)
				{
					$heure=sprintf("%'.02d:30", $trancheHeureCourante);
					$planning['heures'][]=$heure;		
				}
			}
		}
	}
$maxheures=$i;

//////////////////////////
// RECHERCHE DES USERS
//////////////////////////
$realUsers = new GCollection('User');

$sql = "SELECT pu.*, pug.nom as team_nom
		FROM planning_user pu
		LEFT JOIN planning_user_groupe pug ON pu.user_groupe_id = pug.user_groupe_id";
if($user->checkDroit('tasks_view_specific_users')) {
	$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = pu.user_id AND rou.owner_id = " . val2sql($user->user_id);
}
if(is_array($_SESSION['filtreUser']) && count($_SESSION['filtreUser']) > 0) {
	$sql .= " WHERE pu.user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
	$sql .= " AND pu.visible_planning='oui' ";
}else{
	$sql .= " WHERE pu.visible_planning='oui'";
}
// Si filtre sur son équipe
if($user->checkDroit('droits_tasks_view_team_users')) {
	$sql.= " AND pu.user_groupe_id = '".$_SESSION['user_groupe_id']."'";
}
if (isset($_SESSION['triPlanningUser']))
{
$sql .= " ORDER BY " . $_SESSION['triPlanningUser'];
}
$realUsers->db_loadSQL($sql);
$nbRealUsers = $realUsers->getCount();
// FIN RECHERCHE DES USERS

//////////////////////////
// RECHERCHE DES PERIODES
//////////////////////////
// on charge les jours occupés pour toutes les lignes
$periodes = new GCollection('Periode');
$sql = "SELECT planning_periode.*,planning_projet.statut, planning_status.nom as status_nom,  planning_status.barre as statut_barre,planning_status.gras as statut_gras,planning_status.italique as statut_italique,planning_status.souligne as statut_souligne, planning_status.couleur as statut_couleur,planning_status.pourcentage as statut_pourcentage, pu.nom as user_nom, pu.couleur as user_couleur,
		planning_projet.nom as projet_nom, planning_projet.couleur as projet_couleur, pg.nom AS groupe_nom, pu.*,pug.nom AS team_nom,
		pl.nom as lieu_nom, pr.nom as ressource_nom, planning_projet.charge as charge, planning_projet.createur_id AS projet_createur_id,
		puc.nom AS nom_createur, pum.nom AS nom_modifier, planning_periode.date_creation, planning_periode.date_modif,
		CASE 
		   WHEN planning_periode.duree_details = 'AM' THEN '08:00:00;08:01:00' 
		   WHEN planning_periode.duree_details = 'PM' THEN '14:00:00;14:01:00' 
		   WHEN planning_periode.duree_details = 'duree' THEN NULL    
		   ELSE planning_periode.duree_details 
		END AS tri_heures_taches
		FROM planning_periode
		INNER JOIN planning_projet on planning_projet.projet_id = planning_periode.projet_id
		INNER JOIN planning_status on planning_status.status_id = planning_periode.statut_tache
		INNER JOIN planning_user as pu on planning_periode.user_id = pu.user_id
		LEFT JOIN planning_user as puc on planning_periode.createur_id = puc.user_id
		LEFT JOIN planning_user as pum on planning_periode.modifier_id = pum.user_id
		LEFT JOIN planning_user_groupe as pug on pu.user_groupe_id = pug.user_groupe_id
		LEFT JOIN planning_groupe as pg on planning_projet.groupe_id = pg.groupe_id
		LEFT JOIN planning_lieu as pl on planning_periode.lieu_id = pl.lieu_id
		LEFT JOIN planning_ressource as pr on planning_periode.ressource_id = pr.ressource_id";
// Si filtre sur user spécifique
if($user->checkDroit('tasks_view_specific_users')) {
	$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = planning_periode.user_id AND rou.owner_id = " . val2sql($user->user_id);
}
$sql .= "	WHERE planning_periode.projet_id = planning_projet.projet_id and (
			(planning_periode.date_debut <= '" . $dateDebut->format('Y-m-d') . "' AND planning_periode.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
			OR
			(planning_periode.date_debut <= '" . $dateFin->format('Y-m-d') . "' AND planning_periode.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
			)";
// Si filtre sur user
if(is_array($_SESSION['filtreUser']) && count($_SESSION['filtreUser']) > 0) {
	$sql.= " AND planning_periode.user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
}
// Si filtre sur son équipe
if($user->checkDroit('droits_tasks_view_team_users')) {
	$sql.= " AND pu.user_groupe_id = '".$_SESSION['user_groupe_id']."'";
}
// Si filtre sur groupe projet
if(count($_SESSION['filtreGroupeProjet']) > 0) {
	$sql.= " AND planning_periode.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
}
// Si filtre sur groupe lieu
if(count($_SESSION['filtreGroupeLieu']) > 0) {
	$sql.= " AND planning_periode.lieu_id IN ('" . implode("','", $_SESSION['filtreGroupeLieu']) . "')";
}
// Si filtre sur ressource
if(count($_SESSION['filtreGroupeRessource']) > 0) {
	$sql.= " AND planning_periode.ressource_id IN ('" . implode("','", $_SESSION['filtreGroupeRessource']) . "')";
}
// Si filtre sur statut de tache
if(count($_SESSION['filtreStatutTache']) > 0) {
	$sql.= " AND planning_periode.statut_tache IN ('" . implode("','", $_SESSION['filtreStatutTache']) . "')";
}
// Si filtre sur statut de projet
if(count($_SESSION['filtreStatutProjet']) > 0) {
	$sql.= " AND planning_projet.statut IN ('" . implode("','", $_SESSION['filtreStatutProjet']) . "')";
}
// Si filtre sur ses projets seulement
if($user->checkDroit('tasks_view_own_projects')) {
	$sql .= " AND planning_periode.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
}
// Si filtre sur projets de l'équipe
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	$sql .= " AND planning_periode.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
}
// Si filtre sur ses tâches
if ($user->checkDroit('tasks_view_only_own')) {
	$sql .= " AND planning_periode.user_id = " . val2sql($user->user_id);
}
// si filtre sur texte
if($_SESSION['filtreTexte'] != "") {
	$sql.= " AND (convert(planning_periode.notes using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR convert(planning_periode.lien using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') ." OR convert(planning_periode.titre using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR convert(planning_periode.custom using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR planning_periode.projet_id LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR convert(planning_periode.user_id using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " )";
}
$periodes->db_loadSQL($sql);
//echo $sql;die;
$nbLignesTotal = $periodes->getCount();

// on trie par la date de début
if ($base_ligne == "heures") {
	$sql .=" ORDER by date_debut,tri_heures_taches";
} else {
	$sql .=" ORDER by date_debut,duree_details asc";
}

$periodes->db_loadSQL($sql);
//echo $sql;
// FIN RECHERCHE DES PERIODES EN COURS

//////////////////////////
// LIGNES DU PLANNING
//////////////////////////

// Lignes users
if ($base_ligne == 'users')
{
	// liste des users à partir de tous les utilisateurs
	while ($u = $realUsers->fetch())
	{
		$infosUser = $u->getSmartyData();
		if ($user->checkDroit('users_manage_all'))
		{
			$url="xajax_modifUser('".urlencode($infosUser['user_id'])."')";
		}else $url="";
		$planning['lignes'][$infosUser['user_id']]=array('id'=>$infosUser['user_id'],'nom'=>$infosUser['nom'],'couleur'=>$infosUser['couleur'],'team_nom'=>$infosUser['team_nom'],'team_id'=>$infosUser['user_groupe_id'],'url_modif'=>$url);
	}
}

// Lignes projets
if ($base_ligne == 'projets') 
{
	// Si filtre sur groupe projet on supprime les projets non nécessaires
	if(count($_SESSION['filtreGroupeProjet']) > 0) {
		$listeProjets_temp=$projetsFiltre->getSmartyData();
		foreach ($listeProjets_temp as $p)
		{
			if (in_array($p['projet_id'],$_SESSION['filtreGroupeProjet']))
			{
				$listeProjets[]=$p;
			}
		}
	}else 
	{
		$listeProjets=$projetsFiltre->getSmartyData();
	}
	// liste des projets à partir des périodes remontées
	foreach ($listeProjets as $infosJour) {
		if ($user->checkDroit('projects_manage_all'))
		{
			$url="xajax_modifProjet('".urlencode($infosJour['projet_id'])."')";
		}elseif ($user->checkDroit('projects_manage_own') and ($user->user_id==$infosJour['projet_createur_id']))
		{
			$url="xajax_modifProjet('".urlencode($infosJour['projet_id'])."')";
		}else $url="";
		$planning['lignes'][$infosJour['projet_id']]=array('id'=>$infosJour['projet_id'],'nom'=>$infosJour['projet_nom'],'couleur'=>$infosJour['projet_couleur'],'groupe_nom'=>$infosJour['groupe_nom'],'url_modif'=>$url);
	}
	if ($_SESSION['triPlanning']=="nom asc") array_sort_by_columns($planning['lignes'],"nom",SORT_ASC);
	if ($_SESSION['triPlanning']=="nom desc") array_sort_by_columns($planning['lignes'],"nom",SORT_DESC);	
	if (strpos($_SESSION['triPlanning'],"groupe_nom asc") !== FALSE && strpos($_SESSION['triPlanning'],"nom asc") !== FALSE) $planning['lignes']=array_sort_by_columns($planning['lignes'],"groupe_nom", SORT_ASC, "nom", SORT_ASC);
	if (strpos($_SESSION['triPlanning'],"groupe_nom desc") !== FALSE && strpos($_SESSION['triPlanning'],"nom desc") !==FALSE) $planning['lignes']=array_sort_by_columns($planning['lignes'],"groupe_nom", SORT_DESC, "nom", SORT_DESC);
}

// Ligne lieux
if ($base_ligne == 'lieux') 
{
	// liste des lieux à partir des périodes remontées
	while ($p = $periodes->fetch()) {
		$infosJour = $p->getSmartyData();
		// On force les valeurs nulles
		if (empty($infosJour['lieu_nom'])) $infosJour['lieu_nom']=$smarty->getConfigVars('sans_lieux');
		$planning['lignes'][$infosJour['lieu_id']]=array('id'=>$infosJour['lieu_id'],'nom'=>$infosJour['lieu_nom'],'couleur'=>null,'url_modif'=>"xajax_modifLieu('".urlencode($infosJour['lieu_id'])."')");
	}
	if (strpos($_SESSION['triPlanning'],"nom asc") !== FALSE) array_sort_by_column($planning['lignes'],"nom",SORT_ASC);
	if (strpos($_SESSION['triPlanning'],"nom desc") !== FALSE) array_sort_by_column($planning['lignes'],"nom",SORT_DESC);
}

// Ligne ressources
if ($base_ligne == 'ressources')
{	
	// liste des ressources à partir des périodes remontées
	while ($p = $periodes->fetch()) {
		$infosJour = $p->getSmartyData();
		// On force les valeurs nulles
		if (empty($infosJour['ressource_nom'])) $infosJour['ressource_nom']=$smarty->getConfigVars('sans_ressources');
		$planning['lignes'][$infosJour['ressource_id']]=array('id'=>$infosJour['ressource_id'],'nom'=>$infosJour['ressource_nom'],'couleur'=>null,'url_modif'=>"xajax_modifRessource('".urlencode($infosJour['ressource_id'])."')");
	}
	if (strpos($_SESSION['triPlanning'],"nom asc") !== FALSE) array_sort_by_column($planning['lignes'],"nom",SORT_ASC);
	if (strpos($_SESSION['triPlanning'],"nom desc") !== FALSE) array_sort_by_column($planning['lignes'],"nom",SORT_DESC);	
}

// Ligne heures
if ($base_ligne == 'heures') 
{
	foreach ($planning['heures'] as $heure)
	{
		$planning['lignes'][$heure]=array('id'=>$heure,'nom'=>$heure,'couleur'=>null,'url_modif'=>null);
	}
}	

//////////////////////////
// CREATION DU TABLEAU PERIODE
//////////////////////////
$totalHoraireParJour = array();
$totalNbTachesHoraireParJour = array();
$totalNbTachesParJour = array();
$totauxJourUsers = array();

// Parcours de l'ensemble des périodes pour en définir les lignes et les cases remplies
$periodes->db_loadSQL($sql);
while ($p = $periodes->fetch()) {
	$infosJour = $p->getSmartyData();
	$dateDebut_planning = new DateTime();
	$dateDebut_planning->setDate(substr($p->date_debut,0,4), substr($p->date_debut,5,2), substr($p->date_debut,8,2));
	$dateFin_planning = new DateTime();
	$tmpDate = clone $dateDebut_planning;
	if (is_null($p->date_fin)) {
		$dateFin_planning = clone $dateDebut_planning;
	}
	else {
		$dateFin_planning->setDate(substr($p->date_fin,0,4), substr($p->date_fin,5,2), substr($p->date_fin,8,2));
	}
		
	// liste des users du planning
	if (!in_array($infosJour['user_id'],$planning['users']))
	{
		$planning['users'][]=$infosJour['user_id'];
	}
	// liste des projets du planning
	if (!in_array($infosJour['projet_id'],$planning['projets']))
	{
		$planning['projets'][]=$infosJour['projet_id'];
	}
	// liste des lieux du planning
	if (!in_array($infosJour['lieu_id'],$planning['lieux']))
	{
		$planning['lieux'][]=$infosJour['lieu_id'];
	}
	// liste des ressources du planning
	if (!in_array($infosJour['ressource_id'],$planning['ressources']))
	{
		$planning['ressources'][]=$infosJour['ressource_id'];
	}
	// liste des tâches du planning
	if (!in_array($infosJour['periode_id'],$planning['periodes']))
	{
		// Calcul de la durée en heure
		$dureeHeures=0;
		$heureDebut=convertHourToDecimal($planning['heures'][0]);			
		$heureFin=convertHourToDecimal(end($planning['heures']));
		if (empty($infosJour['duree_details'])||($infosJour['duree_details']=="duree"))
		{
			$heureDebutTxt=$planning['heures'][0];			
			$heureFinTxt=end($planning['heures']);			
			$heureDebut=convertHourToDecimal($heureDebutTxt);			
			$heureFin=convertHourToDecimal($heureFinTxt);	
			if (empty($infosJour['duree']))
			{
				$dureeHeures=calcul_duree_heures_non_masquees($heureDebut,$heureFin);
			}else $dureeHeures=convertHourToDecimal($infosJour['duree']);
		}elseif ($infosJour['duree_details']=='AM')
		{
			$dureeAM=convertHourToDecimal(CONFIG_DURATION_AM);
			$heureDebutTxt=$planning['heures'][0];			
			$heureDebut=convertHourToDecimal($planning['heures'][0]);
			$heureFin=$heureDebut + $dureeAM;
			$dureeHeures=calcul_duree_heures_non_masquees($heureDebut,$heureFin);
		}elseif ($infosJour['duree_details']=='PM')
		{
			$dureePM=convertHourToDecimal(CONFIG_DURATION_PM);
			$heureFin=convertHourToDecimal(end($planning['heures']));			
			$heureDebut=$heureFin - $dureePM;
			$heureDebutTxt=$heureDebut;
			$dureeHeures=calcul_duree_heures_non_masquees($heureDebut,$heureFin);
		}else 
		{
			$heureExploded=explode(';',$infosJour['duree_details']);
			$heureDebut=convertHourToDecimal($heureExploded[0]);
			$heureFin=convertHourToDecimal($heureExploded[1]);
			$heureDebutTxt=$heureExploded[0];
			$heureFinTxt=$heureExploded[1];
			$dureeHeures=calcul_duree_heures_non_masquees($heureDebut,$heureFin);
		}
		// Calcule des créneaux masqués

		$cellule=array(
			'id'=>$infosJour['periode_id'],
			'date_debut'=>$infosJour['date_debut'],
			'date_fin'=>$infosJour['date_fin'],
			'user_nom'=>$infosJour['user_nom'],
			'team_id'=>$infosJour['user_groupe_id'],
			'team_nom'=>$infosJour['team_nom'],
			'projet_nom'=>$infosJour['projet_nom'],
			'notes'=>$infosJour['notes'],
			'titre'=>$infosJour['titre'],
			'periode_id'=>$infosJour['periode_id'],
			'parent_id'=>$infosJour['parent_id'],
			'projet_id'=>$infosJour['projet_id'],
			'groupe_nom'=>$infosJour['groupe_nom'],
			'charge'=>$infosJour['charge'],
			'user_id'=>$infosJour['user_id'],
			'lieu_id'=>$infosJour['lieu_id'],		
			'ressource_id'=>$infosJour['ressource_id'],			
			'livrable'=>$infosJour['livrable'],
			'statut_nom'=>$infosJour['status_nom'],
			'statut_tache'=>$infosJour['statut_tache'],
			'statut_couleur'=>$infosJour['statut_couleur'],
			'statut_barre'=>$infosJour['statut_barre'],	
			'statut_gras'=>$infosJour['statut_gras'],	
			'statut_italique'=>$infosJour['statut_italique'],	
			'statut_souligne'=>$infosJour['statut_souligne'],	
			'statut_pourcentage'=>$infosJour['statut_pourcentage'],			
			'status'=>$infosJour['status_nom'],
			'livrable'=>$infosJour['livrable'],
			'custom'=>$infosJour['custom'],
			'lieu'=>$infosJour['lieu_id'],
			'ressource'=>$infosJour['ressource_id'],
			'lieu_nom'=>$infosJour['lieu_nom'],
			'ressource_nom'=>$infosJour['ressource_nom'],
			'lien'=>$infosJour['lien'],
			'duree'=>$infosJour['duree'],
			'createur_id'=>$infosJour['createur_id'],
			'nom_modifier'=>$infosJour['nom_modifier'],
			'nom_createur'=>$infosJour['nom_createur'],
			'projet_createur_id'=>$infosJour['projet_createur_id'],
			'date_creation'=>$infosJour['date_creation'],
			'duree_details'=>$infosJour['duree_details'],
			'date_modif'=>$infosJour['date_modif'],
			'user_couleur'=>$infosJour['user_couleur'],
			'projet_couleur'=>$infosJour['projet_couleur'],
			'dureeHeures'=>$dureeHeures,
			'heure_debut'=>$heureDebut,
			'heure_fin'=>$heureFin);
		if( $base_ligne=='projets') {
			$cellule['nom_cellule']=xss_protect($infosJour['user_id']);
			$cellule['couleur']=xss_protect($infosJour['user_couleur']);
			$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_PROJET;
		}
		if( $base_ligne=='users') {
			$cellule['nom_cellule']=xss_protect($infosJour['projet_id']);
			$cellule['couleur']=xss_protect($infosJour['projet_couleur']);
			$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_PERSONNE;
		}
		if( $base_ligne=='lieux') {
			$cellule['nom_cellule']=xss_protect($infosJour['projet_id']);
			$cellule['couleur']=xss_protect($infosJour['projet_couleur']);
			$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_LIEU;
		}			
		if( $base_ligne=='ressources') {
			$cellule['nom_cellule']=xss_protect($infosJour['projet_id']);
			$cellule['couleur']=xss_protect($infosJour['projet_couleur']);
			$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE;
		}			
		if( $base_ligne=='heures') {
			$cellule['nom_cellule']=xss_protect($infosJour['projet_id']);
			$cellule['couleur']=xss_protect($infosJour['projet_couleur']);
			$cellule['url_modif']="";
			$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_PROJET;
		}
		switch($type_cellule)
		{
			case 'code_projet': $cellule['nom_cellule']= $infosJour['projet_id'];break;
			case 'code_personne': $cellule['nom_cellule']= $infosJour['user_id'];break;
			case 'code_lieu': $cellule['nom_cellule']= $infosJour['lieu_id'];break;
			case 'code_ressource': $cellule['nom_cellule']= $infosJour['ressource_id'];break;
			case 'nom_projet': $cellule['nom_cellule']= $infosJour['projet_nom'];break;
			case 'nom_personne': $cellule['nom_cellule']= $infosJour['user_nom'];break;
			case 'nom_lieu': $cellule['nom_cellule']= $infosJour['lieu_nom'];break;
			case 'nom_ressource': $cellule['nom_cellule']= $infosJour['ressource_nom'];break;
			case 'nom_tache': $cellule['nom_cellule']= $infosJour['titre'];break;
			case 'vide': $cellule['nom_cellule']= " ";break;
		}

		if (isset($infosJour['duree_details_heure_debut']))
		{
			$cellule['duree_details_heure_debut']=$infosJour['duree_details_heure_debut'];
		}
		if (isset($infosJour['duree_details_heure_fin']))
		{
			$cellule['duree_details_heure_fin']=$infosJour['duree_details_heure_fin'];
		}
		$planning['periodes'][$infosJour['periode_id']]=$cellule;
	}
	
	// Mode colonne jour
	// traitement de chaque jour (construction du planning en mode jours)
	if ($base_colonne=='jours')
	{
		while ($tmpDate <= $dateFin_planning) {
			$cle=$tmpDate->format('Y-m-d');
			// tâches par user et jour
			if ($base_ligne=='users') 
				$planning['taches'][$infosJour['user_id']][$cle][]=$infosJour['periode_id'];

			// tâches par projet et jour
			if ($base_ligne=='projets')
				$planning['taches'][$infosJour['projet_id']][$cle][]=$infosJour['periode_id'];

			// tâches par lieux et jour
			if ($base_ligne=='lieux')
				$planning['taches'][$infosJour['lieu_id']][$cle][]=$infosJour['periode_id'];

			// tâches par ressources et jour
			if ($base_ligne=='ressources')
				$planning['taches'][$infosJour['ressource_id']][$cle][]=$infosJour['periode_id'];

			// tâches par heures et jour
			if ($base_ligne=='heures')
			{
				$premierTranche=$planning['heures'][0];
				$derniereTranche=end($planning['heures']);	
				// Si on est sur un jour complet, on rempli l'ensemble des tranches horaires
				if (empty($infosJour['duree_details'])||($infosJour['duree_details']=="duree"))
				{
					foreach ($planning['heures'] as $heure) {
						$planning['taches'][$heure][$cle][]=$infosJour['periode_id'];
					}
				// Si on est sur une demie-journée AM
				}elseif ($infosJour['duree_details']=='AM')
				{
					$dureeAM=convertHourToDecimal(CONFIG_DURATION_AM);
					$heureDebut="08";
					$heureFin=$heureDebut + $dureeAM;
					for ($h = $heureDebut; $h < $heureFin; $h++)
					{
						// Heure pleine
						$heure=sprintf("%'.02d:00", $h);
						$planning['taches'][$heure][$cle][]=$infosJour['periode_id'];
						// Demie heure
						if ($heureFin>($h+0.5))
						{
							$heure=sprintf("%'.02d:30", $h);
							$planning['taches'][$heure][$cle][]=$infosJour['periode_id'];
						}
					}
				// Si on est sur une demie-journée PM
				}elseif ($infosJour['duree_details']=='PM')
				{
					$dureePM=convertHourToDecimal(CONFIG_DURATION_PM);
					$heureFin="17";
					$heureDebut=$heureFin-$dureePM;
					for ($h = $heureDebut; $h <= $heureFin; $h++)
					{
						// Heure pleine
						$heure=sprintf("%'.02d:00", $h);
						$planning['taches'][$heure][$cle][]=$infosJour['periode_id'];
						// Demie heure
						if ($heureFin>($h+0.5))
						{
							$heure=sprintf("%'.02d:30", $h);
							$planning['taches'][$heure][$cle][]=$infosJour['periode_id'];
						}
					}					
				// Si on est sur des heures précises			
				}else 
				{
					$dureePM=convertHourToDecimal(CONFIG_DURATION_PM);
					$heureFin="08";
					$heureDebut="17";
					for ($h = $heureDebut; $h <= $heureFin; $h++)
					{
						// Heure pleine
						$heure=sprintf("%'.02d:00", $h);
						$planning['taches'][$heure][$cle][]=$infosJour['periode_id'];
						// Demie heure
						if ($heureFin>($h+0.5))
						{
							$heure=sprintf("%'.02d:30", $h);
							$planning['taches'][$heure][$cle][]=$infosJour['periode_id'];
						}
					}	
				}
			}
		
			// calcul des totaux jours
			if(!isset($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')])) {
				$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = '00:00';
			}
			if($infosJour['date_fin'] != '') {
				$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = ajouterDuree($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')], usertime2sqltime(CONFIG_DURATION_DAY, false));
			} else {
				if ($infosJour['duree_details']=="AM")
				{
					$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = ajouterDuree($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')], usertime2sqltime(CONFIG_DURATION_AM, false));
				}elseif ($infosJour['duree_details']=="PM")
				{
					$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = ajouterDuree($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')], usertime2sqltime(CONFIG_DURATION_PM, false));
				}else
				{
					$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = ajouterDuree($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')], usertime2sqltime($infosJour['duree'], false));
				}
			}

			if (!in_array($tmpDate->format('w'), $DAYS_INCLUDED) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {$weekend=true;}else $weekend=false;
			
			// on additionne le total des jours
			if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1 || (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 0 && $weekend==false))
			{
				if(!isset($totalHoraireParJour[$tmpDate->format('Ymd')])) {
					$totalNbTachesParJour[$tmpDate->format('Ymd')] = 0;
					$totalHoraireParJour[$tmpDate->format('Ymd')] = '00:00';
				}
				
				if($infosJour['date_fin'] != '') {
					$totalHoraireParJour[$tmpDate->format('Ymd')] = ajouterDuree($totalHoraireParJour[$tmpDate->format('Ymd')], usertime2sqltime(CONFIG_DURATION_DAY, false));
				} else {
				$totalHoraireParJour[$tmpDate->format('Ymd')] = ajouterDuree($totalHoraireParJour[$tmpDate->format('Ymd')], usertime2sqltime($infosJour['duree'], false));
				}
				$totalNbTachesParJour[$tmpDate->format('Ymd')] += 1;
			}

		// boucle sur les jours
		$tmpDate->modify('+1 day');
		}
	}

	// Mode colonne users
	// traitement de chaque users
	if ($base_colonne=='users')
	{
		foreach ($usersFiltre->getSmartyData() as $cle_user) {
			if ($infosJour['user_id'] == $cle_user['user_id'])
			{				
				$cle=$cle_user['user_id'];
				
				// tâches par user et jour
				if ($base_ligne=='users') 
					$planning['taches'][$infosJour['user_id']][$cle][]=$infosJour['periode_id'];

				// tâches par projet et jour
				if ($base_ligne=='projets')
					$planning['taches'][$infosJour['projet_id']][$cle][]=$infosJour['periode_id'];

				// tâches par lieux et jour
				if ($base_ligne=='lieux')
					$planning['taches'][$infosJour['lieu_id']][$cle][]=$infosJour['periode_id'];

				// tâches par ressources et jour
				if ($base_ligne=='ressources')
					$planning['taches'][$infosJour['ressource_id']][$cle][]=$infosJour['periode_id'];

				// tâches par heures et jour
				if ($base_ligne=='heures')
				{
					$heureDebut=sprintf("%'.02d:00",$planning['heures'][0]);
					$derniereTranche=end($planning['heures']);
					$L0=(substr($infosJour['projet_id'], 0, CONFIG_PLANNING_CODE_WIDTH));
					if($dimensionCase=='large') 
					{
						$largeur=130+20;
					}else $largeur=strlen($L0)*3+22;
					// Si on est sur un jour complet, on rempli l'ensemble des tranches horaires
					if (empty($infosJour['duree_details']))
					{
						$planning['taches'][$heureDebut][$cle][]=$infosJour['periode_id'];
						foreach ($planning['heures'] as $heure) 
						{
							$planning['taches_horaires'][$heure][$cle][]=$infosJour['periode_id'];
							$planning['taches_horaires_users'][$cle][$heure][]=$infosJour['periode_id'];
							// calcul de la largeur minimal de la cellule
							if (isset($planning['taches_horaires'][$heure][$cle]['largeur']))
							{
								$planning['taches_horaires'][$heure][$cle]['largeur']=$planning['taches_horaires'][$heure][$cle]['largeur']+$largeur;
							}else $planning['taches_horaires'][$heure][$cle]['largeur']=$largeur;
						}
					// Si on est sur une durée fixe	
					}elseif ($infosJour['duree_details']=="duree")
					{
						$dureeFixe=convertHourToDecimal($infosJour['duree']);
						$planning['taches'][$heureDebut][$cle][]=$infosJour['periode_id'];
						$heureDebut=sprintf("%'.02d:00",$planning['heures'][0]);
						$heureDebut=convertHourToDecimal($planning['heures'][0]);
						$heureFin=$heureDebut + $dureeFixe;
						for ($h = $heureDebut; $h < $heureFin; $h++)
						{
							// Heure pleine
							$heure=sprintf("%'.02d:00", $h);
							$planning['taches_horaires'][$heure][$cle][]=$infosJour['periode_id'];
							$planning['taches_horaires_users'][$cle][$heure][]=$infosJour['periode_id'];
							// calcul de la largeur minimal de la cellule
							if (isset($planning['taches_horaires'][$heure][$cle]['largeur']))
							{
								$planning['taches_horaires'][$heure][$cle]['largeur']=$planning['taches_horaires'][$heure][$cle]['largeur']+$largeur;
							}else $planning['taches_horaires'][$heure][$cle]['largeur']=$largeur;

							// Demie heure
							if ($heureFin>($h+0.5))
							{
								$heure=sprintf("%'.02d:30", $h);
								$planning['taches_horaires'][$heure][$cle][]=$infosJour['periode_id'];
								$planning['taches_horaires_users'][$cle][$heure][]=$infosJour['periode_id'];
								// calcul de la largeur minimal de la cellule
								if (isset($planning['taches_horaires'][$heure][$cle]['largeur']))
								{
									$planning['taches_horaires'][$heure][$cle]['largeur']=$planning['taches_horaires'][$heure][$cle]['largeur']+$largeur;
								}else $planning['taches_horaires'][$heure][$cle]['largeur']=$largeur;
							}
						}
					// Si on est sur une demie-journée AM
					}elseif ($infosJour['duree_details']=='AM')
					{
						$dureeAM=convertHourToDecimal(CONFIG_DURATION_AM);
						$planning['taches'][$heureDebut][$cle][]=$infosJour['periode_id'];
						$heureDebut=sprintf("%'.02d:00",$planning['heures'][0]);
						$heureDebut=convertHourToDecimal($planning['heures'][0]);
						//$heure=sprintf("%'.02d:00", $h);
						$heureFin=$heureDebut + $dureeAM;
						for ($h = $heureDebut; $h < $heureFin; $h++)
						{
							// Heure pleine
							$heure=sprintf("%'.02d:00", $h);
							$planning['taches_horaires'][$heure][$cle][]=$infosJour['periode_id'];
							$planning['taches_horaires_users'][$cle][$heure][]=$infosJour['periode_id'];
							// calcul de la largeur minimal de la cellule
							if (isset($planning['taches_horaires'][$heure][$cle]['largeur']))
							{
								$planning['taches_horaires'][$heure][$cle]['largeur']=$planning['taches_horaires'][$heure][$cle]['largeur']+$largeur;
							}else $planning['taches_horaires'][$heure][$cle]['largeur']=$largeur;

							// Demie heure
							if ($heureFin>($h+0.5))
							{
								$heure=sprintf("%'.02d:30", $h);
								$planning['taches_horaires'][$heure][$cle][]=$infosJour['periode_id'];
								$planning['taches_horaires_users'][$cle][$heure][]=$infosJour['periode_id'];
								// calcul de la largeur minimal de la cellule
								if (isset($planning['taches_horaires'][$heure][$cle]['largeur']))
								{
									$planning['taches_horaires'][$heure][$cle]['largeur']=$planning['taches_horaires'][$heure][$cle]['largeur']+$largeur;
								}else $planning['taches_horaires'][$heure][$cle]['largeur']=$largeur;
							}
						}
					// Si on est sur une demie-journée PM
					}elseif ($infosJour['duree_details']=='PM')
					{
						$dureePM=convertHourToDecimal(CONFIG_DURATION_PM);
						$heureFin=convertHourToDecimal(end($planning['heures']))+0.5;
						$heureDebut=($heureFin-$dureePM);
						$heureDebut2=sprintf("%'.02d:30",$heureDebut);
						$planning['taches'][$heureDebut2][$cle][]=$infosJour['periode_id'];
						for ($h = $heureDebut; $h <= $heureFin; $h++)
						{
							// Heure pleine
							$heure=sprintf("%'.02d:00", $h);
							$planning['taches_horaires'][$heure][$cle][]=$infosJour['periode_id'];
							$planning['taches_horaires_users'][$cle][$heure][]=$infosJour['periode_id'];
							// calcul de la largeur minimal de la cellule
							if (isset($planning['taches_horaires'][$heure][$cle]['largeur']))
							{
								$planning['taches_horaires'][$heure][$cle]['largeur']=$planning['taches_horaires'][$heure][$cle]['largeur']+$largeur;
							}else $planning['taches_horaires'][$heure][$cle]['largeur']=$largeur;
							// Demie heure
							if ($heureFin>=($h+0.5))
							{
								$heure=sprintf("%'.02d:30", $h);
								$planning['taches_horaires'][$heure][$cle][]=$infosJour['periode_id'];
								$planning['taches_horaires_users'][$cle][$heure][]=$infosJour['periode_id'];
								// calcul de la largeur minimal de la cellule
								if (isset($planning['taches_horaires'][$heure][$cle]['largeur']))
								{
									$planning['taches_horaires'][$heure][$cle]['largeur']=$planning['taches_horaires'][$heure][$cle]['largeur']+$largeur;
								}else $planning['taches_horaires'][$heure][$cle]['largeur']=$largeur;
							}
						}					
					// Si on est sur des heures précises			
					}else 
					{
						$heureExploded=explode(';',$infosJour['duree_details']);
						$heureExploded2=explode(':',$heureExploded[0]);
						$heureDebutSelect=$heureExploded2[0].":".$heureExploded2[1];
						$heureDebut=convertHourToDecimal($planning['heures'][0]);
						$heureFin=convertHourToDecimal(end($planning['heures']))+0.5;
						if ($heureExploded2[1]<30)
						{
							$h2=sprintf("%'.02d:00",convertHourToDecimal($heureExploded[0]).":00");
						}else $h2=sprintf("%'.02d:30",convertHourToDecimal($heureExploded[0]).":30");

						$heureFinSelect=convertHourToDecimal($heureExploded[1]);
						$minDebut=explode(':',$heureExploded[0]);
						$planning['taches'][$h2][$cle][]=$infosJour['periode_id'];

						for ($h = $heureDebut; $h < $heureFin; $h++)
						{
							// Heure pleine
							if ($h>=round($heureDebutSelect,0,PHP_ROUND_HALF_DOWN) and $h<$heureFinSelect)
							{
								$heure=sprintf("%'.02d:00", $h);
								$planning['taches_horaires'][$heure][$cle][]=$infosJour['periode_id'];
								$planning['taches_horaires_users'][$cle][$heure][]=$infosJour['periode_id'];
								// calcul de la largeur minimal de la cellule
								if (isset($planning['taches_horaires'][$heure][$cle]['largeur']))
								{
									$planning['taches_horaires'][$heure][$cle]['largeur']=$planning['taches_horaires'][$heure][$cle]['largeur']+$largeur;
								}else $planning['taches_horaires'][$heure][$cle]['largeur']=$largeur;
							}
							// Demie heure
							if ( (($h+0.5)>=$heureDebutSelect || ($h+1)>$heureDebutSelect) and ($h+0.5)<$heureFinSelect)
							{
								$heure=sprintf("%'.02d:30", $h);
								$planning['taches_horaires'][$heure][$cle][]=$infosJour['periode_id'];
								$planning['taches_horaires_users'][$cle][$heure][]=$infosJour['periode_id'];
								// calcul de la largeur minimal de la cellule
								if (isset($planning['taches_horaires'][$heure][$cle]['largeur']))
								{
									$planning['taches_horaires'][$heure][$cle]['largeur']=$planning['taches_horaires'][$heure][$cle]['largeur']+$largeur;
								}else $planning['taches_horaires'][$heure][$cle]['largeur']=$largeur;
							}
						}
						
					}
				}

				// calcul des totaux jours
				if(!isset($totauxJourUsers[$tmpDate->format('Ymd')][$infosJour['user_id']])) {
					$totauxJourUsers[$tmpDate->format('Ymd')][$infosJour['user_id']] = '00:00';
				}
				if($infosJour['date_fin'] != '') {
					$totauxJourUsers[$tmpDate->format('Ymd')][$infosJour['user_id']] = ajouterDuree($totauxJourUsers[$tmpDate->format('Ymd')][$infosJour['user_id']], usertime2sqltime(CONFIG_DURATION_DAY, false));
				} else {
					$totauxJourUsers[$tmpDate->format('Ymd')][$infosJour['user_id']] = ajouterDuree($totauxJourUsers[$tmpDate->format('Ymd')][$infosJour['user_id']], usertime2sqltime($infosJour['duree'], false));
				}
				if (!in_array($tmpDate->format('w'), $DAYS_INCLUDED) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {$weekend=true;}else $weekend=false;
				// on additionne le total des jours
					if(!isset($totalHoraireParJour[$infosJour['user_id']])) {
						$totalHoraireParJour[$infosJour['user_id']] = '00:00';
						$totalNbTachesParJour[$infosJour['user_id']] = 0;
					}
					if($infosJour['date_fin'] != '') {
						$totalHoraireParJour[$infosJour['user_id']] = ajouterDuree($totalHoraireParJour[$infosJour['user_id']], usertime2sqltime(CONFIG_DURATION_DAY, false));
					} else {
						$totalHoraireParJour[$infosJour['user_id']] = ajouterDuree($totalHoraireParJour[$infosJour['user_id']], usertime2sqltime($infosJour['duree'], false));
					}
					$totalNbTachesParJour[$infosJour['user_id']] += 1;
			}
		}
	}
	
	// Mode colonne heures
	// traitement de chaque heure (construction du planning en mode heures)
	if ($base_colonne=='heures') {

		$premierTranche=$planning['heures'][0];
		$derniereTranche=end($planning['heures']);	

		while ($tmpDate <= $dateFin_planning) {		
			// Si on est sur un jour complet, on rempli l'ensemble des tranches horaires
			if (empty($infosJour['duree_details'])) {
				foreach ($planning['heures'] as $heure) {
					// tâches par user et jour
					if ($base_ligne=='users') 
						$planning['taches'][$infosJour['user_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par projet et jour
					if ($base_ligne=='projets')
						$planning['taches'][$infosJour['projet_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par lieux et jour
					if ($base_ligne=='lieux')
						$planning['taches'][$infosJour['lieu_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par ressources et jour
					if ($base_ligne=='ressources')
						$planning['taches'][$infosJour['ressource_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];
				}
			
			// Si on est sur une durée fixe
			} elseif ($infosJour['duree_details']=="duree") {
				$dureeFixe=convertHourToDecimal($infosJour['duree']);
				$heureDebut=convertHourToDecimal($planning['heures'][0]);
				$heureFin=$heureDebut + $dureeFixe;
				for ($h = $heureDebut; $h < $heureFin; $h++) {
					// Heure pleine
					$heure=sprintf("%'.02d:00", $h);
					// tâches par user et jour
					if ($base_ligne=='users') 
						$planning['taches'][$infosJour['user_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par projet et jour
					if ($base_ligne=='projets')
						$planning['taches'][$infosJour['projet_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par lieux et jour
					if ($base_ligne=='lieux')
						$planning['taches'][$infosJour['lieu_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par ressources et jour
					if ($base_ligne=='ressources')
						$planning['taches'][$infosJour['ressource_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];
				}
			
			// Si on est sur une demie-journée AM
			} elseif ($infosJour['duree_details']=='AM') {
				$dureeAM=convertHourToDecimal(CONFIG_DURATION_AM);
				$heureDebut=convertHourToDecimal($planning['heures'][0]);
				$heureFin=$heureDebut + $dureeAM;
				for ($h = $heureDebut; $h < $heureFin; $h++)
				{
					// Heure pleine
					$heure=sprintf("%'.02d:00", $h);
					// tâches par user et jour
					if ($base_ligne=='users') 
						$planning['taches'][$infosJour['user_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par projet et jour
					if ($base_ligne=='projets')
						$planning['taches'][$infosJour['projet_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par lieux et jour
					if ($base_ligne=='lieux')
						$planning['taches'][$infosJour['lieu_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par ressources et jour
					if ($base_ligne=='ressources')
						$planning['taches'][$infosJour['ressource_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];
				}

			// Si on est sur une demie-journée PM
			} elseif ($infosJour['duree_details']=='PM') {
				$dureePM=convertHourToDecimal(CONFIG_DURATION_PM);
				$heureFin=convertHourToDecimal(end($planning['heures']))+0.5;
				$heureDebut=$heureFin-$dureePM;
				for ($h = $heureDebut; $h < $heureFin; $h++)
				{
					// Heure pleine
					$heure=sprintf("%'.02d:00", $h);
					// tâches par user et jour
					if ($base_ligne=='users') 
						$planning['taches'][$infosJour['user_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par projet et jour
					if ($base_ligne=='projets')
						$planning['taches'][$infosJour['projet_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par lieux et jour
					if ($base_ligne=='lieux')
						$planning['taches'][$infosJour['lieu_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par ressources et jour
					if ($base_ligne=='ressources')
						$planning['taches'][$infosJour['ressource_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];
				}					
			// Si on est sur des heures précises			
			} else {
				$heureExploded=explode(';',$infosJour['duree_details']);
				$heureDebut=convertHourToDecimal($planning['heures'][0]);
				$heureFin=convertHourToDecimal(end($planning['heures']))+0.5;
				$heureDebutSelect=convertHourToDecimal($heureExploded[0]);
				$heureFinSelect=convertHourToDecimal($heureExploded[1]);
				for ($h = $heureDebut; $h < $heureFin; $h++)
				{
					// Heure pleine
					if ($h>=round($heureDebutSelect,0,PHP_ROUND_HALF_DOWN) and $h<$heureFinSelect)
					{
					// Heure pleine
					$heure=sprintf("%'.02d:00", $h);
					// tâches par user et jour
					if ($base_ligne=='users') 
						$planning['taches'][$infosJour['user_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par projet et jour
					if ($base_ligne=='projets')
						$planning['taches'][$infosJour['projet_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par lieux et jour
					if ($base_ligne=='lieux')
						$planning['taches'][$infosJour['lieu_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];

					// tâches par ressources et jour
					if ($base_ligne=='ressources')
						$planning['taches'][$infosJour['ressource_id']][$tmpDate->format('Y-m-d')][$heure][]=$infosJour['periode_id'];
					}
					
				}
			}
			
			// calcul des totaux jours
			if(!isset($totalHoraireParJour[$tmpDate->format('Ymd')])) {
				$totalHoraireParJour[$tmpDate->format('Ymd')] = '00:00';
				$totalNbTachesParJour[$tmpDate->format('Ymd')] = 0;
			}
			if($infosJour['date_fin'] != '') {
				$totalHoraireParJour[$tmpDate->format('Ymd')] = ajouterDuree($totalHoraireParJour[$tmpDate->format('Ymd')], usertime2sqltime(CONFIG_DURATION_DAY, false));
			} else {
				$totalHoraireParJour[$tmpDate->format('Ymd')] = ajouterDuree($totalHoraireParJour[$tmpDate->format('Ymd')], usertime2sqltime($infosJour['duree'], false));
			}
			$totalNbTachesParJour[$tmpDate->format('Ymd')] +=1;
			
		// calcul des totaux jours
		if(!isset($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')])) {
			$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = '00:00';
		}
		if($infosJour['date_fin'] != '') {
			$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = ajouterDuree($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')], usertime2sqltime(CONFIG_DURATION_DAY, false));
		} else {
			if ($infosJour['duree_details']=="AM")
			{
				$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = ajouterDuree($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')], usertime2sqltime(CONFIG_DURATION_AM, false));
			}elseif ($infosJour['duree_details']=="PM")
			{
				$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = ajouterDuree($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')], usertime2sqltime(CONFIG_DURATION_PM, false));

			}else
			{
				$totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')] = ajouterDuree($totauxJourUsers[$infosJour['user_id']][$tmpDate->format('Ymd')], usertime2sqltime($infosJour['duree'], false));
			}
		}
			
		// boucle sur les jours
		$tmpDate->modify('+1 day');
		}
	}
}

//////////////////////////
// CALCUL DU PARALLELISME DES TACHES
//////////////////////////
if (isset($planning['taches_horaires']))
{
	foreach($planning['taches_horaires'] as $creneau)
	{
		foreach ($creneau as $userk=>$tab)
		{
			if (isset($max[$userk]))
			{
				$max[$userk]['largeur']=max($max[$userk]['largeur'],$tab['largeur']);
			}else 
			{
				$max[$userk]['largeur']=$tab['largeur'];
			}
		}
	}
	foreach($planning['taches_horaires_users'] as $u=>$creneaux)
	{
		$max_largeur=0;
		foreach ($creneaux as $c)
		{
			$padding=0;
			foreach ($c as $p)
			{
				// Récupération des infos sur la cellule
				$infos_periode=$planning['periodes'][$p];
				$largeur_cellule=strlen($infos_periode['nom_cellule'])*3+25;
					
				// On selectionne la plus grande largeur réservée
				if (isset($max_p[$p]['largeur2']))
				{
					$max_largeur_cellule=max($largeur_cellule,$max_p[$p]['largeur2']);
				}else $max_largeur_cellule=$largeur_cellule;
					
				if (isset($max_p[$p]['largeur2']))
				{
					$max_p[$p]['largeur2']=$max_largeur_cellule;
				}else $max_p[$p]['largeur2']=$largeur_cellule;
					
				$padding=$padding+$max_p[$p]['largeur2'];
			}
			if (isset($max[$u]['largeur']))
			{
				$max[$u]['largeur']=max($max[$u]['largeur'],$padding);
			}else $max[$u]['largeur']=$padding;
		}
	}
}
		
//////////////////////////
// ENTETES DU PLANNING
//////////////////////////
// Colonnes jour
if ($base_colonne=='jours')
{
	$headerMois = '' . CRLF;
	$headerSemaines = '' . CRLF;
	$headerNomJours = '' . CRLF;
	$headerNumeroJours = '' . CRLF;
	$colspanMois = '0';
	$colspanSemaine = '1';
	$tmpDate = clone $dateDebut;
	$tmpMois = $smarty->getConfigVars('month_' . $tmpDate->format('n')) . ' ' . $tmpDate->format('Y');
	$tmpMoisDateDebut = $tmpDate->format(CONFIG_DATE_FIRST_DAY_MONTH);
	$tmp2Date = clone $tmpDate;
	$tmp2Date->modify('+' . $nbJours . 'days');
	$tmpMoisDateFin = $tmp2Date->format(CONFIG_DATE_LONG);
	$dernierJourSemaineInclus = $DAYS_INCLUDED[count($DAYS_INCLUDED)-1];

	while ($tmpDate <= $dateFin) {
		$planning['colonnes'][]=$tmpDate->format('Y-m-d');
		if (in_array($tmpDate->format('w'), $DAYS_INCLUDED) && !array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			$sClass = 'week';
			$weekend = false;
		} else {
			if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
				$sClass = 'weekend';
				$weekend = true;
			} else {
				$tmpDate->modify('+1 day');
				continue;
			}
		}
		if( $tmpDate->format('Y-m-d') == date('Y-m-d')) {
			$sClass .= ' today';
		}
		$tmpJourDateDebut = $tmpDate->format(CONFIG_DATE_LONG);
		$tmp2Date = clone $tmpDate;
		$tmp2Date->modify('+' . $nbJours . 'days');
		$tmpJourDateFin = $tmp2Date->format(CONFIG_DATE_LONG);
		$headerNomJours .= '<th class="planning_head_dayname ' . $sClass . '"><a href="process/planning.php?date_debut_affiche='.$tmpJourDateDebut.'&date_fin_affiche='.$tmpJourDateFin.'">' . strtoupper(substr($smarty->getConfigVars('day_' . $tmpDate->format('w')), 0, 1)) . '</a></th>' . CRLF;
		$headerNumeroJours .= '<th class="planning_head_day ' . $sClass . '"><a href="process/planning.php?date_debut_affiche='.$tmpJourDateDebut.'&date_fin_affiche='.$tmpJourDateFin.'">' . $tmpDate->format('j') . '</a></th>' . CRLF;
		$nomMoisCourant = $smarty->getConfigVars('month_' . $tmpDate->format('n'));
		if ($nomMoisCourant . ' ' . $tmpDate->format('Y') == $tmpMois) {
			$colspanMois++;
		} else {
			$headerMois .= '<th class="planning_head_month" colspan="' . $colspanMois . '"><a href="process/planning.php?date_debut_affiche='.$tmpMoisDateDebut.'&date_fin_affiche='.$tmpMoisDateFin.'">' . $tmpMois . '</a></th>' . CRLF;
			$colspanMois = '1';
			$tmpMois = $nomMoisCourant . ' ' . $tmpDate->format('Y');
			$tmpMoisDateDebut = $tmpDate->format(CONFIG_DATE_FIRST_DAY_MONTH);
			$tmp2Date = clone $tmpDate;
			$tmp2Date->modify('+' . $nbJours . 'days');
			$tmpMoisDateFin = $tmp2Date->format(CONFIG_DATE_LONG);
		}
		// gestion des semaines
		if ((CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 0 && $tmpDate->format('w') == $dernierJourSemaineInclus) || $tmpDate->format('w') == 0) {
			// calcul du date de debut et fin de semaine
			$dateTime = strtotime( $tmpDate->format('d-m-Y'));
			$tmpSemaineDateDebut = date(CONFIG_DATE_LONG, strtotime('monday this week', $dateTime));
			$tmp2Date = clone $tmpDate;
			$tmp2Date->modify('+' . $nbJours . 'days');
			$tmpSemaineDateFin = $tmp2Date->format(CONFIG_DATE_LONG);
			$headerSemaines .= '<th class="planning_head_week" colspan="' . $colspanSemaine . '"><a href="process/planning.php?date_debut_affiche='.$tmpSemaineDateDebut.'&date_fin_affiche='.$tmpSemaineDateFin.'">' . $smarty->getConfigVars('planning_semaine') . ' ' . $tmpDate->format('W') . '</a></th>' . CRLF;
			$colspanSemaine = 1;
		} else {
			$colspanSemaine++;
		}
		$tmpDate->modify('+1 day');
	}
	// on cloture le colspan du mois en cours
	$headerMois .= '<th class="planning_head_month" colspan="' . $colspanMois . '"><a href="process/planning.php?date_debut_affiche='.$tmpMoisDateDebut.'&date_fin_affiche='.$tmpMoisDateFin.'">' . $tmpMois . '</a></th>' . CRLF;
	// on cloture le colspan de la semaine en cours
	if($colspanSemaine != 1) {
		// calcul du date de debut et fin de semaine
		$dateTime = strtotime( $tmpDate->format('d-m-Y'));
		$tmpSemaineDateDebut = date(CONFIG_DATE_LONG, strtotime('this week last monday', $dateTime));
		$tmp2Date = clone $tmpDate;
		$tmp2Date->modify('+' . $nbJours . 'days');
		$tmpSemaineDateFin = $tmp2Date->format(CONFIG_DATE_LONG);
		$headerSemaines .= '<th class="planning_head_week" colspan="' . ($colspanSemaine-1) . '"><a href="process/planning.php?date_debut_affiche='.$tmpSemaineDateDebut.'&date_fin_affiche='.$tmpSemaineDateFin.'">' . $smarty->getConfigVars('planning_semaine') .	' ' . $tmpDate->format('W') . '</a></th>' . CRLF;
	}
	$html .= '<table class="planningContent" id="tabContenuPlanning">' . CRLF;
	$html .= '<thead><tr id="planning_header_month">' . CRLF;
	$html .= '<th id="tdUser_0" rowspan="4" class="planning_switch planningFirstRowCol" scope="row"><div class="text-center"><a id="lienInverse" href="'.$linkswitch.'"><i class="fa fa-exchange fa-3x fa-lg" aria-hidden="true" style="color:white;"></i></a></div></th>' .CRLF;
	$html .= $headerMois . CRLF;
	$html .= '</tr>' . CRLF;
	$html .= '<tr id="planning_header_week">' . CRLF;
	$html .= $headerSemaines . CRLF;
	$html .= '</tr>' . CRLF;
	$html .= '<tr id="planning_header_dayname">' . CRLF;
	$html .= $headerNomJours . CRLF;
	$html .= '</tr>' . CRLF;
	$html .= '<tr id="planning_header_day">' . CRLF;
	$html .= $headerNumeroJours . CRLF;
	$html .= '</tr></thead><tbody>' . CRLF;
	// FIN ENTETES DU TABLEAU (MOIS, SEMAINE ET JOUR)	
}

// Colonnes heures
if ($base_colonne=='heures')
{
	if($dimensionCase=='large'){
		$largeurCase = 130;
	}else{
		$largeurCase = 34;
	}
	$html .= '<table class="planningContent" id="tabContenuPlanning"><thead>' . CRLF;
	$html .= '<tr id="planning_header_week_hour">' . CRLF;
	$html .= '<th id="tdUser_0" rowspan="2" class="planning_switch planningFirstRowCol" scope="row"><div class="text-center"><a id="lienInverse" href="'.$linkswitch.'"><i class="fa fa-exchange fa-3x fa-lg" aria-hidden="true" style="color:white;"></i></a></div></th>' . CRLF;
	$tmpDateFin = clone $dateFin;
	$tmpDate = clone $dateDebut;
	while ($tmpDate <= $dateFin) {
		$planning['colonnes'][]=$tmpDate->format('Y-m-d');
		if (!in_array($tmpDate->format('w'), $DAYS_INCLUDED)) {
			if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
				$sClass = 'weekend';
				$weekend = true;
			}else {
				$tmpDate->modify('+1 day');
				continue;
			}
		} elseif (array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
				$sClass = 'weekend';
				$weekend = true;
			}else {
				$tmpDate->modify('+1 day');
				continue;
			}
		} else {
			$weekend = false;
			$sClass = 'week';
		}
		if( $tmpDate->format('Y-m-d') == date('Y-m-d')) 
		{
			$sClass .= ' today';
		}
		$dateTime = strtotime( $tmpDate->format('d-m-Y'));
		$tmpSemaineDateDebut = date(CONFIG_DATE_LONG, strtotime('monday this week', $dateTime));
		$tmp2Date = clone $tmpDate;
		$tmp2Date->modify('+' . $nbJours . 'days');
		$tmpSemaineDateFin = $tmp2Date->format(CONFIG_DATE_LONG);
		$tmpJourDateDebut = $tmpDate->format(CONFIG_DATE_LONG);
		$tmp2Date = clone $tmpDate;
		$tmp2Date->modify('+' . $nbJours . 'days');
		$tmpJourDateFin = $tmp2Date->format(CONFIG_DATE_LONG);
	
		$cle_date=$tmpDate->format('Y-m-d');
	
		$html .= '<th colspan="' . count($tabTranchesHoraires) . '" class="planning_head_week ' . $sClass .'">' . CRLF;
		$html .= '<a href="process/planning.php?date_debut_affiche='.$tmpSemaineDateDebut.'&date_fin_affiche='.$tmpSemaineDateFin.'">'.$smarty->getConfigVars('planning_semaine2') . ' ' . $tmpDate->format('W') . '</a>&nbsp;&nbsp;&nbsp; <a href="process/planning.php?date_debut_affiche='.$tmpJourDateDebut.'&date_fin_affiche='.$tmpJourDateFin.'">' . $smarty->getConfigVars('day_' . $tmpDate->format('w')) . ' ' . $tmpDate->format(CONFIG_DATE_LONG) .'</a>'.CRLF;
		$html .= '</th>' . CRLF;
		$tmpDate->modify('+1 day');
	}
	$html .= '</tr>' . CRLF;
	$html .= '<tr id="planning_header_hour">' . CRLF;
	$tmpDate = clone $dateDebut;
	// On réinitialise la dateFin
	$dateFin = clone $tmpDateFin;
	while ($tmpDate <= $dateFin) {
		if (!in_array($tmpDate->format('w'), $DAYS_INCLUDED)) {
			if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
				$sClass = 'weekend';
				$weekend = true;
			}else {
				$tmpDate->modify('+1 day');
				continue;
			}
		} elseif (array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
				$sClass = 'weekend';
				$weekend = true;
			}else {
				$tmpDate->modify('+1 day');
				continue;
			}
		} else {
			$weekend = false;
			$sClass = 'week';
		}
		if($tmpDate->format('Y-m-d') == date('Y-m-d')) 
		{
			$sClass .= ' today';
		}
		
		reset($tabTranchesHoraires);
		foreach ($tabTranchesHoraires as $trancheHeureCourante) {
			$trancheFin = $trancheHeureCourante + 1;
			if($trancheFin == 24) 
			{
				$trancheFin = 0;
			}
			$html .= '<th class="planning_head_hour ' . $sClass . '" style="width:' . $largeurCase . 'px;">' . $trancheHeureCourante . '-' .  $trancheFin . $smarty->getConfigVars('tab_h') . '</th>';
		}
		$tmpDate->modify('+1 day');
	}
	$html .= '</tr></thead><tbody>' . CRLF;
}

// Colonnes user
if ($base_colonne=='users')
{
	$html .= '<table class="planningContent" id="tabContenuPlanning">' . CRLF;
	$html .= '<thead><tr>' . CRLF;
	if ($base_colonne=="users" && $base_ligne=="heures" ) 
	{
		$html .= '<th id="tdUser_0" colspan="2" class="planning_switch planningFirstRowCol" scope="row"><div class="text-center"><a id="lienInverse" href="'.$linkswitch.'"><i class="fa fa-exchange fa-3x fa-lg" aria-hidden="true" style="color:white;"></i></a></div></th>' .CRLF;
	}else $html .= '<th id="tdUser_0" class="planning_switch planningFirstCol" scope="row"><div class="text-center"><a id="lienInverse" href="'.$linkswitch.'"><i class="fa fa-exchange fa-3x fa-lg" aria-hidden="true" style="color:white;"></i></a></div></th>' .CRLF;
	while ($u = $realUsers->fetch())
	{
		$infosUser = $u->getSmartyData();
		if (isset($max[$infosUser['user_id']]['largeur']))
		{
			$strminwidth="min-width:".($max[$infosUser['user_id']]['largeur'])."px;padding:7px";
		}else $strminwidth="padding:7px;";
		$html .= "<th class='planning_head_month'><div style='$strminwidth'>".$infosUser['user_id']."</div></th>" . CRLF;
		$planning['colonnes'][]=$infosUser['user_id'];
	}
	$html .= '</tr></thead><tbody>' . CRLF;	
}

//////////////////////////
// AFFICHAGE DES LIGNES
//////////////////////////
$nbLine = 1;
$groupeCourant = false;
$idGroupeCourant = -1;
$smarty->assign('nbPagesLignes', ceil($nbLignesTotal/$nbLignes));
foreach ($planning['lignes'] as $ligne)
{
	// every xx lines, repeat days/month/etc rows
	if(CONFIG_PLANNING_REPEAT_HEADER > 0) {
		if (($nbLine % CONFIG_PLANNING_REPEAT_HEADER) == 0) {
			$html .= '<tr>' . CRLF;
			$html .= '<th>&nbsp;</th>' . CRLF;
			$html .= $headerMois . CRLF;
			$html .= '</tr>' . CRLF;
			$html .= '<tr>' . CRLF;
			$html .= '<th>&nbsp;</th>' . CRLF;
			$html .= $headerSemaines . CRLF;
			$html .= '</tr>' . CRLF;
			$html .= '<tr>' . CRLF;
			$html .= '<th>&nbsp;</th>' . CRLF;
			$html .= $headerNomJours . CRLF;
			$html .= '</tr>' . CRLF;
			$html .= '<tr>' . CRLF;
			$html .= '<th>&nbsp;</th>' . CRLF;
			$html .= $headerNumeroJours . CRLF;
			$html .= '</tr>' . CRLF;
		}
	}
	$nbLine++;
	
	// gestion de l'affichage des groupes (de user ou projet) dans le planning
	if(strpos($_SESSION['triPlanning'], 'groupe_nom') !== FALSE || strpos($_SESSION['triPlanning'], 'team_nom') !== FALSE) {
		if ($base_colonne=='jours')
		{
			$nb_colonnes=count($planning['colonnes']);
			$classTeamDiv="planning_team_div";
		}else 
		{	
			$classTeamDiv="planning_team_div_hour";
			$nb_colonnes=(count($planning['heures']) * count($planning['colonnes']) );
		}
		if($base_ligne=="projets") 
		{
			if($ligne['groupe_nom'] !== $groupeCourant) 
			{
				$html .= '<tr>' . CRLF;
				$html .= '<th class="'.$classTeamDiv.'" id="tdUser_' . $idGroupeCourant . '" scope="row" style="z-index:7 !important;">&nbsp;' . ($ligne['groupe_nom'] != '' ? xss_protect($ligne['groupe_nom']) : $smarty->getConfigVars('planning_pasDeGroupe')) . '&nbsp;' . CRLF;
				$html .= '</th>' . CRLF;
				$html .= '<td class="'.$classTeamDiv.'" colspan="'.$nb_colonnes.'">&nbsp;</td>' . CRLF;
				$html .= '</tr>' . CRLF;
				$idGroupeCourant--;
			}
			$groupeCourant = $ligne['groupe_nom'];
		} elseif($base_ligne=="users") {
			if($ligne['team_nom'] !== $groupeCourant) {
				$html .= '<tr>' . CRLF;
				$html .= '<th class="'.$classTeamDiv.'" id="tdUser_' . $idGroupeCourant . '" scope="row" style="z-index:7 !important;">&nbsp;' . ($ligne['team_nom'] != '' ? xss_protect($ligne['team_nom']) : $smarty->getConfigVars('planning_pasDeTeam')) . '&nbsp;' . CRLF;
				$html .= '</th>' . CRLF;
				$html .= '<td class="'.$classTeamDiv.'" colspan="'.$nb_colonnes.'">&nbsp;</td>' . CRLF;
				$html .= '</tr>' . CRLF;
				$idGroupeCourant--;
			}
			$groupeCourant = $ligne['team_nom'];
		}
	}
	$ordreJourPrec = array();
	$joursOccupes = array();
	
	// pour chaque période de cette ligne, on rempli le tableau des jours occupés
	$infosJour['nom'] = xss_protect($ligne['nom']);

	// Calcul de l'id de la ligne
	if ($base_colonne<>"users" && $base_ligne<>"heures" )
	{
		$ligneId=$ligne['id'];
	}else
	{
		$ligneId=$dateDebut->format('Ymd');
	}
	// Calcul des jours occupés
	if ($base_colonne<>"heures")
	{
		if (isset($planning['taches'][$ligne['id']]))
		{
			foreach ($planning['taches'][$ligne['id']] as $cle => $tache) 
			{
				foreach ($tache as $t)
				{
					$info_tache=$planning['periodes'][$t];
					$joursOccupes[$cle][]=$t;
				}
			}
		}
	}else
	{
		if (isset($planning['taches'][$ligne['id']]))
		{
			foreach ($planning['taches'][$ligne['id']] as $cle => $heures) 
			{
				foreach ($heures as $cle2 => $taches)
				{
					foreach ($taches as $t)
					{
						$info_tache=$planning['periodes'][$t];
						$joursOccupes[$cle][$cle2][]=$t;
					}
				}
			}
		}
	}
	// si option de masquer les lignes vides est activée, on masque la ligne si elle est vide
	if($masquerLigneVide == 1 && count($joursOccupes) == 0 && $base_ligne<>"heures") {
		continue;
	}
	$ordreJourCourant = array();
	////////////////////////////////////////////////////
	// AFFICHAGE DE LA PREMIERE CASES DE CHAQUE LIGNE
	////////////////////////////////////////////////////
	// on genere la ligne courante
	$html .= '<tr>' . CRLF;
	if ($base_ligne=="heures" ) 
	{	
		// Dans le cas d'une ligne horaire, on n'affiche pas la demie-heure
		if (preg_match("/\:30/",$infosJour['nom'])) 
		{
			$html .= "<th class='planningFirstColMin' scope='row'>30</th>";
		}else
		{
			$h=str_replace(":00","h",$infosJour['nom']);
			$html .= "<th class='planningFirstColHour' rowspan='2' scope='row'>".$h."</th>";
			$html .= "<th class='planningFirstColMin' scope='row'>00</th>";
		}
	}else
	{
		$html .= '<th id="tdUser_' . ($nbLine-1) . '" ' . ((!is_null($ligne['couleur']) && $ligne['couleur'] != 'FFFFFF') ? ' style="background-color:#'.$ligne['couleur']. ';color:' . buttonFontColor('#' . $ligne['couleur']) . '"' : '') . ' class="planningFirstCol" scope="row">&nbsp;';
		
		// si le user a le droit, on permet de cliquer pour afficher la fiche de l'item (user ou projet)
		if (!empty($ligne['url_modif']))
		{
			$html .= '<a style="color:' . (!is_null($ligne['couleur']) && $ligne['couleur'] != 'FFFFFF' ? buttonFontColor('#' . $ligne['couleur']) . '' : '#ffffff') . '"';
			$html .= ' href="javascript:'.$ligne['url_modif'].';undefined;">' . $infosJour['nom'] . '</a>';
		}else 
		{
			$html .= '<span style="color:' . (!is_null($ligne['couleur']) && $ligne['couleur'] != 'FFFFFF' ? buttonFontColor('#' . $ligne['couleur']) . '' : '#ffffff') . '"';
			$html .= '>'.$infosJour['nom'].'</span>';
		}
		// dropdown choice for project actions
		if ($base_ligne=="projets" && !$user->checkDroit('tasks_readonly')) {
			$html .= '<div class="btn-group dropright" style="position:absolute;right:0;">';
			$html .= '
					<button class="btn dropdown-toggle" data-toggle="dropdown" id="p'.$ligne['id'].'" style="height:15px;border:0px;padding-top:0px;padding-left:6px;padding-right:6px;padding-bottom:22px"></button>
					<div class="dropdown-menu" aria-labelledby="p'.$ligne['id'].'">
						<a class="dropdown-item" href="javascript:xajax_projet_decalage_form(\'' . $ligne['id'] . '\');undefined;"><i class="fa fa-fw fa-arrows-h" aria-hidden="true"></i> ' . $smarty->getConfigVars('decaler_taches') . '</a>			
					</div>';
			$html .= '</div>';
		}
		$html .= '</th>' . CRLF;
	}

	////////////////////////////////////////////////////
	// AFFICHAGE DES CASES DE CHAQUE LIGNE
	////////////////////////////////////////////////////

	// on boucle sur la durée de l'affichage, on parcours tous les jours/semaines/heures
	if ($base_colonne=="jours" || $base_colonne=="users")
	{
		// Dans le cas d'affichage des jours, on boucle sur toutes les dates
		foreach ($planning['colonnes'] as $cle_colonne) 
		{
			// Planning Jour ou User
			if ($base_colonne=="jours"||$base_colonne=="users")
			{
				// Sélection de la clé
				if ($base_colonne=="jours")
				{
					$datePivot = new DateTime($cle_colonne);
					$current_date = $datePivot->format('Y-m-d');
					$current_date2 = $datePivot->format('Ymd');
					$current_week = $datePivot->format('w');
				}
				if ($base_colonne=="users")
				{
					$datePivot = clone $dateDebut;
					$current_date = $cle_colonne;
					$current_date2 = $datePivot->format('Ymd')."_".$infosJour['nom'];
					$current_week = $datePivot->format('w');
					$ligneId = $cle_colonne;
				}
				
				$styleTD = '';
				// Définition du style pour case semaine et WE
				if ((!in_array($current_week, $DAYS_INCLUDED) || array_key_exists($current_date, $joursFeries)) && $base_colonne<>"users") 
				{
					if (array_key_exists($current_date, $joursFeries) && CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
						if (empty($joursFeries[$current_date]['couleur'])) {
							$classTD = 'week feries';
						} else {
							$styleTD = " style='background-color:#".$joursFeries[$current_date]['couleur']."' ";
							$weekend = true;
						}
					}elseif (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
						$classTD = 'week weekend';
						$weekend = true;
					}else {
						continue;
					}
				} else {
					$classTD = 'week';
					$weekend = false;
				}
				
			// Si la date est un jour férié
				$ferie = false;
				if (array_key_exists($current_date, $joursFeries)) {
					$ferieObj = new Ferie();
					if($ferieObj->db_load(array('date_ferie', '=', $current_date)) && trim($ferieObj->libelle) != "") {
						if (CONFIG_PLANNING_MASQUER_FERIES == 0) {
							$tooltip = '<b>' . $ferieObj->libelle . '</b>';
							$ferie = '<div class="cellHolidays tooltipster" title="'.$tooltip.'">' . $smarty->getConfigVars('planning_ferie') . '</div>' . CRLF;
						}
					}
				}
				$largeuritems=8;
				// Si la date contient une tâche (jour avec au moins une case remplie)
				if (isset($joursOccupes[$current_date])) {
					
					// Affichage de la case
                    $idCase="td_" . $ligneId . "_" . $current_date2;
					$html .= '<td ' . ' id="td_' . $ligneId . '_' . $current_date2 . '"';
					if($user->checkDroit('tasks_modify_all') || $user->checkDroit('tasks_modify_own_project') || $user->checkDroit('tasks_modify_own_task') || $user->checkDroit('tasks_modify_team')) {
						$droitAjoutPeriode = true;
						
						// Cellule en lecture seule si equipe différente et droit de modification de son équipe seulement
						if ( $user->checkDroit('tasks_readonly') || ($user->checkDroit('tasks_modify_team') && isset($ligne['team_id']) && $ligne['team_id'] <> $_SESSION['user_groupe_id']) )
						{
							$dragndropzone = '';
							$classTD.=" read-only";
						}else $dragndropzone = 'ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="leaveDropZone(event);"';
					}else {
						$droitAjoutPeriode = false;
						$dragndropzone = '';
					}
					$html .= ' '. $styleTD. ' class="' . $classTD . (($current_date == date('Y-m-d')) ? ' today' : '') . '" '.$dragndropzone.' >' . CRLF;

					// Si férié, on affiche l'objet férié
					if($ferie !== false) 
					{
						$html .= $ferie;
					}

					$niveauCourant = 0;
					$nbitems=0;
					
					// Affichage de toutes les cellules (boucle)
					foreach ($joursOccupes[$current_date] as $j) 
					{
						$jour=$planning['periodes'][$j];
						$nbitems++;
						// Generation des cellules vides pour aligner les cases d'une meme periode
						if(in_array($jour['periode_id'], $ordreJourPrec) && $niveauCourant != array_search($jour['periode_id'], $ordreJourPrec)) 
						{
							$nbVides = (array_search($jour['periode_id'], $ordreJourPrec)-$niveauCourant);
							for($i=1; $i<=$nbVides; $i++) 
							{
								$html .= '<div class="cellProject cellEmpty" ondrop="drop(event)" ondragleave="leaveDropZone(event);"></div>' . CRLF;
								$niveauCourant++;
							}
							$niveauCourant++;
							$ordreJourCourant[array_search($jour['periode_id'], $ordreJourPrec)] = $jour['periode_id'];
						} else 
						{
							$ordreJourCourant[] = $jour['periode_id'];
							$niveauCourant++;
						}
						// Génération du tooltip
						$jour['tooltip']=create_tooltip($jour);
						// Génération de la cell projet
						$html.=createCellProject($jour);
						
					}
					$ordreJourPrec = $ordreJourCourant;
					$ordreJourCourant = array();

					// Espace vide pour permettre de cliquer en dessous d'une case assignée
					if ($user->checkDroit('tasks_modify_team') && $jour['team_id'] <> $_SESSION['user_groupe_id'])
					{
					}else $html.= '<div class="cellEmpty" ondrop="drop(event)" ondragleave="leaveDropZone(event)" data-parent="'.$idCase.'"></div>';
					$html .= '</td>' . CRLF;
				
				} else {
					if($user->checkDroit('tasks_modify_all') || $user->checkDroit('tasks_modify_own_project') || $user->checkDroit('tasks_modify_own_task')|| $user->checkDroit('tasks_modify_team')) {
						$droitAjoutPeriode = true;
					} else {
						$droitAjoutPeriode = false;
					}

					// Cellule en lecture seule si equipe différente et droit de modification de son équipe seulement
					if ( $user->checkDroit('tasks_readonly') || ($user->checkDroit('tasks_modify_team') && array_key_exists('team_id',$ligne) && $ligne['team_id'] <> $_SESSION['user_groupe_id'])) {						
						$classTD.=" read-only";
						$dragndropzone = '';
					}else {
						$dragndropzone = 'ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="leaveDropZone(event);"';
					}
					
					// Cas d'un jour vide
					$html .= '<td ' . ' id="td_' . $ligneId . '_' . $current_date2 . '"';
					$html .= ' '. $styleTD. ' class="' . $classTD . (($current_date == date('Y-m-d')) ? ' today' : '') . '" '.$dragndropzone.' >';
					if($ferie !== false) 
					{
						$html .= $ferie;
					} else 
					{
						$html .= '';
					}
					$html .= '</td>' . CRLF;
				}
			}
		}
	}

	// Planning Heures
	if ($base_colonne=="heures")
	{
		// Dans le cas d'affichage des jours, on boucle sur toutes les dates
		foreach ($planning['colonnes'] as $cle_colonne) 
		{	
				// Sélection de la clé
				$datePivot = new DateTime($cle_colonne);
				$current_date = $datePivot->format('Y-m-d');
				$current_date2 = $datePivot->format('Ymd');
				$current_week = $datePivot->format('w');
				$styleTD = '';
				// Définition du style pour case semaine et WE
				if (!in_array($current_week, $DAYS_INCLUDED) || array_key_exists($current_date, $joursFeries)) {
					if (array_key_exists($current_date, $joursFeries) && CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
						if (empty($joursFeries[$current_date]['couleur'])) {
							$classTD = 'week feries';
						}else {
							$styleTD = " style='background-color:#".$joursFeries[$current_date]['couleur']."' ";
							$weekend = true;
						}
					}elseif (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
						$classTD = 'week weekend';
						$weekend = true;
					}else {
						continue;
					}
				} else {
					$classTD = 'week';
					$weekend = false;
				}
				
				// Si la date est un jour férié
				$ferie = false;
				if (array_key_exists($current_date, $joursFeries)) 
				{
					$ferie = true;
					$ferieObj = new Ferie();
					if($ferieObj->db_load(array('date_ferie', '=', $current_date)) && trim($ferieObj->libelle) != "") 
					{
						if (CONFIG_PLANNING_MASQUER_FERIES == 0)
						{
							$tooltip = '<b>' . $ferieObj->libelle . '</b>';
							$ferie = '<div class="cellHolidays tooltipster" title="'.$tooltip.'">' . $smarty->getConfigVars('planning_ferie') . '</div>' . CRLF;
						}
					}
				}
				
				// Si la date contient une tâche (jour avec au moins une case remplie)
				foreach ($planning['heures'] as $heure)
				{
					if (isset($joursOccupes[$current_date][$heure]))
					{
						$heure1=str_replace(":","_",$heure);
						$heure2=date('H:i', strtotime($heure.'+1 hour'));
						$current_date2 = $datePivot->format('Ymd')."_".$heure1;
						$niveauCourant = 0;		

						// Affichage de la case
						$html .= '<td ' . ' id="td_' . $ligneId . '_' . $current_date2 . '_'.str_replace(":","_",$heure2).'"';
						if($user->checkDroit('tasks_modify_all') || $user->checkDroit('tasks_modify_own_project') || $user->checkDroit('tasks_modify_own_task')|| $user->checkDroit('tasks_modify_team'))
						{
							$droitAjoutPeriode = true;
						}else {
							$droitAjoutPeriode = false;
						}
						$html .= ' '. $styleTD. ' class="' . $classTD . (($current_date == date('Y-m-d')) ? ' today' : '') . '" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="leaveDropZone(event);">' . CRLF;

						// Si férié, on affiche l'objet férié
						if($ferie !== false) 
						{
							$html .= $ferie;
						}
						
						$niveauCourant = 0;
						if (isset($joursOccupes[$current_date][$heure]))
						{		
								$h=$joursOccupes[$current_date][$heure];
								foreach ($joursOccupes[$current_date][$heure] as $cle_heure)
								{
									$jour=$planning['periodes'][$cle_heure];
									// on checke que la tache couvre la tranche horaire en cours
									//if(!couvreTranche($jour['duree_details'], $heure)) {
									//	continue;
									//}
									
									
									// Generation des cellules vides pour aligner les cases d'une meme periode			
									if(in_array($jour['periode_id'], $ordreJourPrec) && $niveauCourant != array_search($jour['periode_id'], $ordreJourPrec)) 
									{
										$nbVides = (array_search($jour['periode_id'], $ordreJourPrec)-$niveauCourant);
										for($i=1; $i<=$nbVides; $i++) 
										{
											$html .= '<div class="cellProject cellEmpty" ondragleave="leaveDropZone(event);"></div>' . CRLF;
											$niveauCourant++;
										}
										$niveauCourant++;
										$ordreJourCourant[array_search($jour['periode_id'], $ordreJourPrec)] = $jour['periode_id'];
									} else 
									{
										$ordreJourCourant[] = $jour['periode_id'];
										$niveauCourant++;
									}
									
									// Génération du tooltip
									$jour['tooltip']=create_tooltip($jour);
									// Génération de la cell projet
									$html.=createCellProject($jour);
								}
							$ordreJourPrec = $ordreJourCourant;
							$ordreJourCourant = array();

							// Espace vide pour permettre de cliquer en dessous d'une case assignée
							$html.= '<div class="cellEmpty" ondrop="drop(event)" ondragleave="leaveDropZone(event);"></div>';
							$html .= '</td>' . CRLF;
						
						} else {
							
							// Cas d'un jour vide
							$html .= '<td ' . ' id="td_' . $ligneId . '_' . $current_date2 . '_'. str_replace(":","_",$heure2) .'"';
							if($user->checkDroit('tasks_modify_all') || $user->checkDroit('tasks_modify_own_project') || $user->checkDroit('tasks_modify_own_task')|| $user->checkDroit('tasks_modify_team')) {
								$droitAjoutPeriode = true;
							} else {
								$droitAjoutPeriode = false;
							}
							$html .= ' '. $styleTD. ' class="' . $classTD . (($current_date == date('Y-m-d')) ? ' today' : '') . '" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="leaveDropZone(event);">';
							if($ferie !== false) 
							{
								$html .= $ferie;
							} else 
							{
								$html .= '';
							}
							$html .= '</td>' . CRLF;
						}
					
					}else {
							$heure1=str_replace(":","_",$heure);
							$heure2=date('H:i', strtotime($heure.'+1 hour'));
							$current_date2 = $datePivot->format('Ymd')."_".$heure1;
							// Cas d'un jour vide
							$html .= '<td ' . ' id="td_' . $ligneId . '_' . $current_date2 . '_'. str_replace(":","_",($heure2)) .'"';
							if($user->checkDroit('tasks_modify_all') || $user->checkDroit('tasks_modify_own_project') || $user->checkDroit('tasks_modify_own_task')|| $user->checkDroit('tasks_modify_team')) {
								$droitAjoutPeriode = true;

							} else {
								$droitAjoutPeriode = false;
							}
							$html .= ' '. $styleTD. ' class="' . $classTD . (($current_date == date('Y-m-d')) ? ' today' : '') . '" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="leaveDropZone(event);">';
							if($ferie !== false) 
							{
								$html .= $ferie;
							} else 
							{
								//$html .= '&nbsp;';
								$html .= '';
							}
							$html .= '</td>' . CRLF;
						}
				}
		}
	}
	$html .= '</tr>' . CRLF;
}
	////////////////////////////////////////////////////
	// AFFICHAGE DES TOTAUX DE LIGNES
	////////////////////////////////////////////////////
if($afficherLigneTotal == 1) {
	
	// Affichage du libellé
	$html .= '<tr><th id="tdTotal" scope="row">' . $smarty->getConfigVars('tab_totalJour') . '</td>' .CRLF;
	if ($base_ligne=='heures')
	{
		$html .= '<td id="tdTotal2"></td>' .CRLF;
	}
	
	// on boucle sur la durée de l'affichage
	if ($base_colonne<>"heures")
	{
		foreach ($planning['colonnes'] as $cle_colonne) 
		{
			if ($base_colonne=="jours")
			{
				$datePivot = new DateTime($cle_colonne);
				$current_date = $datePivot->format('Y-m-d');
				$current_date2 = $datePivot->format('Ymd');
				$current_week = $datePivot->format('w');
			}
			if ($base_colonne=="users")
			{
				$datePivot = clone $dateDebut;
				$current_date = $cle_colonne;
				$current_date2 = $cle_colonne;
				$current_week = $cle_colonne;
			}
			if ($base_colonne=="heures")
			{
				$datePivot = new DateTime($cle_colonne);
				$current_date = $datePivot->format('Y-m-d');
				$current_date2 = $datePivot->format('Ymd');
				$current_week = $datePivot->format('w');
			}
		
			// définit le style pour case semaine et WE
			$styleTD='';
			if (!in_array($current_week, $DAYS_INCLUDED) || array_key_exists($current_date, $joursFeries)) {
				if (array_key_exists($current_date, $joursFeries) && CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
					if (empty($joursFeries[$current_date]['couleur'])) {
						$classTD = 'feries';
					} else {
						$styleTD = " style='background-color:#".$joursFeries[$current_date]['couleur']."' ";
						$weekend = true;
					}
				} elseif (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
					$classTD = 'weekend';
					$weekend = true;
				} else {
					continue;
				}
			} else {
				$classTD = 'week';
				$weekend = false;
			}

			if( $current_date == date('Y-m-d')) {$classTD .= ' today';}
			if(isset($totalHoraireParJour[$current_date2])) {
				$capitalCharge=$nbRealUsers*convertHourToDecimal(CONFIG_DURATION_DAY);
				if($capitalCharge != 0){
					$ratioCharge=round(decimalHours($totalHoraireParJour[$current_date2])/$capitalCharge,1);
				}else{
					$ratioCharge=0;
				}
				$ratio=round($ratioCharge*10);
				if ($ratio > 10){
					$ratio=11;
				}
				if($dimensionCase=='large'){
					$symboleH1='h/';
					$symboleH2='h';
				}else{
					$symboleH1='/';
					$symboleH2='';
				}
				if($dimensionCase=='large') {
					if($ratio == 0) {
						$html .= '<td '. $styleTD. ' class="' . $classTD . ' sumCell"><div class="sumLargeCell">' . $totalHoraireParJour[$current_date2];
						$html .= '</div><div class="jaugeTD"><div class="jauge0"></div></div></td>' . CRLF;
					} else{
						$html .= '<td '. $styleTD. ' class="' . $classTD . ' sumCell"><div class="sumLargeCell">' . $totalHoraireParJour[$current_date2];
						$html .= '</div><div class="jaugeTD"><div class="jauge0">';
						$html .= '<div class="jauge' . $ratio . '">';
						if ($ratio == 10) {
							$html .= '100';
						}
						$html .= '</div></div></div></td>' . CRLF;
					}
				} else {
					$html .= '<td '. $styleTD. ' class="' . $classTD . ' sumCell">' . $totalHoraireParJour[$current_date2];
					$html .= '</td>' . CRLF;
				}	
			} else {
				$html .= '<td '. $styleTD. ' class="' . $classTD . '"></td>' . CRLF;
			}
		}
	$html .= '</tr>';
	}
	
	// on boucle sur la durée de l'affichage
	if ($base_colonne=="heures")
	{
		$nbheures=count($planning['heures']);
		foreach ($planning['colonnes'] as $cle_colonne) 
		{
			$datePivot = new DateTime($cle_colonne);
			$current_date = $datePivot->format('Y-m-d');
			$current_date2 = $datePivot->format('Ymd');
			$current_week = $datePivot->format('w');
			$styleTD='';
			// définit le style pour case semaine et WE
			if (!in_array($current_week, $DAYS_INCLUDED) || array_key_exists($current_date, $joursFeries)) {
				if (array_key_exists($current_date, $joursFeries)) {
					if (empty($joursFeries[$current_date]['couleur'])) {
						$classTD = 'feries';
					}else {
						$styleTD = " style='background-color:#".$joursFeries[$current_date]['couleur']."' ";
						$weekend = true;
					}
				} elseif (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1)	{
					$classTD = 'weekend';
					$weekend = true;
				} else {
					continue;
				}
			} else {
				$classTD = 'week';
				$weekend = false;
			}

			if( $current_date == date('Y-m-d')) {
				$classTD .= ' today';
			}
			
			if(isset($totalHoraireParJour[$current_date2])) {
				$capitalCharge=$nbRealUsers*convertHourToDecimal(CONFIG_DURATION_DAY);
				if($capitalCharge != 0){
					$ratioCharge=round(decimalHours($totalHoraireParJour[$current_date2])/$capitalCharge,1);
				}else{
					$ratioCharge=0;
				}
				$ratio=round($ratioCharge*10);
				if ($ratio > 10){
					$ratio=11;
				}
				if($dimensionCase=='large'){
					$symboleH1='h/';
					$symboleH2='h';
				}else{
					$symboleH1='/';
					$symboleH2='';
				}
				if($dimensionCase=='large') {
					if($ratio == 0) {
						$html .= '<td '. $styleTD. ' class="' . $classTD . ' sumCell"><div class="sumLargeCell">' . $totalHoraireParJour[$current_date2];
						$html .= '</div><div class="jaugeTD"><div class="jauge0"></div></div></td>' . CRLF;
					} else {
						$html .= '<td '. $styleTD. ' class="' . $classTD . ' sumCell"><div class="sumLargeCell">' . $totalHoraireParJour[$current_date2];
						$html .= '</div><div class="jaugeTD"><div class="jauge0">';
						$html .= '<div class="jauge' . $ratio . '">';
						if ($ratio == 10) {
							$html .= '100';
						}
						$html .= '</div></div></div></td>' . CRLF;
					}
				} else {
					$html .= '<td '. $styleTD. ' colspan="'.$nbheures.'" class="' . $classTD . ' sumCell">' . $totalHoraireParJour[$current_date2];
					$html .= '</td>' . CRLF;
				}	
			} else {
				$html .= '<td '. $styleTD. ' colspan="'.$nbheures.'" class="' . $classTD . '"></td>' . CRLF;
			}
		}
	$html .= '</tr>';
	}
}

if($afficherLigneTotalTaches == 1) {
	
	// Affichage du libellé
	$html .= '<tr><th id="tdTotalTaches" scope="row">' . $smarty->getConfigVars('tab_totalJourTaches') . '</th>' .CRLF;
	if ($base_ligne=='heures')
	{
		$html .= '<td id="tdTotal3"></td>' .CRLF;
	}
	
	// on boucle sur la durée de l'affichage
	if ($base_colonne<>"heures")
	{
		foreach ($planning['colonnes'] as $cle_colonne) 
		{
			if ($base_colonne=="jours")
			{
				$datePivot = new DateTime($cle_colonne);
				$current_date = $datePivot->format('Y-m-d');
				$current_date2 = $datePivot->format('Ymd');
				$current_week = $datePivot->format('w');
			}
			if ($base_colonne=="users")
			{
				$datePivot = clone $dateDebut;
				$current_date = $cle_colonne;
				$current_date2 = $cle_colonne;
				$current_week = $cle_colonne;
			}
			if ($base_colonne=="heures")
			{
				$datePivot = new DateTime($cle_colonne);
				$current_date = $datePivot->format('Y-m-d');
				$current_date2 = $datePivot->format('Ymd');
				$current_week = $datePivot->format('w');
			}
		
			// définit le style pour case semaine et WE
			$styleTD='';
			if (!in_array($current_week, $DAYS_INCLUDED) || array_key_exists($current_date, $joursFeries)) {
				if (array_key_exists($current_date, $joursFeries) && CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
					if (empty($joursFeries[$current_date]['couleur'])) {
						$classTD = 'feries';
					} else {
						$styleTD = " style='background-color:#".$joursFeries[$current_date]['couleur']."' ";
						$weekend = true;
					}
				} elseif (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
					$classTD = 'weekend';
					$weekend = true;
				} else {
					continue;
				}
			} else {
				$classTD = 'week';
				$weekend = false;
			}

			if( $current_date == date('Y-m-d')) {$classTD .= ' today';}
			if(isset($totalNbTachesParJour[$current_date2])) {
				if($dimensionCase=='large') {
					if($ratio == 0) {
						$html .= '<td '. $styleTD. ' class="' . $classTD . ' sumCell"><div class="sumLargeCell">' . $totalNbTachesParJour[$current_date2];
						$html .= '</div><div class="jaugeTD"><div class="jauge0"></div></div></td>' . CRLF;
					}
				} else {
					$html .= '<td '. $styleTD. ' class="' . $classTD . ' sumCell">' . $totalNbTachesParJour[$current_date2];
					$html .= '</td>' . CRLF;
				}	
			} else {
				$html .= '<td '. $styleTD. ' class="' . $classTD . '"></td>' . CRLF;
			}
		}
	$html .= '</tr>';
	}
	
	// on boucle sur la durée de l'affichage
	if ($base_colonne=="heures")
	{
		$nbheures=count($planning['heures']);
		foreach ($planning['colonnes'] as $cle_colonne) 
		{
			$datePivot = new DateTime($cle_colonne);
			$current_date = $datePivot->format('Y-m-d');
			$current_date2 = $datePivot->format('Ymd');
			$current_week = $datePivot->format('w');
			$styleTD='';
			// définit le style pour case semaine et WE
			if (!in_array($current_week, $DAYS_INCLUDED) || array_key_exists($current_date, $joursFeries)) {
				if (array_key_exists($current_date, $joursFeries)) {
					if (empty($joursFeries[$current_date]['couleur'])) {
						$classTD = 'feries';
					}else {
						$styleTD = " style='background-color:#".$joursFeries[$current_date]['couleur']."' ";
						$weekend = true;
					}
				} elseif (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1)	{
					$classTD = 'weekend';
					$weekend = true;
				} else {
					continue;
				}
			} else {
				$classTD = 'week';
				$weekend = false;
			}

			if( $current_date == date('Y-m-d')) {
				$classTD .= ' today';
			}
			
			if(isset($totalNbTachesParJour[$current_date2])) {
				if($dimensionCase=='large') {
					if($ratio == 0) {
						$html .= '<td '. $styleTD. ' class="' . $classTD . ' sumCell"><div class="sumLargeCell">' . $totalNbTachesParJour[$current_date2];
						$html .= '</div><div class="jaugeTD"><div class="jauge0"></div></div></td>' . CRLF;
					}
				} else {
					$html .= '<td '. $styleTD. ' colspan="'.$nbheures.'" class="' . $classTD . ' sumCell">' . $totalNbTachesParJour[$current_date2];
					$html .= '</td>' . CRLF;
				}	
			} else {
				$html .= '<td '. $styleTD. ' colspan="'.$nbheures.'" class="' . $classTD . '"></td>' . CRLF;
			}
		}
	$html .= '</tr>';
	}
}


$html .= '</tbody></table>' . CRLF;

// anchor for show/hide, move the page to be the entire project table
$html .= '<a id="anchorProjectTable"></a>';

////////////////////////////////////////////////////
// AFFICHAGE DU TABLEAU RECAPITULATIF
////////////////////////////////////////////////////
$html_recap="";
if ($_SESSION['afficherTableauRecap']=="1")
{
	include "planning_recap.php";
}

// Assignation du tableau
$smarty->assign('htmlTableau', $html);
// Assignation du tableau récapitulatif
$smarty->assign('htmlRecap', $html_recap);
$smarty->assign('modeAffichage', $_SESSION['planningView']);
$smarty->assign('dimensionCase', $_SESSION['dimensionCase']);
$smarty->assign('baseligne', $base_ligne);
// pour savoir combien de groupes à afficher dans colonne de gauche
$smarty->assign('nbGroupes', ($idGroupeCourant+1));
$smarty->assign('droitAjoutPeriode',$droitAjoutPeriode);
$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));
$smarty->display('www_planning.tpl');