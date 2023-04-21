<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

if(!$user->checkDroit('stats_projects')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: index.php');
	exit;
}


require_once (BASE . '/../jpgraph/src/jpgraph.php');
require_once (BASE . '/../jpgraph/src/jpgraph_bar.php');
require_once (BASE . '/../jpgraph/src/jpgraph_line.php');

$joursFeries = getJoursFeries();
$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);

$dateDebutGraphe = new DateTime();
$dateDebutGraphe->setDate(substr($_SESSION['stats_projects']['date_debut'],6,4), substr($_SESSION['stats_projects']['date_debut'],3,2), substr($_SESSION['stats_projects']['date_debut'],0,2));
$dateFinGraphe = clone $dateDebutGraphe;
$dateFinGraphe->setDate(substr($_SESSION['stats_projects']['date_fin'],6,4), substr($_SESSION['stats_projects']['date_fin'],3,2), substr($_SESSION['stats_projects']['date_fin'],0,2));

$projets = new GCollection('Projet');
$sql = "SELECT *
		FROM planning_projet
		WHERE 0 = 0
		";
if(count($_SESSION['stats_projects']['projets']) > 0) {
	$sql .= " AND projet_id IN ('" . implode("','", $_SESSION['stats_projects']['projets']) . "')";
}
$sql .= " ORDER BY nom";
$projets->db_loadSQL($sql);
$projetsTab = $projets->get('projet_id');

$donnees = array();
$dateTmpGraphe = clone $dateDebutGraphe;

while($dateTmpGraphe <= $dateFinGraphe) {
	if($_SESSION['stats_projects']['abscisse_echelle'] == 'jour') {
		$interval = 'day';
		$donnees[$dateTmpGraphe->format('d/m/y')] = array();
	} elseif ($_SESSION['stats_projects']['abscisse_echelle'] == 'semaine') {
		$interval = 'week';
		$donnees[$smarty->getConfigVars('planning_semaine') .  $dateTmpGraphe->format('W Y')] = array();
	} elseif ($_SESSION['stats_projects']['abscisse_echelle'] == 'mois') {
		$interval = 'month';
		$donnees[$dateTmpGraphe->format('m/y')] = array();
	}
	$dateTmpGraphe->modify('+ 1 ' . $interval);
}

$periodes = new GCOllection('Periode');
$sql = "SELECT ppe.*
		FROM planning_periode AS ppe
		INNER JOIN planning_projet AS proj ON proj.projet_id = ppe.projet_id
		LEFT JOIN planning_user AS pu ON pu.user_id = ppe.user_id
		WHERE 0 = 0
		AND 
		(
			(date_debut <= '" . $dateDebutGraphe->format('Y-m-d') . "' AND date_fin >= '" . $dateDebutGraphe->format('Y-m-d') . "')
			OR
			(date_debut <= '" . $dateFinGraphe->format('Y-m-d') . "' AND date_debut >= '" . $dateDebutGraphe->format('Y-m-d') . "')
		)
		";
if(count($_SESSION['stats_projects']['users']) > 0) {
	$sql .= " AND ppe.user_id IN ('" . implode("','", $_SESSION['stats_projects']['users']) . "')";
}
if(count($_SESSION['stats_projects']['projets']) > 0) {
	$sql .= " AND ppe.projet_id IN ('" . implode("','", $_SESSION['stats_projects']['projets']) . "')";
}
$sql .= " ORDER BY date_debut";
$periodes->db_loadSQL($sql);


while ($periode = $periodes->fetch()) {
	$tmpDateTache = new DateTime();
	$tmpDateTache->setDate(substr($periode->date_debut,0,4), substr($periode->date_debut,5,2), substr($periode->date_debut,8,2));
	unset($finTache);
	if(!is_null($periode->date_fin)) {
		$finTache = new DateTime();
		$finTache->setDate(substr($periode->date_fin,0,4), substr($periode->date_fin,5,2), substr($periode->date_fin,8,2));
	}

	if(!is_null($periode->date_fin)) {
		while($tmpDateTache <= $finTache) {
			if(!in_array($tmpDateTache->format('w'), $DAYS_INCLUDED) || array_key_exists($tmpDateTache->format('Y-m-d'), $joursFeries) || $tmpDateTache > $dateFinGraphe || $tmpDateTache < $dateDebutGraphe) {
				$tmpDateTache->modify('+1 day');
				continue;
			}

			if($_SESSION['stats_projects']['abscisse_echelle'] == 'jour') {
				$cleTableau = $tmpDateTache->format('d/m/y');
			} elseif ($_SESSION['stats_projects']['abscisse_echelle'] == 'semaine') {
				$cleTableau = $smarty->getConfigVars('planning_semaine') .  $tmpDateTache->format('W Y');
			} elseif ($_SESSION['stats_projects']['abscisse_echelle'] == 'mois') {
				$cleTableau = $tmpDateTache->format('m/y');
			}

			if(!array_key_exists($periode->projet_id, $donnees[$cleTableau])) {
				$donnees[$cleTableau][$periode->projet_id] = "00:00";
			}
			$donnees[$cleTableau][$periode->projet_id] = ajouterDuree($donnees[$cleTableau][$periode->projet_id], CONFIG_DURATION_DAY);

			$tmpDateTache->modify('+1 day');
		}
	} else {
		if($_SESSION['stats_projects']['abscisse_echelle'] == 'jour') {
			$cleTableau = $tmpDateTache->format('d/m/y');
		} elseif ($_SESSION['stats_projects']['abscisse_echelle'] == 'semaine') {
			$cleTableau = $smarty->getConfigVars('planning_semaine') .  $tmpDateTache->format('W Y');
		} elseif ($_SESSION['stats_projects']['abscisse_echelle'] == 'mois') {
			$cleTableau = $tmpDateTache->format('m/y');
		}

		if(!isset($donnees[$cleTableau][$periode->projet_id])) {
			$donnees[$cleTableau][$periode->projet_id] = '00:00';
		}
		$donnees[$cleTableau][$periode->projet_id] = ajouterDuree($donnees[$cleTableau][$periode->projet_id], $periode->duree);
	}
}


