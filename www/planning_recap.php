<?php
	//////////////////////////
	// TABLEAU RECAP DES PROJETS
	//////////////////////////
	$html_recap = '<table id="divProjectTable" class="table table-striped">' . CRLF;
	$html_recap .= '	<tbody>' . CRLF;
	$html_recap .= '	<tr>' . CRLF;
	$html_recap .= '		<th class="w50"></th>' . CRLF;
	if($base_ligne == 'projets'){
		$cleTableau = 'tab_projet2';
	} elseif($base_ligne == 'users'){
		$cleTableau = 'tab_personne';
	} elseif($base_ligne == 'lieux'){
		$cleTableau = 'winPeriode_lieu';
	} elseif($base_ligne == 'ressources'){
		$cleTableau = 'winPeriode_ressource';
	}
	$html_recap .= '		<th class="planningTabName">' . $smarty->getConfigVars($cleTableau) . '</th>' . CRLF;
	$html_recap .= '		<th class="planningTabTask">' . $smarty->getConfigVars('tab_periode2') . '</th>' . CRLF;
	$html_recap .= '		<th class="w220 planningTabCharge">' . $smarty->getConfigVars('tab_charge') . '</th>' . CRLF;
	$html_recap .= '	</tr>' . CRLF;
	foreach ($planning['lignes'] as $cle => $infos)
	{
		// Calcul des jours occupés
		$joursOccupes = array();
		if (isset($planning['taches'][$infos['id']])) {
			foreach ($planning['taches'][$infos['id']] as $cleTmp => $tache) {
				foreach ($tache as $t) {
					$joursOccupes[$cle][]=$t;
				}
			}
		}
		// si option de masquer les lignes vides est activée, on masque la ligne si elle est vide
		if($masquerLigneVide == 1 && count($joursOccupes) == 0) {
			continue;
		}

		$html_recap .= '	<tr>' . CRLF;
		$couleurTexte = buttonFontColor('#' . $infos['couleur']);
		$tooltipProjet = '<b>' . $smarty->getConfigVars('tab_projet') . '</b> : ' . xss_protect($infos['nom']) . '(' . $infos['id'] . ')<br />' ;

		$html_recap .= '<td onClick="javascript:Reloader.stopRefresh();'.$infos['url_modif'].';undefined;" class="w25"><span data-tooltip-content="#tooltipprojet-'.$infos['id'].'" class="smallFontSize pastille-projet tooltipster" style="background-color:#' . $infos['couleur'] . ';color:'. $couleurTexte.'">' . $infos['id'] . '</span></td>' . CRLF;
		$html_recap .= "<div class='tooltip-html'><div id='tooltipprojet-".$infos['id']."'>$tooltipProjet</div></div>". '</td>' . CRLF;
		$html_recap .= '<td class="planningTabName"><b>' . xss_protect($infos['nom']) . '</b>';
		$html_recap .= '<td class="vbottom planningTabTask">';
		// Si aucune tâche
		if (!isset($planning['taches'][$cle]))
		{
			$html_recap .= '</td>' . CRLF;
			$html_recap .= '<td>' . CRLF;
			$html_recap .= '</td>' . CRLF;
			continue;			
		// Si des tâches
		}else
		{
			if ($base_colonne=="heures")
			{
			    $planning_temp=array();
				foreach ($planning['taches'][$cle] as $date=>$periodes)
				{
					foreach ($periodes as $p)
					{
						if (!in_array($p,$planning_temp))
						{
							$planning_temp[]=$p;
						}
					}	
				}
				$planning['taches'][$cle]=$planning_temp;
			}
			
			$totalJours = 0;
			$totalJoursPassed = 0;
			$totalHeures = "00:00";
			$totalHeuresPassed = "00:00";
			$taches_des_affichees=array();
			foreach ($planning['taches'][$cle] as $periodes)
			{
				foreach ($periodes as $p)
				{
					if (in_array($p,$taches_des_affichees))
					{
						continue;
					}else $taches_des_affichees[]=$p;
					$infos_tache=$planning['periodes'][$p];
					$html_recap .= '<div class="smallFontSize taskDivComment" onclick="javascript:xajax_modifPeriode(' . $p . ');undefined;">';
					
					if (CONFIG_PLANNING_AFFICHAGE_STATUS == 'pastille')
					{
						$html_recap .= '<div class="pastille-statut tooltipster" style="float:left;margin-right:7px;background-color:#'.$infos_tache['statut_couleur'].'" title="'.$infos_tache['statut_nom'].'"></div>';
					}
					$html_recap .= '<b>'.xss_protect($infos['nom']);
					
					if (!is_null($infos_tache['titre'])) {
						$html_recap .= ' - ' . xss_protect($infos_tache['titre']);
					}
					$html_recap .= '</b><br />';
					$html_recap .= '<i class="fa fa-calendar" aria-hidden="true"></i> '.sqldate2userdate($infos_tache['date_debut']) . ' <i class="fa fa-caret-right" aria-hidden="true"></i> ';
					if (is_null($infos_tache['date_fin'])) {
						$html_recap .= sqltime2usertime($infos_tache['duree']) . ' (' . $infos_tache['user_id'] . ')';
					} else {
						$html_recap .= sqldate2userdate($infos_tache['date_fin']) . ' (' . $infos_tache['user_id'] . ')';
					}
					// Lieu
					if (!is_null($infos_tache['lieu_id'])) {
						$html_recap .= "<br /><i class='fa fa-map-marker' aria-hidden='true'></i>&nbsp;&nbsp;".$infos_tache['lieu_nom'];
					}
					// Ressource
					if (!is_null($infos_tache['ressource'])) {
						$html_recap .= "<br /><i class='fa fa-plug' aria-hidden='true'></i> ".$infos_tache['ressource_nom'];
					}	
					// Statut
					if (CONFIG_PLANNING_AFFICHAGE_STATUS == 'aucun')
					{
						$html_recap .='<br />';
					}elseif (CONFIG_PLANNING_AFFICHAGE_STATUS == 'nom')
					{
						$html_recap .= '<br />'.$infos_tache['statut_nom'].'<br />';
					}elseif (CONFIG_PLANNING_AFFICHAGE_STATUS == 'pourcentage')
					{
						$couleurTexte=buttonFontColor('#'.$infos_tache['statut_couleur']);
						$html_recap .= '<div class="progress tooltipster" title="'.$infos_tache['statut_nom'].'"><div class="progress-bar" style="width: '.$infos_tache['statut_pourcentage'].'%;background-color:#'.$infos_tache['statut_couleur'].';color:'.$couleurTexte.'">'.$infos_tache['statut_pourcentage'].'%</div></div>';
					}
					// commentaire
					if (!is_null($infos_tache['notes'])) {
						$html_recap .= '' .	xss_protect($infos_tache['notes']). '';
					}
					// lien
					if (!is_null($infos_tache['lien'])) {
						$html_recap .= '<br><a href="' . xss_protect($infos_tache['lien']) . '" target="_blank">' . $smarty->getConfigVars('tab_lien') . '</a>';
					}
					$html_recap .= '</div>';

					$date1 = new DateTime();
					$date1->setDate(substr($infos_tache['date_debut'],0,4), substr($infos_tache['date_debut'],5,2), substr($infos_tache['date_debut'],8,2));

					// on additionne les jours de travail
					if(!is_null($infos_tache['date_fin'])) 
					{
						$date2 = new DateTime();
						$date2->setDate(substr($infos_tache['date_fin'],0,4), substr($infos_tache['date_fin'],5,2), substr($infos_tache['date_fin'],8,2));
						while ($date1 <= $date2) 
						{
							// on ne compte pas le jour si c'est WE ou jour férié
							if (in_array($date1->format('w'), $DAYS_INCLUDED) && !array_key_exists($date1->format('Y-m-d'), $joursFeries)) 
							{
								$totalJours +=1;
								if($date1 < $now) {
									$totalJoursPassed +=1;
								}
							}
							$date1->modify('+1 day');
						}
					} else 
					{
						$totalHeures = ajouterDuree($totalHeures, $infos_tache['duree']);
						if($date1 < $now) 
						{
							$totalHeuresPassed = ajouterDuree($totalHeuresPassed, $infos_tache['duree']);
						}
					}
				}
			}
		}
		
		$html_recap .= '</td>' . CRLF;
		$html_recap .= '<td class="planningTabCharge">' . CRLF;
		if(!is_null($infos_tache['charge'])) {
			$html_recap .= $smarty->getConfigVars('tab_chargeProjet') . ' : ' . $infos_tache['charge'] . $smarty->getConfigVars('tab_j') . '<br />' . CRLF;
		}

		$nbJourTot=0;
		$TotalHeureExplode = explode (':',$totalHeures);
		$TotalHeureH=$TotalHeureExplode[0];
		$TotalHeureM=$TotalHeureExplode[1];
		if($totalHeures != '00:00') {
			$nbJourTot = round (($TotalHeureH+$TotalHeureM/60)/$TotalMaxJour,2);
		}
		$nbHeuresTotal = ($totalJours*$TotalMaxJourH+$TotalHeureH).'h';
		$nbheures = ($totalJours*$TotalMaxJourH+$TotalHeureH);
		$nbminutes = ($totalJours*$TotalMaxJourM+$TotalHeureM);
		if ($nbminutes >= 60)
		{
			$nbh=floor($nbminutes/60);
			$nbminutes = $nbminutes - $nbh*60;
			$nbheures = $nbheures + $nbh;
		}
		$nbHeuresTotal="$nbheures"."h";
		if ($nbminutes > 0) $nbHeuresTotal=$nbHeuresTotal.sprintf("%'.02d\n", $nbminutes);
		$html_recap .= "<b>". $smarty->getConfigVars('tab_total') . ' : '	. ($totalJours+$nbJourTot) .$smarty->getConfigVars('tab_j'). " ( = ".$nbHeuresTotal.") </b>" . CRLF;

		$html_recap .= '<br />' . CRLF;
		$nbJourTotPassed=0;
		$TotalHeurePassedH=0;
		$TotalHeurePassedM=0;
		if($totalHeuresPassed > 0) 
		{
			$TotalHeurePassedExplode = explode (':',$totalHeuresPassed);
			$TotalHeurePassedH=$TotalHeurePassedExplode[0];
			$TotalHeurePassedM=$TotalHeurePassedExplode[1];
			$nbJourTotPassed = round (($TotalHeurePassedH+$TotalHeurePassedM/60)/$TotalMaxJour,2);
		}
		if($totalJoursPassed > 0 || $totalHeuresPassed > 0) 
		{
			$nbHeuresTotalPassed = (($totalJoursPassed*$TotalMaxJour)+$TotalHeurePassedH).'h'.($TotalHeurePassedM!="00"?($TotalHeurePassedM):"");
			$html_recap .= $smarty->getConfigVars('tab_passe') . ' : ' . ($totalJoursPassed+$nbJourTotPassed) .$smarty->getConfigVars('tab_j'). " ( = ".$nbHeuresTotalPassed." / ".round(($totalJoursPassed+$nbJourTotPassed)/($totalJours+$nbJourTot)*100,1) ."% ) " . CRLF;
		}
		$html_recap .= '</td>' . CRLF;
		$html_recap .= '	</tr>' . CRLF;
	}
	$html_recap .= '</tbody></table>' . CRLF;
?>