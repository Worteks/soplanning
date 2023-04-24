<?php

@ini_set('memory_limit', '256M');
@set_time_limit(1000);

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

$html = '';
$js = '';


$joursFeries = getJoursFeries();

// PARAMETRES ////////////////////////////////
$dateDebut = initDateTime($_SESSION['date_debut_affiche']);
$dateFin = initDateTime($_SESSION['date_fin_affiche']);

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

// FIN PARAMETRES ////////////////////////////////

$enteteCustom = true;
if($enteteCustom){
	if(CONFIG_SOPLANNING_LOGO != ''){
		$html .= '<a class="navbar-brand navbar-brand-logo mr-auto d-inline-block align-items-center"><img src="upload/logo/' . CONFIG_SOPLANNING_LOGO . '" class="mr-3 logo" /></a>';
	}
	$html .= '&nbsp;&nbsp;&nbsp;<span style="font-size:15px;">';
	if(count($_SESSION['filtreUser']) == 1){
		$userTmp = new User();
		if($userTmp->db_load(array('user_id', '=', $_SESSION['filtreUser'][0]))){
			$html .= xss_protect($userTmp->nom);
		}
	} else{
		$html .= xss_protect(CONFIG_SOPLANNING_TITLE);
	}
	$html .= '</span>';
}

// on se cale sur les mois entiers
$dateDebut->modify('-' . ($dateDebut->format('d') - 1) . ' day');

$tmpDate = clone $dateDebut;
$tmpMois = $smarty->getConfigVars('month_' . $tmpDate->format('n')) . ' ' . $tmpDate->format('Y');



// recuperation des projets couvrant la période, pour le filtre de projets
$projetsFiltre = new GCollection('Projet');
$sql = "SELECT distinct pp.*, pg.nom AS groupe_nom
		FROM planning_projet pp
		INNER JOIN planning_periode pd ON pp.projet_id = pd.projet_id
		LEFT JOIN planning_groupe AS pg ON pp.groupe_id = pg.groupe_id ";
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'équipe de ce user
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
	// on filtre sur les projets dont le user courant est propriétaire ou assigné
	$sql .= " AND (pp.createur_id = " . val2sql($user->user_id) . " OR pd.user_id = " . val2sql($user->user_id) . ")";
}
if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
	// on filtre sur les projets de l'équipe de ce user
	$sql .= " AND pu.user_groupe_id = " . val2sql($user->user_groupe_id);
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
	$sql = "SELECT *
			FROM planning_projet
			WHERE 0=0 ";
	if(count($_SESSION['filtreGroupeProjet']) > 0) {
		$sql.= " AND projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
	}
	if($user->checkDroit('tasks_view_own_projects')) {
		$sql .= " AND projet_id IN ('" . implode("','", $listeProjetsPossibles) . "')";
	}
	$sql .= " ORDER BY livraison";
} else {
	$lines = new GCollection('User');
	$sql = "SELECT * FROM planning_user ";
	if($user->checkDroit('tasks_view_specific_users')) {
		$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = planning_user.user_id AND rou.owner_id = " . val2sql($user->user_id);
	}
	$sql .= "	WHERE visible_planning = 'oui'";
	if(count($_SESSION['filtreUser']) > 0) {
		$sql.= " AND user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
	}
	$sql .= " ORDER BY nom";
}
$lines->db_loadSQL($sql);

// FIN CHARGEMENT DES LIGNES (USERS SI NORMAL, PROJET SI INVERSE)

$joursOccupes = array();

