<?php

@ini_set('memory_limit', '256M');
@set_time_limit(1000);

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

$html = '';
$js = '';

$joursFeries = getJoursFeries();

if (!controlDate($_GET['date_debut_pdf']) || !controlDate($_GET['date_fin_pdf'])) {
	echo $smarty->getConfigVars('feries_dateNonValide');
	die;
}

// PARAM?TRES ////////////////////////////////
$dateDebut = initDateTime($_GET['date_debut_pdf']);
$dateFin = initDateTime($_GET['date_fin_pdf']);

$nbLignes = $_SESSION['nb_lignes'];
$pageLignes = $_SESSION['page_lignes'];

if(isset($_GET['pdf_orientation'])) {
	setcookie('pdf_orientation', $_GET['pdf_orientation'], 0, '/');
	$pdf_orientation = $_GET['pdf_orientation'];
} else {
	$pdf_orientation = 'paysage';
}
if(isset($_GET['pdf_format'])) {
	setcookie('pdf_format', $_GET['pdf_format'], 0, '/');
	$pdf_format = $_GET['pdf_format'];
} else {
	$pdf_format = 'A4';
}

$masquerLigneVide = $_SESSION['masquerLigneVide'];

$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);

// FIN PARAM?TRES ////////////////////////////////

$now = new DateTime();

$dateBoutonInferieur = clone $dateDebut;
$dateBoutonInferieur->modify('-1 month');

$dateBoutonSuperieur = clone $dateDebut;
$dateBoutonSuperieur->modify('+1 month');

$headerMois = '' . CRLF;
$headerSemaines = '' . CRLF;
$headerNomJours = '' . CRLF;
$headerNumeroJours = '' . CRLF;
$colspanMois = '0';
$colspanSemaine = '1';
$tmpDate = clone $dateDebut;
$tmpMois = $smarty->getConfigVars('month_' . $tmpDate->format('n')) . ' ' . $tmpDate->format('Y');


// GESTION DES ENTETES DU TABLEAU (MOIS, SEMAINE ET JOUR)
while ($tmpDate <= $dateFin) {
	if (in_array($tmpDate->format('w'), $DAYS_INCLUDED) && !array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
		$sClass = 'week';
	} else {
		if(CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1){
			$sClass = 'weekend';
		} else{
			$tmpDate->modify('+1 day');
			continue;
		}
	}
	/*
	if( $tmpDate->format('Y-m-d') == date('Y-m-d')) {
		$sClass .= ' today';
	}
	*/
	$headerNomJours .= '<th width="' . CONFIG_PLANNING_COL_WIDTH . '" class="planning_head_day ' . $sClass . '">' . strtoupper(substr($smarty->getConfigVars('day_' . $tmpDate->format('w')), 0, 1)) . '</th>' . CRLF;
	$headerNumeroJours .= '<th width="' . CONFIG_PLANNING_COL_WIDTH . '" class="planning_head_dayname ' . $sClass . '">' . $tmpDate->format('j') . '</th>' . CRLF;

	$nomMoisCourant = $smarty->getConfigVars('month_' . $tmpDate->format('n'));
	if ($nomMoisCourant . ' ' . $tmpDate->format('Y') == $tmpMois) {
	    $colspanMois++;
	} else {
		$headerMois .= '<th class="planning_head_month" colspan="' . $colspanMois . '">' . $tmpMois . '</th>' . CRLF;
		$colspanMois = '1';
		$tmpMois = $nomMoisCourant . ' ' . $tmpDate->format('Y');
	}
	// gestion des semaines
	if ($tmpDate->format('w') == 0) {
		$headerSemaines .= '<th class="planning_head_week" colspan="' . $colspanSemaine . '">' . $smarty->getConfigVars('planning_semaine') . ' ' . $tmpDate->format('W') . '</th>' . CRLF;
		$colspanSemaine = 1;
	} else {
		$colspanSemaine++;
	}
	$tmpDate->modify('+1 day');
}
// on cloture le colspan du mois en cours
$headerMois .= '<th class="planning_head_month" colspan="' . $colspanMois . '">' . $tmpMois . '</th>' . CRLF;
// on cloture le colspan de la semaine en cours
if($colspanSemaine != 1) {
	$headerSemaines .= '<th class="planning_head_week" colspan="' . ($colspanSemaine-1) . '">' . $smarty->getConfigVars('planning_semaine') .  ' ' . $tmpDate->format('W') . '</th>' . CRLF;
}

$html .= '<table class="planningContent">' . CRLF;
$html .= '<tr>' . CRLF;
$html .= '<th id="tdUser_0" rowspan="4" class="planning_switch"></th>' .CRLF;
$html .= $headerMois . CRLF;
$html .= '</tr>' . CRLF;
$html .= '<tr>' . CRLF;
$html .= $headerSemaines . CRLF;
$html .= '</tr>' . CRLF;
$html .= '<tr>' . CRLF;
$html .= $headerNomJours . CRLF;
$html .= '</tr>' . CRLF;
$html .= '<tr>' . CRLF;
$html .= $headerNumeroJours . CRLF;
$html .= '</tr>' . CRLF;
// FIN GESTION DES ENTETES DU TABLEAU (MOIS, SEMAINE ET JOUR)