$donneesAbscisse = array();
$donneesOrdonnee = array();
foreach ($donnees as $cle => $valeurs) {
	$donneesAbscisse[] = $cle;
	reset($projetsTab);
	foreach ($projetsTab as $projet_id) {
		if(isset($valeurs[$projet_id])) {
			if($_SESSION['stats_projects']['abscisse_echelle_valeur'] == 'heures') {
				$donneesOrdonnee[$projet_id][] = convertHourToDecimal($valeurs[$projet_id]);
			} else {
				$donneesOrdonnee[$projet_id][] = convertHourToDecimal($valeurs[$projet_id]) / CONFIG_DURATION_DAY;
			}
		} else {
			$donneesOrdonnee[$projet_id][] = "0";
		}
	}
}

$graph = new Graph($_SESSION['stats_projects']['graphe_width'],$_SESSION['stats_projects']['graphe_height'],'auto');
$graph->SetScale("textlin");
$graph->SetY2Scale("lin",0,90);
$graph->SetY2OrderBack(false);

$theme_class = new UniversalTheme;
$graph->SetTheme($theme_class);

// pour changer l'ordonnée
//$graph->yaxis->SetTickPositions(array(0,50,100,150,200,250,300,350), array(25,75,125,175,275,325));
//$graph->y2axis->SetTickPositions(array(30,40,50,60,70,80,90));

$graph->SetBox(false);
$graph->img->SetAntiAliasing(false); 

if ($_SESSION['stats_projects']['grille'] == 'grille_h' || $_SESSION['stats_projects']['grille'] == 'grille_hv') {
	$graph->ygrid->show();
} else {
	$graph->ygrid->show(false, false);
}
$graph->ygrid->SetFill(false);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
if($_SESSION['stats_projects']['ordonnee_min'] != "" && $_SESSION['stats_projects']['ordonnee_max'] != "") {
	$graph->SetScale('linlin', $_SESSION['stats_projects']['ordonnee_min'], $_SESSION['stats_projects']['ordonnee_max']);
}
// masque les points en dehors du graphe
$graph->SetClipping();

if ($_SESSION['stats_projects']['grille'] == 'grille_v' || $_SESSION['stats_projects']['grille'] == 'grille_hv') {
	$graph->xgrid->show();
} else {
	$graph->xgrid->show(false, false);
}
$graph->xaxis->SetTickLabels($donneesAbscisse);
$graph->xaxis->SetLabelAngle(45);
$nbMaxLabels = $_SESSION['stats_projects']['graphe_width'] * 30 / 1000;
$interval = count($donneesAbscisse) / $nbMaxLabels;
if($interval < 1) {
	$interval = 1;
}
$graph->xaxis->SetTextLabelInterval($interval);

foreach ($donneesOrdonnee as $projet_id => $valeurs) {

	$projetTmp = new Projet();
	$projetTmp->db_load(array('projet_id', '=', $projet_id));
	${'ligne_'.$projet_id} = new LinePlot($valeurs);
	${'ligne_'.$projet_id}->SetLegend($projetTmp->nom);
	$graph->Add(${'ligne_'.$projet_id});
	//${'ligne_'.$user_id}->value->Show();
	//${'ligne_'.$user_id}->value->SetFormat('%01.2f');
	$couleur = $projetTmp->couleur;
	if(is_null($couleur) || strtoupper($couleur) == 'FFFFFF') {
		$couleur = '000000';
	}
	${'ligne_'.$projet_id}->SetColor('#' . $couleur);
	${'ligne_'.$projet_id}->mark->SetWeight(5);
	${'ligne_'.$projet_id}->mark->SetWidth(8);
}


$graph->legend->SetFrameWeight(1);
$graph->legend->Pos(0.5,0.99,'center','bottom');
$graph->legend->SetColumns(6);
$graph->legend->SetColor('#4E4E4E','#00A78A');
//$graph->title->Set("Combineed Line and Bar plots");

$graph->SetMargin(40,10,10,100);

// Display the graph
$graph->Stroke();