while($lineTmp = $lines->fetch()) {
	if($_SESSION['baseLigne'] == 'projets') {
		$ligneId = $lineTmp->projet_id;
	} else {
		$ligneId = $lineTmp->user_id;
	}

	// on charge les jours occupés pour cette ligne
	$periodes = new GCollection('Periode');
	if($_SESSION['baseLigne'] == 'projets') {
		$sql = "SELECT planning_periode.*, planning_user.*, planning_projet.createur_id, planning_user.couleur as user_couleur, planning_user.nom as user_nom, planning_status.couleur as statut_couleur, planning_status.nom as status_nom, planning_projet.couleur as projet_couleur, planning_projet.nom as projet_nom,pl.nom as lieu_nom, pr.nom as ressource_nom
				FROM planning_periode
				INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id
				INNER JOIN planning_projet ON planning_projet.projet_id = planning_periode.projet_id 
				INNER JOIN planning_status on planning_status.status_id = planning_periode.statut_tache
				LEFT JOIN planning_groupe as pg on planning_projet.groupe_id = pg.groupe_id
				LEFT JOIN planning_lieu as pl on planning_periode.lieu_id = pl.lieu_id
				LEFT JOIN planning_ressource as pr on planning_periode.ressource_id = pr.ressource_id";
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			// on filtre sur les projets de l'équipe de ce user
			$sql .= " INNER JOIN planning_user AS pu ON planning_periode.user_id = pu.user_id ";
		}
		if($user->checkDroit('tasks_view_specific_users')) {
			$sql .= " INNER JOIN planning_right_on_user AS rou ON rou.allowed_id = planning_user.user_id AND rou.owner_id = " . val2sql($user->user_id);
		}
		$sql .= " WHERE planning_periode.projet_id = " . val2sql($ligneId);
	} else {
		$sql = "SELECT planning_periode.*, planning_projet.*, planning_projet.createur_id, planning_user.couleur as user_couleur, planning_user.nom as user_nom, planning_status.couleur as statut_couleur, planning_status.nom as status_nom, planning_projet.couleur as projet_couleur, planning_projet.nom as projet_nom,pl.nom as lieu_nom, pr.nom as ressource_nom
			FROM planning_periode
				INNER JOIN planning_projet ON planning_periode.projet_id = planning_projet.projet_id
				INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id
				INNER JOIN planning_status on planning_status.status_id = planning_periode.statut_tache
				LEFT JOIN planning_groupe as pg on planning_projet.groupe_id = pg.groupe_id
				LEFT JOIN planning_lieu as pl on planning_periode.lieu_id = pl.lieu_id
				LEFT JOIN planning_ressource as pr on planning_periode.ressource_id = pr.ressource_id ";
		if ($user->checkDroit('tasks_view_team_projects') && !is_null($user->user_groupe_id)) {
			// on filtre sur les projets de l'équipe de ce user
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
		// on filtre sur les projets de l'équipe de ce user
		$sql .= " AND pu.user_groupe_id = " . val2sql($user->user_groupe_id);
	}
	if ($user->checkDroit('tasks_view_only_own')) {
		$sql .= " AND planning_periode.user_id = " . val2sql($user->user_id);
	}
	if(count($_SESSION['filtreGroupeProjet']) > 0) {
		$sql.= " AND planning_periode.projet_id IN ('" . implode("','", $_SESSION['filtreGroupeProjet']) . "')";
	}
	if(count($_SESSION['filtreUser']) > 0) {
		$sql.= " AND planning_periode.user_id IN ('" . implode("','", $_SESSION['filtreUser']) . "')";
	}
	if($_SESSION['filtreTexte'] != "") {
		$sql.= " AND (convert(planning_periode.notes using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') . " OR convert(planning_periode.lien using utf8) collate utf8_general_ci LIKE " . val2sql('%' . $_SESSION['filtreTexte'] . '%') ." )";
	}
	$sql.= " ORDER BY planning_periode.date_debut";
	$periodes->db_loadSQL($sql);
	//echo $sql . ' : ' . $periodes->getCount() . '<br>' ;

	$ordreJourPrec = array();

	// pour chaque période de cette ligne, on remplie le tableau des jours occupés
	while ($periode = $periodes->fetch()) {
		$infosJour = $periode->getData();
		if($_SESSION['baseLigne'] == 'projets') {
			$infosJour['projet_nom'] = $lineTmp->nom;
			$infosJour['user_nom'] = $periode->nom;
		} else {
			$infosJour['projet_nom'] = $periode->nom;
			$infosJour['user_nom'] = $lineTmp->nom;
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

	$ordreJourCourant = array();
}

reset($joursOccupes);

$html .= '<table border="0" cellpadding="0" cellspacing="0">' . CRLF;
$html .= '<tr>' . CRLF;

$tmpDate = clone $dateDebut;
while ($tmpDate <= $dateFin) {
	$html .= '<td valign="top">' . CRLF;
	$html .= '<table class="calendarContent">' . CRLF;
	$html .= '<tr>' . CRLF;
	$html .= '<td colspan="3" class="planning_head_day">' . $tmpMois . '</td>' . CRLF;
	$html .= '</tr>' . CRLF;
	while (true) {
		$html .= '<tr>' . CRLF;
		$html .= '<td class="calCell"';
		if (in_array($tmpDate->format('w'), $DAYS_INCLUDED) && !array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			$html .= ' class="calOpenDay"';
		} else {
			$html .= ' class="calCloseDay"';
		}
		$html .= '>' . strtoupper(substr($smarty->getConfigVars('day_' . $tmpDate->format('w')), 0, 1)) . '</td>' . CRLF;
		$html .= '<td class="calCell"';
		if (in_array($tmpDate->format('w'), $DAYS_INCLUDED) && !array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			$html .= ' class="calOpenDay"';
		} else {
			$html .= ' class="calCloseDay"';
		}
		$html .= '>' . $tmpDate->format('j') . '</td>' . CRLF;
		$html .= '<td class="w40" ';
		if (!in_array($tmpDate->format('w'), $DAYS_INCLUDED) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			$html .= ' style="background-color:#e4e8eb" ';
		}

		$html .= '>' . CRLF;
		// on boucle pour afficher les cases de ce jour

		if (isset($joursOccupes[$tmpDate->format('Y-m-d')]) && in_array($tmpDate->format('w'), $DAYS_INCLUDED) && !array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
			foreach ($joursOccupes[$tmpDate->format('Y-m-d')] as $jour) {
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
				$html .= '<div id="c_' . $jour['periode_id'] . '_' . $tmpDate->format('Ymd') . '" class="calCellProject" style="color:' . $couleurTexte . ';background-color:#' . $couleurFond . ';';
				//if($_SESSION['baseLigne'] == 'projets') {
				//	$nom = substr($jour['user_id'], 0, 5);
				//} else {
				$nom = $jour['nom_cellule'];
				//$nom = substr($jour['nom_cellule'], 0, 5);
				//}
				$html .= '">' . $nom . '</div>';

			}

		}

		$html .= '</td>' . CRLF;
		$html .= '</tr>' . CRLF;
		$tmpDate->modify('+1 day');
		if(($smarty->getConfigVars('month_' . $tmpDate->format('n')) . ' ' . $tmpDate->format('Y')) != $tmpMois) {
			$tmpDate2 = clone $tmpDate;
			$tmpDate2->modify('-1 day');
			if($tmpDate2->format('j') < 31) {
				for($i=$tmpDate2->format('j');$i<31;$i++) {
					$html .= '<tr>' . CRLF;
					$html .= '<td class="calDisabledDay">&nbsp;</td>' . CRLF;
					$html .= '<td class="calDisabledDay">&nbsp;</td>' . CRLF;
					$html .= '<td class="calDisabledDay">&nbsp;</td>' . CRLF;
					$html .= '</tr>' . CRLF;
				}
			}
			break;
		}
	}
	$html .= '</table>' . CRLF;
	$html .= '</td>' . CRLF;
	$tmpMois = $smarty->getConfigVars('month_' . $tmpDate->format('n')) . ' ' . $tmpDate->format('Y');
}
$html .= '</tr>' . CRLF;
$html .= '</table>' . CRLF;

if($pdf_orientation == 'paysage') {
	$orientation = 'L';
} else {
	$orientation = 'P';
}
$html = '<page orientation="' . $orientation . '"><style>' . file_get_contents('assets/css/export_pdf_calendrier.css') .  file_get_contents('assets/css/themes/'.CONFIG_SOPLANNING_THEME) .'</style>' . $html . '</page>';

if(isset($_GET['debug'])) {
	echo $html;
	die;
}

use Spipu\Html2Pdf\Html2Pdf;

try
{
	$html = utf8_encode($html);
	$html2pdf = new HTML2PDF($orientation, $pdf_format, 'fr', true);
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->pdf->SetDisplayMode('fullpage');
	$html2pdf->writeHTML($html);
	$html2pdf->Output('soplanning-' . date('Y-m-d-H:i:s') . '.pdf');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}
