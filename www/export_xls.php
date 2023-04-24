<?php

@ini_set('memory_limit', '256M');
@set_time_limit(1000);

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

$html = '';
$js = '';

$joursFeries = getJoursFeries();

// PARAM�TRES ////////////////////////////////
$dateDebut = initDateTime($_SESSION['date_debut_affiche']);
$dateFin = initDateTime($_SESSION['date_fin_affiche']);

$nbLignes = $_SESSION['nb_lignes'];
$pageLignes = $_SESSION['page_lignes'];

if(isset($_GET['pdf_orientation'])) {
	setcookie('pdf_orientation', $_GET['pdf_orientation'], 0, '/');
	$pdf_orientation = $_GET['pdf_orientation'];
} else {
	$pdf_orientation = 'L';
}
if(isset($_GET['pdf_format'])) {
	setcookie('pdf_format', $_GET['pdf_format'], 0, '/');
	$pdf_format = $_GET['pdf_format'];
} else {
	$pdf_format = 'A4';
}

$masquerLigneVide = $_SESSION['masquerLigneVide'];

$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);

// FIN PARAM�TRES ////////////////////////////////

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
		if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
			$sClass = 'weekend';
		} else {
			$tmpDate->modify('+1 day');
			continue;
		}
	}
	/*
	if( $tmpDate->format('Y-m-d') == date('Y-m-d')) {
		$sClass .= ' today';
	}
	*/
	$headerNomJours .= '<th class="' . $sClass . '" style="width:25px;">' . strtoupper(substr($smarty->getConfigVars('day_' . $tmpDate->format('w')), 0, 1)) . '</th>' . CRLF;
	$headerNumeroJours .= '<th class="' . $sClass . '">' . $tmpDate->format('j') . '</th>' . CRLF;

	$nomMoisCourant = $smarty->getConfigVars('month_' . $tmpDate->format('n'));
	if ($nomMoisCourant . ' ' . $tmpDate->format('Y') == $tmpMois) {
	    $colspanMois++;
	} else {
		$headerMois .= '<th colspan="' . $colspanMois . '">' . $tmpMois . '</th>' . CRLF;
		$colspanMois = '1';
		$tmpMois = $nomMoisCourant . ' ' . $tmpDate->format('Y');
	}
	// gestion des semaines
	if ($tmpDate->format('w') == 0) {
		$headerSemaines .= '<th colspan="' . $colspanSemaine . '">' . $smarty->getConfigVars('planning_semaine') . ' ' . $tmpDate->format('W') . '</th>' . CRLF;
		$colspanSemaine = 1;
	} else {
		$colspanSemaine++;
	}
	$tmpDate->modify('+1 day');
}
// on cloture le colspan du mois en cours
$headerMois .= '<th colspan="' . $colspanMois . '">' . $tmpMois . '</th>' . CRLF;
// on cloture le colspan de la semaine en cours
if($colspanSemaine != 1) {
	$headerSemaines .= '<th colspan="' . ($colspanSemaine-1) . '">' . $smarty->getConfigVars('planning_semaine') .  ' ' . $tmpDate->format('W') . '</th>' . CRLF;
}

$html .= '<table border="0" cellpadding="0" cellspacing="1" class="css_tableau">' . CRLF;
$html .= '<tr>' . CRLF;
$html .= '<th id="tdUser_0" rowspan="4"></th>' .CRLF;
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


// recuperation des projets couvrant la p�riode, pour le filtre de projets
$projetsFiltre = new GCollection('Projet');
$sql = "SELECT distinct pp.*, pg.nom AS groupe_nom
		FROM planning_projet pp
		INNER JOIN planning_periode pd ON pp.projet_id = pd.projet_id
		LEFT JOIN planning_groupe AS pg ON pp.groupe_id = pg.groupe_id ";
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'�quipe de ce user
	$sql .= " INNER JOIN planning_user AS pu ON pd.user_id = pu.user_id ";
}
$sql .= "WHERE (
			(pd.date_debut <= '" . $dateDebut->format('Y-m-d') . "'
			AND pd.date_fin >= '" . $dateDebut->format('Y-m-d') . "')
			OR
			(pd.date_debut <= '" . $dateFin->format('Y-m-d') . "'
			AND pd.date_debut >= '" . $dateDebut->format('Y-m-d') . "')
	)";
if($user->checkDroit('tasks_view_own_projects')) {
	// on filtre sur les projets dont le user courant est propri�taire ou assign�
	$sql .= " AND (pp.createur_id = '" . $user->user_id . "' OR pd.user_id = '" . $user->user_id . "')";
}
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'�quipe de ce user
	$sql .= " AND pu.user_groupe_id = " . $user->user_groupe_id;
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

// recuperation de la liste des utilisateurs pour filtre sur users
$usersFiltre = new GCollection('User');
$sql = "SELECT * FROM planning_user WHERE visible_planning = 'oui' ORDER BY nom";
$usersFiltre->db_loadSQL($sql);
$smarty->assign('listeUsers', $usersFiltre->getSmartyData());