// recuperation des projets couvrant la p?riode, pour le filtre de projets
$projetsFiltre = new GCollection('Projet');
$sql = "SELECT distinct pp.*, pg.nom AS groupe_nom
		FROM planning_projet pp
		INNER JOIN planning_periode pd ON pp.projet_id = pd.projet_id
		LEFT JOIN planning_groupe AS pg ON pp.groupe_id = pg.groupe_id ";
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'?quipe de ce user
	$sql .= " INNER JOIN planning_user AS pu ON pd.user_id = pu.user_id ";
}
$sql .= "WHERE (
		0=0
	)";
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'?quipe de ce user
	$sql .= " AND (pu.user_groupe_id = " . val2sql($user->user_groupe_id) . " OR pd.createur_id = " . val2sql($user->user_id) . ")";
}
if($user->checkDroit('tasks_view_own_projects')) {
	// on filtre sur les projets dont le user courant est proprietaire ou assigne
	$sql .= " AND (pp.createur_id = " . val2sql($user->user_id) . " OR pd.user_id = " . val2sql($user->user_id) . ")";
}
if ($user->checkDroit('tasks_view_only_own')) {
	$sql .= " AND pd.user_id = " . val2sql($user->user_id);
}
$sql .= "	GROUP BY pp.nom, pp.projet_id
			ORDER BY pp.groupe_id, pp.nom";
$projetsFiltre->db_loadSQL($sql);
$smarty->assign('listeProjets', $projetsFiltre->getSmartyData());
if($user->checkDroit('tasks_view_own_projects')) {
	$listeProjetsPossibles = $projetsFiltre->get('projet_id');
}
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	$listeProjetsPossibles = $projetsFiltre->get('projet_id');
}

// CHARGEMENT DES LIGNES (USERS SI NORMAL, PROJET SI INVERSE)
if($_SESSION['baseLigne'] == 'projets') {
	$lines = new GCollection('Projet');
	$sql = "SELECT planning_projet.*, planning_groupe.nom AS groupe_nom
			FROM planning_projet
			LEFT JOIN planning_groupe ON planning_projet.groupe_id = planning_groupe.groupe_id
			WHERE 0=0 ";
	if(count($_SESSION['filtreGroupeProjet']) > 0) {
		$sql.= " AND projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
	}
	if($user->checkDroit('tasks_view_own_projects')) {
		$sql .= " AND projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
	}
	if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
		// on filtre sur les projets de l'?quipe de ce user
		$sql .= " AND projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
	}
	$sql .= " ORDER BY " . $_SESSION['triPlanning'];
} else {
	$lines = new GCollection('User');
	$sql = "SELECT planning_user.*, planning_user_groupe.nom AS team_nom
			FROM planning_user ";
	if($user->checkDroit('tasks_view_specific_users')) {
		$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = planning_user.user_id AND rou.owner_id = " . val2sql($user->user_id);
	}
	$sql .= "	LEFT JOIN planning_user_groupe ON planning_user.user_groupe_id = planning_user_groupe.user_groupe_id
				WHERE visible_planning = 'oui'";
	if(count($_SESSION['filtreUser']) > 0) {
		$sql.= " AND user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
	}
	if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
		$sql .= " AND planning_user.user_groupe_id = " . val2sql($user->user_groupe_id);
	}
	if ($user->checkDroit('tasks_view_only_own')) {
		$sql .= " AND planning_user.user_id = " . val2sql($user->user_id);
	}
	$sql .= " ORDER BY " . $_SESSION['triPlanning'];
}
//echo $sql;die;
$lines->db_loadSQL($sql);
$nbLignesTotal = $lines->getCount();

// on recupere le nombre de pages pour afficher le pager
$smarty->assign('nbPagesLignes', ceil($nbLignesTotal/$nbLignes));

// FIN CHARGEMENT DES LIGNES (USERS SI NORMAL, PROJET SI INVERS?)

$nbLine = 1;
$groupeCourant = false;
$idGroupeCourant = -1;

