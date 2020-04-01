<?php

require('./base.inc');
require(BASE . '/../config.inc');

$smarty = new MySmarty();

require BASE . '/../includes/header.inc';

$html = '';
$js = '';

$joursFeries = getJoursFeries();

// PARAMÈTRES ////////////////////////////////
$dateDebut = new DateTime();
$dateFin = new DateTime();
$dateDebut->setDate(substr($_SESSION['date_debut_affiche'],6,4), substr($_SESSION['date_debut_affiche'],3,2), substr($_SESSION['date_debut_affiche'],0,2));
$dateFin->setDate(substr($_SESSION['date_fin_affiche'],6,4), substr($_SESSION['date_fin_affiche'],3,2), substr($_SESSION['date_fin_affiche'],0,2));

$nbLignes = $_SESSION['nb_lignes'];
$pageLignes = $_SESSION['page_lignes'];

$masquerLigneVide = $_SESSION['masquerLigneVide'];

$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);

$cir_cii_projectList = Array('ciiltb', 'ciiwsweet', 'cirlemon', 'cirlsc', 'cirwopla');
$not_working_projectList = Array('pade', 'RTT', 'CP', 'ArretW', 'Hospitalisation', 'sanssolde');

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




// Debut creation CSV ////////////////////////////////
$data = Array();
$projectList = Array();
$totalByProject = Array();
$texte = '';

// Calcul du nombre de jours ouvree entre les dates d export
$nb_jours_ouvres = 0;
$begin = strtotime($dateDebut->format('Y-m-d'));
$end = strtotime($dateFin->format('Y-m-d'));
foreach ($joursFeries as $jour) {
    if (strtotime($jour['date']) >= $begin && strtotime($jour['date']) <= $end) {
        $nb_jours_ouvres--;
    }
}
while ($begin <= $end) {
    $day = date("N", $begin);
    if (!in_array($day, [6,7])) {
        $nb_jours_ouvres++;
    }
    $begin += 86400;
}

// Creation des data
while ($periode = $periodes->fetch()) {
    if (in_array($periode->projet_id, $cir_cii_projectList, true) || in_array($periode->projet_id, $not_working_projectList, true)) {
        $nom_projet = utf8_encode($periode->nom_projet);
        $nom_personne = utf8_encode($periode->nom_personne);
        // Initialisation du tableau de data
        if (!isset($data[$nom_personne])) {
            $data[$nom_personne] = Array();
            $data[$nom_personne]['Total'] = 0;
        }
        if (!isset($data[$nom_personne][$nom_projet])) {
            $data[$nom_personne][$nom_projet] = 0;
            if (!isset($totalByProject[$nom_projet])) {
                $totalByProject[$nom_projet] = 0;
            }
        }
        if (in_array($periode->projet_id, $cir_cii_projectList, true)) {
            $addProject = true;
            foreach ($projectList as $project) {
                if ($project == $nom_projet) {
                    $addProject = false;
                    continue;
                }
            }
            if ($addProject) {
                $projectList[$periode->projet_id] = $nom_projet;
            }
        }

        // Ajout du temps passe dans le tableau de data
        if (!empty($periode->duree || $periode->duree == '08:00:00')) { // Etrangement, si la duree est egale a '08:00:00', celle-ci est considéré comme empty
            $data[$nom_personne][$nom_projet] += $periode->duree;
            $data[$nom_personne]['Total'] += $periode->duree;
            $totalByProject[$nom_projet] += $periode->duree;
        } else {
            // Calcul du temps passe si la duree est vide
            $working_time = 0;
            if (strtotime($periode->date_debut) < strtotime($dateDebut->format('Y-m-d'))) {
                $begin = strtotime($dateDebut->format('Y-m-d'));
            } else {
                $begin = strtotime($periode->date_debut);
            }
            if (strtotime($periode->date_fin) > strtotime($dateFin->format('Y-m-d'))) {
                $end = strtotime($dateFin->format('Y-m-d'));
            } else {
                $end = strtotime($periode->date_fin);
            }
            // Sousstraction des jours feries
            foreach ($joursFeries as $jour) {
                if (strtotime($jour['date']) >= $begin && strtotime($jour['date']) <= $end) {
                    $working_time-=8;
                }
            }
            // Calcul du nombre de jours ouvree dans la periode
            while ($begin <= $end) {
                $day = date("N", $begin);
                if (!in_array($day, [6,7])) {
                    $working_time+=8;
                }
                $begin += 86400;
            }
            $data[$nom_personne][$nom_projet] += $working_time;
            $data[$nom_personne]['Total'] += $working_time;
            $totalByProject[$nom_projet] += $working_time;
        }
    }
}

// En-tetes de colonnes
$texte .= 'NOM;Prénom;Fonction;';
foreach ($projectList as $projectName) {
    $texte .= "$projectName;";
}
$texte .= "Jours travaillés dans l'année";
$texte .= "\n";

// contenu des lignes
$total_jours_travailles = 0;
foreach ($data as $userName => $userData) {
    $user = explode(' ', $userName);
    $texte .= "$user[0];$user[1];;";
    foreach ($projectList as $projectName) {
        if (isset($userData[$projectName])) {
            $texte .= ($userData[$projectName]/8).";";
        } else {
            $texte .= "0;";
        }
    }
    $nb_jours_travailles = $nb_jours_ouvres*8;
    if (isset($userData['Pas encore dans l\'entreprise'])) {
        $nb_jours_travailles -= $userData['Pas encore dans l\'entreprise'];
    }
    if (isset($userData['CP'])) {
        $nb_jours_travailles -= $userData['CP'];
    }
    if (isset($userData['RTT'])) {
        $nb_jours_travailles -= $userData['RTT'];
    }
    if (isset($userData['Arrêt de Travail'])) {
        $nb_jours_travailles -= $userData['Arrêt de Travail'];
    }
    if (isset($userData['Hospitalisation'])) {
        $nb_jours_travailles -= $userData['Hospitalisation'];
    }
    if (isset($userData['Congé sans solde'])) {
        $nb_jours_travailles -= $userData['Congé sans solde'];
    }
    $texte .= ($nb_jours_travailles/8).";";
    $total_jours_travailles += $nb_jours_travailles/8;
    $texte .= "\n";
}

// Lignes de total par projet
$texte .= ";;;";
foreach ($projectList as $projectName) {
    $texte .= ($totalByProject[$projectName]/8).";";
}
$texte .= $total_jours_travailles.";\n";

$nomFichier = 'export_cir_cii_' . date('Y-m-d-H-i') . '.csv';

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='. $nomFichier);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . strlen($texte));
echo $texte;