// CHARGEMENT DES LIGNES (USERS SI NORMAL, PROJET SI INVERS�)
if($_SESSION['baseLigne'] == 'projets') {
	$lines = new GCollection('Projet');
	$sql = "SELECT planning_projet.*, planning_groupe.nom AS groupe_nom
			FROM planning_projet
			LEFT JOIN planning_groupe ON planning_projet.groupe_id = planning_groupe.groupe_id
			WHERE 0=0 ";
	if(count($_SESSION['filtreGroupeProjet']) > 0) {
		$sql.= " AND planning_projet.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
	}
	if($user->checkDroit('tasks_view_own_projects')) {
		$sql .= " AND planning_projet.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
	}
	if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
		// on filtre sur les projets de l'�quipe de ce user
		$sql .= " AND planning_projet.projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
	}
	$sql .= " ORDER BY " . $_SESSION['triPlanning'];
} else {
	$lines = new GCollection('User');
	$sql = "SELECT planning_user.*, planning_user_groupe.nom AS team_nom
			FROM planning_user
			LEFT JOIN planning_user_groupe ON planning_user.user_groupe_id = planning_user_groupe.user_groupe_id
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
$lines->db_loadSQL($sql);
$nbLignesTotal = $lines->getCount();

// on recupere le nombre de pages pour afficher le pager
$smarty->assign('nbPagesLignes', ceil($nbLignesTotal/$nbLignes));

// FIN CHARGEMENT DES LIGNES (USERS SI NORMAL, PROJET SI INVERS�)


$nbLine = 1;
$groupeCourant = false;
$idGroupeCourant = -1;

while($ligneTmp = $lines->fetch()) {
	if($_SESSION['baseLigne'] == 'projets') {
		$ligneId = $ligneTmp->projet_id;
	} else {
		$ligneId = $ligneTmp->user_id;
	}

	$nbLine++;

	// gestion de l'affichage des groupes (de user ou projet) dans le planning
	if(strpos($_SESSION['triPlanning'], 'groupe_nom') !== FALSE || strpos($_SESSION['triPlanning'], 'team_nom') !== FALSE) {
		if($_SESSION['baseLigne'] == 'projets') {
			if($ligneTmp->groupe_nom !== $groupeCourant) {
				$html .= '<tr>' . CRLF;
				$html .= '<th nowrap="nowrap" style="background-color:#AAAAAA;color:#000000" id="tdUser_' . $idGroupeCourant . '">&nbsp;' . ($ligneTmp->groupe_nom != '' ? xss_protect($ligneTmp->groupe_nom) : $smarty->getConfigVars('planning_pasDeGroupe')) . '&nbsp;' . CRLF;
				$html .= '</th>' . CRLF;
				$tmpDate = clone $dateDebut;
				while ($tmpDate <= $dateFin) {
					$html .= '<td style="background-color:#AAAAAA;">&nbsp;</td>' . CRLF;
					$tmpDate->modify('+1 day');
				}
				$html .= '</tr>' . CRLF;
				$idGroupeCourant--;
			}
			$groupeCourant = $ligneTmp->groupe_nom;
		} else {
			if($ligneTmp->team_nom !== $groupeCourant) {
				$html .= '<tr>' . CRLF;
				$html .= '<th nowrap="nowrap" style="background-color:#AAAAAA;color:#000000;" id="tdUser_' . $idGroupeCourant . '">&nbsp;' . ($ligneTmp->team_nom != '' ? xss_protect($ligneTmp->team_nom) : $smarty->getConfigVars('planning_pasDeTeam')) . '&nbsp;' . CRLF;
				$html .= '</th>' . CRLF;
				$tmpDate = clone $dateDebut;
				while ($tmpDate <= $dateFin) {
					$html .= '<td style="background-color:#AAAAAA;">&nbsp;</td>' . CRLF;
					$tmpDate->modify('+1 day');
				}
				$html .= '</tr>' . CRLF;
				$idGroupeCourant--;
			}
			$groupeCourant = $ligneTmp->team_nom;
		}
	}

	// on charge les jours occup�s pour cette ligne
	$periodes = new GCollection('Periode');
	if($_SESSION['baseLigne'] == 'projets') {
		$sql = "SELECT planning_periode.*, planning_user.*, planning_status.nom as status_nom, planning_projet.nom as projet_nom,pl.nom as lieu_nom, pr.nom as ressource_nom, planning_projet.couleur AS projet_couleur, planning_user.couleur AS user_couleur
				FROM planning_periode
				INNER JOIN planning_projet ON planning_periode.projet_id = planning_projet.projet_id
				INNER JOIN planning_status on planning_status.status_id = planning_periode.statut_tache
				INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id
				LEFT JOIN planning_lieu as pl on planning_periode.lieu_id = pl.lieu_id
				LEFT JOIN planning_ressource as pr on planning_periode.ressource_id = pr.ressource_id ";
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			// on filtre sur les projets de l'�quipe de ce user
			$sql .= " INNER JOIN planning_user AS pu ON planning_periode.user_id = pu.user_id ";
		}
		$sql .= " WHERE planning_periode.projet_id = " . val2sql($ligneId);
	} else {
		$sql = "SELECT planning_periode.*, planning_projet.*, planning_status.nom as status_nom, planning_projet.nom as projet_nom,pl.nom as lieu_nom, pr.nom as ressource_nom, planning_projet.couleur AS projet_couleur, planning_user.couleur AS user_couleur
				FROM planning_periode
				INNER JOIN planning_projet ON planning_periode.projet_id = planning_projet.projet_id
				INNER JOIN planning_status on planning_status.status_id = planning_periode.statut_tache
				INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id
				LEFT JOIN planning_lieu as pl on planning_periode.lieu_id = pl.lieu_id
				LEFT JOIN planning_ressource as pr on planning_periode.ressource_id = pr.ressource_id ";
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			// on filtre sur les projets de l'�quipe de ce user
			$sql .= " INNER JOIN planning_user AS pu ON planning_periode.user_id = pu.user_id ";
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
		$sql .= " AND planning_user.user_groupe_id = " . $user->user_groupe_id;
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
		$sql.= " AND (convert(planning_periode.notes using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR convert(planning_periode.lien using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') ." OR convert(planning_periode.titre using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " )";
	}
	$sql.= " ORDER BY planning_periode.date_debut";
	$periodes->db_loadSQL($sql);
	//echo $sql . ' : ' . $periodes->getCount() . '<br>' ;

	$joursOccupes = array();
	// pour chaque p�riode de cette ligne, on remplie le tableau des jours occup�s
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

	// si option activ�e, on masque la ligne si elle est vide
	if($masquerLigneVide == 1 && count($joursOccupes) == 0) {
		continue;
	}

	// on genere la ligne courante
	$html .= '<tr>' . CRLF;
	$html .= '<th id="tdUser_' . ($nbLine-1) . '" nowrap="nowrap"' . ((!is_null($ligneTmp->couleur) && $ligneTmp->couleur != 'FFFFFF') ? ' style="background-color:#'.$ligneTmp->couleur. ';color:' . buttonFontColor('#' . $ligneTmp->couleur) . '"' : '') . '>&nbsp;' . $ligneTmp->nom . '&nbsp;</th>' . CRLF;
	$tmpDate = clone $dateDebut;
	// on boucle sur la dur�e de l'affichage
	while ($tmpDate <= $dateFin) {
		// d�finit le style pour case semaine et WE
		if (!in_array($tmpDate->format('w'), $DAYS_INCLUDED) || !array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 1) {
				$classTD = 'weekend';
				$opacity = 'filter:alpha(opacity=25);-moz-opacity:.25;opacity:.25';
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

		if (array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			// jours f�ri�s
			$ferie = new Ferie();
			if($ferie->db_load(array('date_ferie', '=', $tmpDate->format('Y-m-d'))) && trim($ferie->libelle) != "") {
				$ferie = '<div class="caseFerie">' . $smarty->getConfigVars('planning_ferie') . '</div>' . CRLF;
			}
		} else {
			$ferie = false;
		}

		if (isset($joursOccupes[$tmpDate->format('Y-m-d')])) {
			$html .= '<td ' . $styleLigne . ' valign="top" id="td_' . $ligneId . '_' . $tmpDate->format('Ymd') . '"';
			$html .= ' class="' . $classTD . '">' . CRLF;

			if($ferie !== false) {
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
				$html .= '<div id="c_' . $jour['periode_id'] . '_' . $tmpDate->format('Ymd') . '" class="caseProjets" style="' . $opacity . ';color:' . $couleurTexte . ';background-color:' . $couleurFond . '"';

				if($jour['statut_tache'] == 'fait' || $jour['statut_tache'] == 'abandon') {
					$html .= 'text-decoration:line-through;';
				}
				$html .= '">';
				if($jour['livrable'] == 'oui') {
					$html .= '<img src="assets/img/pictos/milestone.png" border="0" style="vertical-align:top" />';
				} else {
					if(trim($jour['nom_cellule']) == ''){
						$jour['nom_cellule'] = '- - -';
					}
					$html .= $jour['nom_cellule'];
				}
				$html .= '</div>';
			}
			$html .= '</td>' . CRLF;

		} else {
			$html .= '<td ' . $styleLigne . ' id="td_' . $ligneId . '_' . $tmpDate->format('Ymd') . '"';
			$html .= ' class="' . $classTD . '">';
			if($ferie !== false) {
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

$html = '<page orientation="' . $orientation . '"><style>' . file_get_contents('assets/css/export_pdf.css') . '</style>' . $html . '</page>';


if(isset($_GET['debug'])) {
	echo $html;
	die;
}

// syntax for excel
// http://stackoverflow.com/questions/354476/html-to-excel-how-can-tell-excel-to-treat-columns-as-numbers
// http://cosicimiento.blogspot.fr/2008/11/styling-excel-cells-with-mso-number.html

header('Content-Type: application/vnd.ms-excel');
header('Cache-Control: private, must-revalidate, post-check=0, pre-check=0, max-age=1');
//header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Content-Disposition: inline; filename="' . 'soplanning-' . date('Y-m-d-H:i:s') . '.xls' . '"');
echo $html;