while($ligneTmp = $lines->fetch()) {
	if($_SESSION['baseLigne'] == 'projets') {
		$ligneId = $ligneTmp->projet_id;
	} else {
		$ligneId = $ligneTmp->user_id;
	}

	// every 10 lines, repeat days/month/etc rows
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
		if($_SESSION['baseLigne'] == 'projets') {
			if($ligneTmp->groupe_nom !== $groupeCourant) {
				$html .= '<tr>' . CRLF;
				$html .= '<th class="planning_team_div" id="tdUser_' . $idGroupeCourant . '">&nbsp;' . ($ligneTmp->groupe_nom != '' ? xss_protect($ligneTmp->groupe_nom) : $smarty->getConfigVars('planning_pasDeGroupe')) . '&nbsp;' . CRLF;
				$html .= '</th>' . CRLF;
				$tmpDate = clone $dateDebut;
				while ($tmpDate <= $dateFin) {
					$html .= '<td class="planning_team_div">&nbsp;</td>' . CRLF;
					$tmpDate->modify('+1 day');
				}
				$html .= '</tr>' . CRLF;
				$idGroupeCourant--;
			}
			$groupeCourant = $ligneTmp->groupe_nom;
		} else {
			if($ligneTmp->team_nom !== $groupeCourant) {
				$html .= '<tr>' . CRLF;
				$html .= '<th class="planning_team_div" id="tdUser_' . $idGroupeCourant . '">&nbsp;' . ($ligneTmp->team_nom != '' ? xss_protect($ligneTmp->team_nom) : $smarty->getConfigVars('planning_pasDeTeam')) . '&nbsp;' . CRLF;
				$html .= '</th>' . CRLF;
				$tmpDate = clone $dateDebut;
				while ($tmpDate <= $dateFin) {
					$html .= '<td class="planning_team_div">&nbsp;</td>' . CRLF;
					$tmpDate->modify('+1 day');
				}
				$html .= '</tr>' . CRLF;
				$idGroupeCourant--;
			}
			$groupeCourant = $ligneTmp->team_nom;
		}
	}

	// on charge les jours occup?s pour cette ligne
	$periodes = new GCollection('Periode');
	if($_SESSION['baseLigne'] == 'projets') {
		$sql = "SELECT planning_periode.*, planning_user.*, planning_user.couleur as user_couleur, planning_user.nom as user_nom, planning_status.couleur as statut_couleur, planning_status.nom as status_nom, planning_projet.couleur as projet_couleur, planning_projet.nom as projet_nom,pl.nom as lieu_nom, pr.nom as ressource_nom
				FROM planning_periode
				INNER JOIN planning_status on planning_status.status_id = planning_periode.statut_tache
				INNER JOIN planning_projet ON planning_periode.projet_id = planning_projet.projet_id
				INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id
				LEFT JOIN planning_groupe as pg on planning_projet.groupe_id = pg.groupe_id
				LEFT JOIN planning_lieu as pl on planning_periode.lieu_id = pl.lieu_id
				LEFT JOIN planning_ressource as pr on planning_periode.ressource_id = pr.ressource_id";
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			// on filtre sur les projets de l'equipe de ce user
			$sql .= " INNER JOIN planning_user AS pu ON planning_periode.user_id = pu.user_id ";
		}
		if($user->checkDroit('tasks_view_specific_users')) {
			$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = planning_user.user_id AND rou.owner_id = " . val2sql($user->user_id);
		}
		$sql .= " WHERE planning_periode.projet_id = " . val2sql($ligneId);
	} else {
		$sql = "SELECT planning_periode.*, planning_projet.*, planning_user.couleur as user_couleur, planning_user.nom as user_nom, planning_status.couleur as statut_couleur, planning_status.nom as status_nom, planning_projet.couleur as projet_couleur, planning_projet.nom as projet_nom,pl.nom as lieu_nom, pr.nom as ressource_nom
				FROM planning_periode
				INNER JOIN planning_status on planning_status.status_id = planning_periode.statut_tache
				INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id
				INNER JOIN planning_projet ON planning_periode.projet_id = planning_projet.projet_id
				LEFT JOIN planning_groupe as pg on planning_projet.groupe_id = pg.groupe_id
				LEFT JOIN planning_lieu as pl on planning_periode.lieu_id = pl.lieu_id
				LEFT JOIN planning_ressource as pr on planning_periode.ressource_id = pr.ressource_id";
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			// on filtre sur les projets de l'?quipe de ce user
			$sql .= " INNER JOIN planning_user AS pu ON planning_periode.user_id = pu.user_id ";
		}
		if($user->checkDroit('tasks_view_specific_users')) {
			$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = planning_user.user_id AND rou.owner_id = " . val2sql($user->user_id);
		}
		$sql .= " WHERE planning_periode.user_id = " . val2sql($ligneId);
	}
	$sql .= "	AND (
					(planning_periode.date_debut <= '" . $dateDebut->format('Y-m-d') . "' AND planning_periode.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
						OR
					(planning_periode.date_debut <= '" . $dateFin->format('Y-m-d') . "' AND planning_periode.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
						)";
	if($user->checkDroit('tasks_view_own_projects')) {
		$sql .= " AND planning_periode.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
	}
	if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
		$sql .= " AND planning_periode.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
		//$sql .= " AND (planning_user.user_groupe_id = " . val2sql($user->user_groupe_id) . ' OR planning_projet.createur_id = ' . val2sql($user->user_id) . ')';
	}
	if ($user->checkDroit('tasks_view_only_own')) {
		$sql .= " AND planning_periode.user_id = " . val2sql($user->user_id);
	}
	if(count($_SESSION['filtreStatutTache']) > 0) {
		$sql.= " AND planning_periode.statut_tache IN ('" . implode("','", $_SESSION['filtreStatutTache']) . "')";
	}
	if(count($_SESSION['filtreStatutProjet']) > 0) {
	    $sql.= " AND planning_projet.statut IN ('" . implode("','", $_SESSION['filtreStatutProjet']) . "')";
	}
	if(count($_SESSION['filtreGroupeProjet']) > 0) {
		$sql.= " AND planning_periode.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
	}
	if(count($_SESSION['filtreUser']) > 0) {
		$sql.= " AND planning_periode.user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
	}
	if($_SESSION['filtreTexte'] != "") {
		$sql.= " AND (planning_periode.notes LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR planning_periode.lien LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') ." OR planning_periode.titre LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " )";
	}
	$sql.= " ORDER BY planning_periode.date_debut";
	$periodes->db_loadSQL($sql);
	$joursOccupes = array();
	// pour chaque p?riode de cette ligne, on remplie le tableau des jours occup?s
	while ($periode = $periodes->fetch()) {
		$infosJour = $periode->getData();
		if($_SESSION['baseLigne'] == 'projets') {
			$infosJour['projet_nom'] = $ligneTmp->nom;
			$infosJour['user_nom'] = $periode->nom;
		} else {
			$infosJour['projet_nom'] = $periode->nom;
			$infosJour['user_nom'] = $ligneTmp->nom;
		}

		$dateDebut_projet = new DateTime();
		$dateDebut_projet->setDate(substr($periode->date_debut,0,4), substr($periode->date_debut,5,2), substr($periode->date_debut,8,2));

		$dateFin_projet = new DateTime();

		$tmpDate = clone $dateDebut_projet;
		if (is_null($periode->date_fin)) {
			$dateFin_projet = clone $dateDebut_projet;
		}
		else {
			$dateFin_projet->setDate(substr($periode->date_fin,0,4), substr($periode->date_fin,5,2), substr($periode->date_fin,8,2));
		}

		while ($tmpDate <= $dateFin_projet) {
				if (isset($joursOccupes[$tmpDate->format('Y-m-d')])) {
					if(CONFIG_PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY == 0) {
						$tmpArray = $joursOccupes[$tmpDate->format('Y-m-d')];
						$tmpArray[] = $infosJour;
						$joursOccupes[$tmpDate->format('Y-m-d')] = $tmpArray;
					}
				} else {
					$tmpArray = array($infosJour);
					$joursOccupes[$tmpDate->format('Y-m-d')] = $tmpArray;
				}
			$tmpDate->modify('+1 day');
		}
	}

	// si option activ?e, on masque la ligne si elle est vide
	if($masquerLigneVide == 1 && count($joursOccupes) == 0) {
		continue;
	}

	// on genere la ligne courante
	$html .= '<tr>' . CRLF;
	$html .= '<th class="divpeoplePDF" id="tdUser_' . ($nbLine-1) . '" ' . ((!is_null($ligneTmp->couleur) && $ligneTmp->couleur != 'FFFFFF') ? ' style="background-color:#'.$ligneTmp->couleur. ';color:' . buttonFontColor('#' . $ligneTmp->couleur) . '"' : '') . '>&nbsp;' . $ligneTmp->nom . '&nbsp;</th>' . CRLF;
	$tmpDate = clone $dateDebut;
	// on boucle sur la dur?e de l'affichage
	while ($tmpDate <= $dateFin) {
		$styleTD = '';
		// d?finit le style pour case semaine et WE
		if (!in_array($tmpDate->format('w'), $DAYS_INCLUDED) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
				$current_date=$tmpDate->format('Y-m-d');
				if (empty($joursFeries[$current_date]['couleur'])) {
					$classTD = 'feries';
				}else {
					$styleTD = " style='background-color:#".$joursFeries[$current_date]['couleur']."' ";
					$opacity = 'filter:alpha(opacity=25);-moz-opacity:.25;opacity:.25';
				}
			} else {
				$tmpDate->modify('+1 day');
				continue;
			}
		} else {
			$classTD = 'week';
			$opacity = '';
		}
		if(CONFIG_PLANNING_LINE_HEIGHT > 0) {
			$styleLigne = ' style="height:' . CONFIG_PLANNING_LINE_HEIGHT . ';"';
		} else {
			$styleLigne = '';
		}

		if (CONFIG_PLANNING_MASQUER_FERIES == 0 && array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			// jours feries
			$ferieObj = new Ferie();
			if($ferieObj->db_load(array('date_ferie', '=', $tmpDate->format('Y-m-d'))) && trim($ferieObj->libelle) != "") {
				$ferie = '<div class="cellHolidays">' . $smarty->getConfigVars('planning_ferie') . '</div>' . CRLF;
			}
		} else {
			$ferie = false;
		}
		if (isset($joursOccupes[$tmpDate->format('Y-m-d')])) {
			$html .= '<td ' . $styleLigne . ' id="td_' . $ligneId . '_' . $tmpDate->format('Ymd') . '"';
			$html .= ' '.$styleTD.' class="' . $classTD . '">' . CRLF;

			if(isset($ferie) && $ferie !== false) {
				$html .= $ferie;
			}
			
			
			// si il y a des periodes pour le jour courant, on boucle pour toutes les afficher
			foreach ($joursOccupes[$tmpDate->format('Y-m-d')] as $jour) {
				// Calcul de la couleur du texte dans la case, selon la couleur de fond de la case
				if( $_SESSION['baseLigne']=='projets') {
					$jour['nom_cellule']=$jour['user_id'];
					$jour['couleur']=$jour['user_couleur'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_PROJET;
				}
				if( $_SESSION['baseLigne']=='users') {
					$jour['nom_cellule']=$jour['projet_id'];
					$jour['couleur']=$jour['projet_couleur'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_PERSONNE;
				}
				if( $_SESSION['baseLigne']=='lieux') {
					$jour['nom_cellule']=$jour['projet_id'];
					$jour['couleur']=$jour['projet_couleur'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_LIEU;
				}
				if( $_SESSION['baseLigne']=='ressources') {
					$jour['nom_cellule']=$jour['projet_id'];
					$jour['couleur']=$jour['projet_couleur'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE;
				}
				if( $_SESSION['baseLigne']=='heures') {
					$jour['nom_cellule']=$jour['projet_id'];
					$jour['couleur']=$jour['projet_couleur'];
					$type_cellule=CONFIG_PLANNING_TEXTE_TACHES_PROJET;
				}
				switch($type_cellule)
				{
					case 'code_projet': $jour['nom_cellule']=$jour['projet_id'];break;
					case 'code_personne': $jour['nom_cellule']=$jour['user_id'];break;
					case 'code_lieu': $jour['nom_cellule']=$jour['lieu_id'];break;
					case 'code_ressource': $jour['nom_cellule']=$jour['ressource_id'];break;
					case 'nom_projet': $jour['nom_cellule']=$jour['projet_nom'];break;
					case 'nom_personne': $jour['nom_cellule']=$jour['user_nom'];break;
					case 'nom_lieu': $jour['nom_cellule']=$jour['lieu_nom'];break;
					case 'nom_ressource': $jour['nom_cellule']=$jour['ressource_nom'];break;
					case 'nom_tache': $jour['nom_cellule']=$jour['titre'];break;
					case 'vide': $jour['nom_cellule']=" ";break;
				}
				if (CONFIG_PLANNING_COULEUR_TACHE == 0)
				{
					$couleurFond = $jour['couleur'];
				}else
				{
					$couleurFond = $jour['statut_couleur'];
				}
				if($couleurFond =='FFFFFF')
				{
					$couleurFond = 'fafafa';
				}
				// couleur du texte dans la case, selon la couleur de fond de la case
				$couleurTexte = buttonFontColor('#' . $couleurFond);
				// la case avec le code du projet
				$html .= '<div id="c_' . $jour['periode_id'] . '_' . $tmpDate->format('Ymd') . '" class="cellProject" style="color:' . $couleurTexte . ';' . $opacity . ';background-color:#' . $couleurFond . ';';

				if($jour['statut_tache'] == 'fait' || $jour['statut_tache'] == 'abandon') {
					$html .= 'text-decoration:line-through;';
				}
				$nom = $jour['nom_cellule'];
				$html .= '">';
				if($jour['livrable'] == 'oui') {
					$html .= '<img src="assets/img/pictos/milestone.png" class="picto-milestone" />';
				} else {
					if(trim($nom) == ''){
						$nom = '- - -';					
					}
					$html .= substr($nom, 0, CONFIG_PLANNING_CODE_WIDTH);
				}
				$html .= '</div>';
			}
			$html .= '</td>' . CRLF;

		} else {
			$html .= '<td ' . $styleLigne . ' id="td_' . $ligneId . '_' . $tmpDate->format('Ymd') . '"';
			$html .= ' '.$styleTD.' class="' . $classTD . '">';
			if(isset($ferie) && $ferie !== false) {
				$html .= $ferie;
			}
			$html .= '</td>' . CRLF;
		}
		$tmpDate->modify('+1 day');
	}
	$html .= '</tr>' . CRLF;
}
$html .= '</table>' . CRLF;

if($pdf_orientation == 'paysage') {
	$orientation = 'L';
} else {
	$orientation = 'P';
}

$html = '<page orientation="' . $orientation . '"><style>' . file_get_contents('assets/css/themes/'.CONFIG_SOPLANNING_THEME) . file_get_contents('assets/css/export_pdf.css')  .'</style>' . $html . '</page>';

if(isset($_GET['cb_inclure_recap'])) {
	$html .= '<page pageset="old"><style>' . file_get_contents('assets/css/export_pdf.css') . '</style>';

	if($_SESSION['baseLigne'] == 'projets') {
		//////////////////////////
		// TABLEAU RECAP DES PROJETS
		//////////////////////////
		$html .= '<table border="0" id="divProjectTable" class="table-pdf" ' . (isset($_COOKIE['divProjectTable']) && $_COOKIE['divProjectTable'] == 'none' ? 'style="display:none;"' : '') . ' width="' . ($pdf_orientation == 'paysage' ? 700 : 480) . '" border="1">' . CRLF;
		$html .= '	<tr>' . CRLF;
		$html .= '		<td class="w70">' . $smarty->getConfigVars('tab_code') . '</td>' . CRLF;
		$html .= '		<td>' . $smarty->getConfigVars('tab_projet2') . '</td>' . CRLF;
		$html .= '		<td>' . $smarty->getConfigVars('tab_periode2') . '</td>' . CRLF;
		$html .= '		<td class="w140">' . $smarty->getConfigVars('tab_charge') . '</td>' . CRLF;
		$html .= '	</tr>' . CRLF;
		// recuperation des projets couvrant la p?riode, pour le filtre de projets
		$projets = new GCollection('Projet');
		$sql= "SELECT distinct pp.*, pg.nom AS groupe_nom
			FROM planning_projet pp
			INNER JOIN planning_periode pd ON pp.projet_id = pd.projet_id
			LEFT JOIN planning_groupe AS pg ON pp.groupe_id = pg.groupe_id ";
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			// on filtre sur les projets de l'?quipe de ce user
			$sql .= " INNER JOIN planning_user AS pu ON pd.user_id = pu.user_id ";
		}
		$sql .= " WHERE (
				(pd.date_debut <= '" . $dateDebut->format('Y-m-d') . "'
				AND pd.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
				OR
				(pd.date_debut <= '" . $dateFin->format('Y-m-d') . "'
				AND pd.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
			)";
		if(count($_SESSION['filtreGroupeProjet']) > 0) {
			$sql .= " AND pp.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
		}
		if(count($_SESSION['filtreUser']) > 0) {
			$sql.= " AND pd.user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
		}
		if($_SESSION['filtreTexte'] != "") {
			$sql.= " AND (pd.notes LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR pd.lien LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') ." )";
		}
		if($user->checkDroit('tasks_view_own_projects')) {
			$sql .= " AND pp.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
		}
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			$sql .= " AND pd.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
		}
	if ($user->checkDroit('tasks_view_only_own')) {
		$sql .= " AND pd.user_id = " . val2sql($user->user_id);
	}
		$sql .= "	GROUP BY pp.nom, pp.projet_id
					ORDER BY pp.groupe_id, pp.nom";
		$projets->db_loadSQL($sql);
		while($projet = $projets->fetch()) {
			$html .= '	<tr>' . CRLF;
			$couleurTexte = buttonFontColor('#' . $projet->couleur);
			$html .= '<td class="w50" style="background:#' . $projet->couleur . ';color:' . $couleurTexte .';">' . $projet->projet_id . '</td>' . CRLF;
			$html .= '<td><b>' . $projet->nom . '</b>' . (!is_null($projet->iteration) ? '<br />' . $projet->iteration : '') . "</td>";

			$html .= '<td>';
		// on charge les periodes liees ? ce projet
			$periodes = new GCollection('Periode');
			$sql = "SELECT pp.*
					FROM planning_periode AS pp
					INNER JOIN planning_user ON planning_user.user_id = pp.user_id ";
			if($user->checkDroit('tasks_view_specific_users')) {
				$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = planning_user.user_id AND rou.owner_id = " . val2sql($user->user_id);
			}
			$sql .= "	WHERE planning_user.visible_planning = 'oui'
						AND projet_id = " . val2sql($projet->projet_id) . "
						AND (
							(pp.date_debut <= '" . $dateDebut->format('Y-m-d') . "' AND pp.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
							OR (pp.date_debut <= '" . $dateFin->format('Y-m-d') . "' AND pp.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
							)";
			if(count($_SESSION['filtreUser']) > 0) {
				$sql.= " AND pp.user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
			}
			if($_SESSION['filtreTexte'] != "") {
				$sql.= " AND (pp.notes LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR pp.lien LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') ." )";
			}
		if ($user->checkDroit('tasks_view_only_own')) {
			$sql .= " AND planning_user.user_id = " . val2sql($user->user_id);
		}
			$sql .= " ORDER BY pp.date_debut";
			//echo $sql . '<br>';
			$periodes->db_loadSQL($sql);

			// si aucune p?riode dispo pour ce projet (par exemple si user non visible) on masque le projet
			if($periodes->getCount() == 0) {
				continue;
			}

			$totalJours = 0;
			$totalJoursPassed = 0;
			$totalHeures = "00:00";
			$totalHeuresPassed = "00:00";
			while ($periode = $periodes->fetch()) {
				$html .= '<div>';
				$html .= '<b>' . $smarty->getConfigVars('date_duree') . '</b> : ';
				if (is_null($periode->date_fin)) {
					$html .= sqldate2userdate($periode->date_debut) . ' => ' . sqltime2usertime($periode->duree) . ' (' . $periode->user_id . ')';
				} else {
					$html .= sqldate2userdate($periode->date_debut) . ' => ' . sqldate2userdate($periode->date_fin) . ' (' . $periode->user_id . ')';
				}
				  if (!is_null($periode->titre)) {
					$html .= '<br><b>' . $smarty->getConfigVars('winPeriode_titre') . '</b> : ' . xss_protect($periode->titre);
				  }
				  if (!is_null($periode->notes)) {
					$html .= '<br><b>' . $smarty->getConfigVars('winPeriode_commentaires') . '</b> : ' .  xss_protect($periode->notes);
				  }
				  if (!is_null($periode->lien)) {
					$html .= '<br><b>' . $smarty->getConfigVars('winPeriode_lien') . '</b> : <a href="' . xss_protect($periode->lien) . '" target="_blank">' . $smarty->getConfigVars('tab_lien') . '</a>';
				  }
				$html .= '</div>';

				$date1 = new DateTime();
				$date1->setDate(substr($periode->date_debut,0,4), substr($periode->date_debut,5,2), substr($periode->date_debut,8,2));

				// on additionne les jours de travail
				if(!is_null($periode->date_fin)) {
					$date2 = new DateTime();
					$date2->setDate(substr($periode->date_fin,0,4), substr($periode->date_fin,5,2), substr($periode->date_fin,8,2));
					while ($date1 <= $date2) {
						// on ne compte pas le jour si c'est WE ou jour f?ri?
						if (in_array($date1->format('w'), $DAYS_INCLUDED) && !array_key_exists($date1->format('Y-m-d'), $joursFeries)) {
							$totalJours +=1;
							if($date1 < $now) {
								$totalJoursPassed +=1;
							}
						}
						$date1->modify('+1 day');
					}
				} else {
					$totalHeures = ajouterDuree($totalHeures, $periode->duree);
					if($date1 < $now) {
						$totalHeuresPassed = ajouterDuree($totalHeuresPassed, $periode->duree);
					}

				}
			}

			$html .= '</td>' . CRLF;
			$html .= '<td>' . CRLF;
			if(!is_null($projet->charge)) {
				$html .= $smarty->getConfigVars('tab_chargeProjet') . ' : ' . $projet->charge . $smarty->getConfigVars('tab_j') . '<br />' . CRLF;
			}
			$nbJourTot=0;
			$config = new Config();
			$config->db_load(array('cle', '=', 'DURATION_DAY'));
			$TotalHeureExplode = explode (':',$totalHeures);
			$TotalHeureH=$TotalHeureExplode[0];
			$TotalHeureM=$TotalHeureExplode[1];
			if($totalHeures != '00:00') {
				$nbJourTot = round (($TotalHeureH+$TotalHeureM/60)/$config->valeur,2);
			}
			$nbHeuresTotal = ($totalJours*$config->valeur+$TotalHeureH).'h'.($TotalHeureM!="00"?($TotalHeureM):"");
			$html .= "<b>". $smarty->getConfigVars('tab_total') . ' : '  . ($totalJours+$nbJourTot) .$smarty->getConfigVars('tab_j'). " ( = ".$nbHeuresTotal.") </b>" . CRLF;

			$html .= '<br />' . CRLF;
			$nbJourTotPassed=0;
			$TotalHeurePassedH=0;
			$TotalHeurePassedM=0;
			if($totalHeuresPassed > 0) {
				$TotalHeurePassedExplode = explode (':',$totalHeuresPassed);
				$TotalHeurePassedH=$TotalHeurePassedExplode[0];
				$TotalHeurePassedM=$TotalHeurePassedExplode[1];
				$nbJourTotPassed = round (($TotalHeurePassedH+$TotalHeurePassedM/60)/$config->valeur,2);
			}
			if($totalJoursPassed > 0 || $totalHeuresPassed > 0) {
				$nbHeuresTotalPassed = (($totalJoursPassed*$config->valeur)+$TotalHeurePassedH).'h'.($TotalHeurePassedM!="00"?($TotalHeurePassedM):"");
				$html .= $smarty->getConfigVars('tab_passe') . ' : ' . ($totalJoursPassed+$nbJourTotPassed) .$smarty->getConfigVars('tab_j'). " ( = ".$nbHeuresTotalPassed." / ".round(($totalJoursPassed+$nbJourTotPassed)/($totalJours+$nbJourTot)*100,1) ."% ) " . CRLF;

			}
			$html .= '</td>' . CRLF;
			$html .= '	</tr>' . CRLF;
		}
		$html .= '</table>' . CRLF;

	} else {

		//////////////////////////
		// TABLEAU RECAP DES USERS
		//////////////////////////
		$html .= '<table border="0" id="divProjectTable" cellspacing="1" cellpadding="3" class="table-pdf" width="' . ($pdf_orientation == 'paysage' ? 700 : 480) . '" border="1">' . CRLF;
		$html .= '	<tr>' . CRLF;
		$html .= '		<td class="w50">' . $smarty->getConfigVars('tab_code') . '</td>' . CRLF;
		$html .= '		<td>' . $smarty->getConfigVars('tab_personne') . '</td>' . CRLF;
		$html .= '		<td>' . $smarty->getConfigVars('tab_periode2') . '</td>' . CRLF;
		$html .= '		<td class="w140">' . $smarty->getConfigVars('tab_charge') . '</td>' . CRLF;
		$html .= '	</tr>' . CRLF;

		// recuperation des personnes
		$users = new GCollection('User');
		$sql= "SELECT *
				FROM planning_user ";
		if($user->checkDroit('tasks_view_specific_users')) {
			$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = planning_user.user_id AND rou.owner_id = " . val2sql($user->user_id);
		}
		$sql .= "	WHERE visible_planning = 'oui' ";
		if(count($_SESSION['filtreUser']) > 0) {
			$sql.= " AND user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
		}
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			$sql .= " AND planning_user.user_groupe_id = " . val2sql($user->user_groupe_id);
		}
	if ($user->checkDroit('tasks_view_only_own')) {
		$sql .= " AND planning_user.user_id = " . val2sql($user->user_id);
	}
		if(strpos($_SESSION['triPlanning'], 'nom') !== FALSE) {
			$sql .= "	ORDER BY nom ASC";
		} else {
			$sql .= "	ORDER BY nom DESC";
		}
		$users->db_loadSQL($sql);

		while($userTemp = $users->fetch()) {
			$html .= '	<tr>' . CRLF;
			$couleurTexte = buttonFontColor('#' . $userTemp->couleur);
			$html .= '<td class="w50" style="background:#' . $userTemp->couleur . ';color:' . $couleurTexte .';">' . $userTemp->user_id . '</td>' . CRLF;
			$html .= '<td><b>' . $userTemp->nom . '</b></td>';

			$html .= '<td class="vbottom">';
			// on charge les p?riodes li?es aux projets
			$periodes = new GCollection('Periode');
			$sql = "SELECT pp.*
					FROM planning_periode AS pp
					INNER JOIN planning_user ON planning_user.user_id = pp.user_id
					WHERE planning_user.visible_planning = 'oui'
					AND pp.user_id = " . val2sql($userTemp->user_id) . "
					AND (
						(pp.date_debut <= '" . $dateDebut->format('Y-m-d') . "' AND pp.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
						OR (pp.date_debut <= '" . $dateFin->format('Y-m-d') . "' AND pp.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
						)";
			if(count($_SESSION['filtreUser']) > 0) {
				$sql.= " AND pp.user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
			}
			if($_SESSION['filtreTexte'] != "") {
				$sql.= " AND (convert(pp.notes using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR convert(pp.lien using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') ." OR convert(pp.titre using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR convert(pp.custom using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR pp.projet_id LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR convert(pp.user_id using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " )";
			}
			if(count($_SESSION['filtreGroupeProjet']) > 0) {
				$sql .= " AND pp.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
			}
			if($user->checkDroit('tasks_view_own_projects')) {
				$sql .= " AND pp.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
			}
			if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
				$sql .= " AND pp.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
			}
		if ($user->checkDroit('tasks_view_only_own')) {
			$sql .= " AND pp.user_id = " . val2sql($user->user_id);
		}
			$sql .= " ORDER BY pp.date_debut";
			//echo $sql . '<br>';
			$periodes->db_loadSQL($sql);


			$totalJours = 0;
			$totalJoursPassed = 0;
			$totalHeures = "00:00";
			$totalHeuresPassed = "00:00";
			while ($periode = $periodes->fetch()) {
				$html .= '<div>';
				$html .= '<b>' . $smarty->getConfigVars('date_duree') . '</b> : ';
				if (is_null($periode->date_fin)) {
					$html .= sqldate2userdate($periode->date_debut) . ' => ' . sqltime2usertime($periode->duree);
					$testHeures = $periode->getHeureDebutFin();
					if(!is_null($testHeures)) {
						$html .= ' (' . sqltime2usertime($testHeures['duree_details_heure_debut']) . ' => ' . sqltime2usertime($testHeures['duree_details_heure_fin']) . ')';
					}
					if($periode->duree_details == 'AM') {
						$html .= ' (' . $smarty->getConfigVars('tab_matin') . ')';
					}
					if($periode->duree_details == 'PM') {
						$html .= ' (' . $smarty->getConfigVars('tab_apresmidi') . ')';
					}
					$html .= ' (' . $periode->projet_id . ')';
				} else {
					$html .= sqldate2userdate($periode->date_debut) . ' => ' . sqldate2userdate($periode->date_fin) . ' (' . $periode->projet_id . ')';
				}
				  if (!is_null($periode->titre)) {
					$html .= '<br><b>' . $smarty->getConfigVars('winPeriode_titre') . '</b> : ' . xss_protect($periode->titre);
				  }
				  if (!is_null($periode->notes)) {
					$html .= '<br><b>' . $smarty->getConfigVars('winPeriode_commentaires') . '</b> : ' .  xss_protect($periode->notes);
				  }
				  if (!is_null($periode->lien)) {
					$html .= '<br><b>' . $smarty->getConfigVars('winPeriode_lien') . '</b> : <a href="' . xss_protect($periode->lien) . '" target="_blank">' . $smarty->getConfigVars('tab_lien') . '</a>';
				  }
				$html .= '</div>';

				$date1 = new DateTime();
				$date1->setDate(substr($periode->date_debut,0,4), substr($periode->date_debut,5,2), substr($periode->date_debut,8,2));

				// on additionne les jours de travail
				if(!is_null($periode->date_fin)) {
					$date2 = new DateTime();
					$date2->setDate(substr($periode->date_fin,0,4), substr($periode->date_fin,5,2), substr($periode->date_fin,8,2));
					while ($date1 <= $date2) {
						// on ne compte pas le jour si c'est WE ou jour f?ri?
						if (in_array($date1->format('w'), $DAYS_INCLUDED) && !array_key_exists($date1->format('Y-m-d'), $joursFeries)) {
							$totalJours +=1;
							if($date1 < $now) {
								$totalJoursPassed +=1;
							}
						}
						$date1->modify('+1 day');
					}
				} else {
					$totalHeures = ajouterDuree($totalHeures, $periode->duree);
					if($date1 < $now) {
						$totalHeuresPassed = ajouterDuree($totalHeuresPassed, $periode->duree);
					}

				}
			}

			$html .= '</td>' . CRLF;
			$html .= '<td valign="top">' . CRLF;

			$nbJourTot=0;
			$config = new Config();
			$config->db_load(array('cle', '=', 'DURATION_DAY'));
			$TotalHeureExplode = explode (':',$totalHeures);
			$TotalHeureH=$TotalHeureExplode[0];
			$TotalHeureM=$TotalHeureExplode[1];
			if($totalHeures != '00:00') {
				$nbJourTot = round (($TotalHeureH+$TotalHeureM/60)/$config->valeur,2);
			}
			$nbHeuresTotal = ($totalJours*$config->valeur+$TotalHeureH).'h'.($TotalHeureM!="00"?($TotalHeureM):"");
			$html .= "<b>". $smarty->getConfigVars('tab_total') . ' : '  . ($totalJours+$nbJourTot) .$smarty->getConfigVars('tab_j'). " ( = ".$nbHeuresTotal.") </b>" . CRLF;

			$html .= '<br />' . CRLF;
			$nbJourTotPassed=0;
			$TotalHeurePassedH=0;
			$TotalHeurePassedM=0;
			if($totalHeuresPassed > 0) {
				$TotalHeurePassedExplode = explode (':',$totalHeuresPassed);
				$TotalHeurePassedH=$TotalHeurePassedExplode[0];
				$TotalHeurePassedM=$TotalHeurePassedExplode[1];
				$nbJourTotPassed = round (($TotalHeurePassedH+$TotalHeurePassedM/60)/$config->valeur,2);
			}
			if($totalJoursPassed > 0 || $totalHeuresPassed > 0) {
				$nbHeuresTotalPassed = (($totalJoursPassed*$config->valeur)+$TotalHeurePassedH).'h'.($TotalHeurePassedM!="00"?($TotalHeurePassedM):"");
				$html .= $smarty->getConfigVars('tab_passe') . ' : ' . ($totalJoursPassed+$nbJourTotPassed) .$smarty->getConfigVars('tab_j'). " ( = ".$nbHeuresTotalPassed." / ".round(($totalJoursPassed+$nbJourTotPassed)/($totalJours+$nbJourTot)*100,1) ."% ) " . CRLF;

			}

			$html .= '</td>' . CRLF;
			$html .= '</tr>' . CRLF;
		}

		$html .= '</table>' . CRLF;
	}

	$html .= '</page>';
}

if(isset($_GET['debug'])) {
	echo $html;
	die;
}

// syntax for excel
// http://stackoverflow.com/questions/354476/html-to-excel-how-can-tell-excel-to-treat-columns-as-numbers
// http://cosicimiento.blogspot.fr/2008/11/styling-excel-cells-with-mso-number.html

use Spipu\Html2Pdf\Html2Pdf;

try
{
//	$html2pdf = new HTML2PDF($orientation, $pdf_format, 'fr', true, 'iso-8859-1');
	$html = utf8_encode($html);
	$html2pdf = new HTML2PDF($orientation, $pdf_format, 'fr', true);
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->pdf->SetDisplayMode('fullpage');
//      $html2pdf->pdf->SetProtection(array('print'), 'spipu');
	$html2pdf->writeHTML($html);
	$html2pdf->Output('soplanning-' . date('Y-m-d-H:i:s') . '.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}
