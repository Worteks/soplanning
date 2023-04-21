		<div class="vw-100 position-fixed" id="firstLayer">
			<div class="soplanning-box form-inline pt-0" id="divPlanningDateSelector">
				<div class="btn-group cursor-pointer pt-2" id="btnDateNow">
					<a class="btn btn-default tooltipster" title="{#aujourdhui#}{$dateToday}" onClick="document.location='process/planning.php?raccourci_date=aujourdhui'" id="buttonDateNowSelector"><i class="fa fa-home fa-lg fa-fw" aria-hidden="true"></i></a>
				</div>
			{* DIV POUR CHOIX DATE *}
					<div class="btn-group ml-md-2 pt-2" id="dropdownDateSelector">
						<form action="process/planning.php" method="GET" class="form-inline" id="formChoixDates">
						<a href="#" id="buttonDateSelector" class="btn dropdown-toggle btn-default" data-toggle="dropdown">
							<b>
							<span class="d-none d-sm-inline-block">{$dateDebutTexte1}&nbsp;</span>{$dateDebutTexte2}
							{if $baseLigne neq "heures"}
								- <span class="d-none d-sm-inline-block">{$dateFinTexte1}&nbsp;</span>{$dateFinTexte2}
							{/if}
							</b>&nbsp;&nbsp;&nbsp;<span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li>
								<table class="planning-dateselector">
								<tr>
									<td>
										{#formDebut#} :&nbsp;
									</td>
									<td>
									{if $smarty.session.isMobileOrTablet==1}
										<input name="date_debut_affiche" id="date_debut_affiche" type="date" value="{$dateDebut|forceISODateFormat}" class="form-control" onChange="$('date_debut_custom').value= '----------------';" />
									{else}
										<input name="date_debut_affiche" id="date_debut_affiche" type="text" value="{$dateDebut}" class="form-control datepicker" onChange="$('date_debut_custom').value= '----------------';" />
									{/if}
									<br>
										<select id="date_debut_custom" class="form-control" name="date_debut_custom" onChange="$('date_debut_affiche').value= '----------------';">
											<option value="">{#raccourci#}...</option>
											<option value="aujourdhui">{#raccourci_aujourdhui#}</option>
											<option value="semaine_derniere">{#raccourci_semaine_derniere#}</option>
											<option value="mois_dernier">{#raccourci_mois_dernier#}</option>
											<option value="debut_semaine">{#raccourci_debut_semaine#}</option>
											<option value="debut_mois">{#raccourci_debut_mois#}</option>
										</select>
									</td>
									{if $baseLigne neq "heures"}
										<td>
											&nbsp;{#formFin#} :&nbsp;
										</td>
										<td>
										{if $smarty.session.isMobileOrTablet==1}
											<input name="date_fin_affiche" id="date_fin_affiche" type="date" value="{$dateFin|forceISODateFormat}" class="form-control"  onChange="$('date_fin_custom').value= '----------------';" />
										{else}
											<input name="date_fin_affiche" id="date_fin_affiche" type="text" value="{$dateFin}" class="form-control datepicker"   onChange="$('date_fin_custom').value= '----------------';" />
										{/if}

											<br>
											<select id="date_fin_custom" name="date_fin_custom" class="form-control" onChange="$('date_fin_affiche').value= '----------------';">
												<option value="">{#raccourci#}...</option>
												<option value="1_semaine">{#raccourci_1_semaine#}</option>
												<option value="2_semaines">{#raccourci_2_semaines#}</option>
												<option value="3_semaines">{#raccourci_3_semaines#}</option>
												<option value="1_mois">{#raccourci_1_mois#}</option>
												<option value="2_mois">{#raccourci_2_mois#}</option>
												<option value="3_mois">{#raccourci_3_mois#}</option>
												<option value="4_mois">{#raccourci_4_mois#}</option>
												<option value="5_mois">{#raccourci_5_mois#}</option>
												<option value="6_mois">{#raccourci_6_mois#}</option>
											</select>
										</td>
									{/if}
									<td class="pr-3">
										<button id="dateFilterButton" class="btn btn-sm btn-default" onClick="$('formChoixDates').submit();"><i class="fa fa-search fa-lg fa-fw" aria-hidden="true"></i></button>
									</td>
								</tr>
								</table>
							</li>
						</ul>
				</form>
				</div>
					<div class="btn-group ml-md-2 pt-2 cursor-pointer" id="btnDateSelector">
						<a class="btn btn-default" onClick="document.location='process/planning.php?raccourci_date=-{$nbJours}';" id="buttonDatePrevSelector"><i class="fa fa-chevron-left fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-xl-inline-block">{$dateBoutonInferieur}</span></a>
						<a class="btn btn-default" onClick="document.location='process/planning.php?raccourci_date=+{$nbJours}';" id="buttonDateNextSelector"><span class="d-none d-xl-inline-block">{$dateBoutonSuperieur}</span> <i class="fa fa-chevron-right fa-lg fa-fw" aria-hidden="true"></i></a>
					</div>
					{if !in_array("tasks_readonly", $user.tabDroits)}
						<div class="btn-group ml-md-4 pt-2" id="btnAddTask">
							<a class="btn btn-info" href="javascript:Reloader.stopRefresh();xajax_ajoutPeriode();undefined;">
								<i class="fa fa-calendar-plus-o fa-lg fa-fw" aria-hidden="true"></i>
								<span class="d-none d-xl-inline-block" >{#menuAjouterPeriode#}</span>
							</a>
						</div>
					{/if}
			</div>
		</div>
		<div class="vw-100 position-fixed" id="secondLayer">
			<div class="soplanning-box form-inline pt-0" id="divPlanningMainFilter">
					{* DIV POUR CHOIX AFFICHAGE *}
					<div class="btn-group pt-2" id="dropdownTypePlanning">
						<button class="btn dropdown-toggle btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static" ><i class="fa fa-calendar fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;&nbsp;{#planning_affichage#}</span>&nbsp;<span class="caret"></span></button>
						<div class="dropdown-menu">
							{if $smarty.session.baseLigne eq 'users'}
								<a class="dropdown-item" href="process/planning.php?baseLigne=users">
								<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
							{else}
								<a class="dropdown-item" href="process/planning.php?baseLigne=users">
								<i style="margin-left:19px;">&nbsp;</i>
							{/if}
							{#planningPersonne#}</a>
							
							{if $smarty.session.baseLigne eq 'projets'}
								<a class="dropdown-item" href="process/planning.php?baseLigne=projets">
								<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
							{else}
								<a class="dropdown-item" href="process/planning.php?baseLigne=projets">
								<i style="margin-left:19px;">&nbsp;</i>
							{/if}
							{#planningProjet#}</a>
							
							{if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX == 1}
								{if $smarty.session.baseLigne eq 'lieux'}
									<a class="dropdown-item" href="process/planning.php?baseLigne=lieux">
									<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
								{else}
									<a class="dropdown-item" href="process/planning.php?baseLigne=lieux">
									<i style="margin-left:19px;">&nbsp;</i>
								{/if}
								{#planningLieu#}</a>
							{/if}
							
							{if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES == 1}
								{if $smarty.session.baseLigne eq 'ressources'}
									<a class="dropdown-item" href="process/planning.php?baseLigne=ressources">
									<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
								{else}
									<a class="dropdown-item" href="process/planning.php?baseLigne=ressources">
									<i style="margin-left:19px;">&nbsp;</i>
								{/if}
								{#planningRessource#}</a>
							{/if}

							{if $smarty.session.baseLigne eq 'heures' && $smarty.session.baseColonne eq 'users' }
								<a class="dropdown-item" href="process/planning.php?baseLigne=heures&baseColonne=users">
								<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
							{else}
								<a class="dropdown-item" href="process/planning.php?baseLigne=heures&baseColonne=users">
								<i style="margin-left:19px;">&nbsp;</i>
							{/if}
							{#planningHorairePersonne#}</a>
														
							<div class="dropdown-divider"></div>
							{if $smarty.session.baseColonne eq 'users' and $smarty.session.baseLigne eq 'heures'}
								<a class="dropdown-item disabled" href="process/planning.php?baseColonne=heures"><i style="margin-left:19px;">&nbsp;</i>&nbsp;&nbsp;{#planningHeures#}</a>
								<a class="dropdown-item disabled" href="process/planning.php?baseColonne=jours"><i style="margin-left:19px;">&nbsp;</i>&nbsp;&nbsp;{#planningJours#}</a>
							{else}
								{if $smarty.session.baseColonne eq 'heures'}
									<a class="dropdown-item" href="process/planning.php?baseColonne=heures">
									<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
								{else}
									<a class="dropdown-item" href="process/planning.php?baseColonne=heures">
									<i style="margin-left:19px;">&nbsp;</i>
								{/if}
								{#planningHeures#}</a>
								{if $smarty.session.baseColonne eq 'jours'}
									<a class="dropdown-item" href="process/planning.php?baseColonne=jours">
									<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
								{else}
									<a class="dropdown-item" href="process/planning.php?baseColonne=jours">
									<i style="margin-left:19px;">&nbsp;</i>
								{/if}
								{#planningJours#}</a>
							{/if}
							<div class="dropdown-divider"></div>

							{if $smarty.session.masquerLigneVide eq 0 }
								<a class="dropdown-item" href="process/planning.php?baseLigne={$smarty.session.baseLigne}&baseColonne={$smarty.session.baseColonne}&masquerLigneVide=1">
								<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
							{else}
								<a class="dropdown-item" href="process/planning.php?baseLigne={$smarty.session.baseLigne}&baseColonne={$smarty.session.baseColonne}&masquerLigneVide=0">
								<i style="margin-left:19px;">&nbsp;</i>
							{/if}
							{#planningAfficherLignesVides#}</a>
							
							{if $smarty.session.afficherLigneTotal eq 1}
								<a class="dropdown-item" href="process/planning.php?baseLigne={$smarty.session.baseLigne}&baseColonne={$smarty.session.baseColonne}&afficherLigneTotal=0">
								<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
							{else}
								<a class="dropdown-item" href="process/planning.php?baseLigne={$smarty.session.baseLigne}&baseColonne={$smarty.session.baseColonne}&afficherLigneTotal=1">
								<i style="margin-left:19px;">&nbsp;</i>
							{/if}
							{#planningAfficherTotal#}</a>
							
							{if $smarty.session.afficherLigneTotalTaches eq 1}
								<a class="dropdown-item" href="process/planning.php?baseLigne={$smarty.session.baseLigne}&baseColonne={$smarty.session.baseColonne}&afficherLigneTotalTaches=0">
								<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
							{else}
								<a class="dropdown-item" href="process/planning.php?baseLigne={$smarty.session.baseLigne}&baseColonne={$smarty.session.baseColonne}&afficherLigneTotalTaches=1">
								<i style="margin-left:19px;">&nbsp;</i>
							{/if}
							{#planningAfficherTotalTaches#}</a>

							{if $smarty.session.afficherTableauRecap eq 1}
								<a class="dropdown-item" href="process/planning.php?baseLigne={$smarty.session.baseLigne}&baseColonne={$smarty.session.baseColonne}&afficherTableauRecap=0">
								<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
							{else}
								<a class="dropdown-item" href="process/planning.php?baseLigne={$smarty.session.baseLigne}&baseColonne={$smarty.session.baseColonne}&afficherTableauRecap=1">
								<i style="margin-left:19px;">&nbsp;</i>
							{/if}
							{#planningAfficherTableauRecap#}</a>
						</div>
					</div>
			
					{* DIV POUR CHOIX FILTRE USERS *}
					<div class="btn-group pt-2" id="dropdownTaskUserFilter">
						<form action="process/planning.php" method="POST">
						<input type="hidden" name="filtreUser" value="1" />
						<select name="filtreUser" multiple="multiple" id="filtreUser" class="d-none multiselect">
							{if $listeUsers|@count eq 0}
								<option>&nbsp;{#formFiltreUserAucunProjet#}</option>
							{else}
								<optgroup id="gu0" label="{#cocheUserSansGroupe#}">
								{assign var=groupeTemp value=""}
								{foreach from=$listeUsers item=userCourant name=loopUsers}
									{if $userCourant.user_groupe_id neq $groupeTemp}
										</optgroup><optgroup id="gu{$userCourant.user_groupe_id}" label="{$userCourant.groupe_nom}">
									{/if}
								<option value="{$userCourant.user_id}" {if in_array($userCourant.user_id, $filtreUser)}selected="selected"{/if}>{$userCourant.nom|xss_protect} ({$userCourant.user_id|xss_protect})</option>
								{assign var=groupeTemp value=$userCourant.user_groupe_id}
								{/foreach}
							{/if}
							</optgroup></select>
						</form>
					</div>
					{* DIV POUR CHOIX FILTRE PROJETS *}
					<div class="btn-group pt-2" id="dropdownTaskProjectFilter">
						<form action="process/planning.php" method="POST">
						<input type="hidden" name="filtreGroupeProjet" value="1" />
						<select name="filtreGroupeProjet" multiple="multiple" id="filtreGroupeProjet" class="d-none multiselect">
							{if $listeProjets|@count eq 0}
								<option>&nbsp;{#formFiltreProjetAucunProjet#}</option>
							{else}
								<optgroup id="g0" label="{#projet_liste_sansGroupes#}">
								{assign var=groupeTemp value=""}
								{foreach from=$listeProjets item=projetCourant name=loopProjets}
									{if $projetCourant.groupe_id neq $groupeTemp}
										</optgroup><optgroup id="g{$projetCourant.groupe_id}" label="{$projetCourant.groupe_nom}">
									{/if}
								<option value="{$projetCourant.projet_id}" {if in_array($projetCourant.projet_id, $filtreGroupeProjet)}selected="selected"{/if}>{$projetCourant.nom|xss_protect} ({$projetCourant.projet_id|xss_protect})</option>
								{assign var=groupeTemp value=$projetCourant.groupe_id}
								{/foreach}
							{/if}
							</optgroup></select>
						</form>
					</div>
					{* DIV POUR CHOIX FILTRE AVANCES *}
					<div class="btn-group pt-2" id="dropdownAdvancedFilter">
						<form action="process/planning.php" method="POST">
						<button class="btn {if ($filtreGroupeLieu|@count >0) or ($filtreGroupeRessource|@count >0)}btn-danger{else}btn-default{/if} dropdown-toggle" data-toggle="dropdown" onclick="javascript:multiselecthide();" data-display="static"><i class="fa fa-flask fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-xl-inline-block">&nbsp;{#filtres_avances#}&nbsp;</span><span class="caret"></span></button>
						<ul class="dropdown-menu filtrePlanning">
							<li>
								<input type="submit" value="{#submit#}" class="btn btn-default ml-2" />
								{if ($filtreGroupeLieu|@count >0) or ($filtreGroupeRessource|@count >0)}<a href="process/planning.php?desactiverFiltreAvances=1" class="btn btn-danger btn-sm margin-left-10">{#formFiltreAvancesDesactiver#}</a>{/if}
							</li>
							<li class="divider"></li>
							<li>
								<table onClick="event.cancelBubble=true;" class="planning-filter">
									<tr>
										<td class="planningDropdownFilter">
											<input type="hidden" name="filtreStatutTache" value="1">
											<b>{#formChoixStatutTache#}</b><br />
											<div class="form-horizontal col-md-12">
											{foreach from=$listeStatusTaches item=statust}
											<label class="checkbox">
												<input type="checkbox" id="{$statust.status_id}" name="statutsTache[]" value="{$statust.status_id}" {if in_array($statust.status_id, $filtreStatutTache) || $filtreStatutTache|count eq 0}checked="checked"{/if} />&nbsp;{$statust.nom}
											</label>
											{/foreach}
											</div>
										</td>
										<td class="planningDropdownFilter">
											<input type="hidden" name="filtreStatutProjet" value="1">
											<b>{#formChoixStatutProjet#}</b><br />
											<div class="form-horizontal col-md-12">
											{foreach from=$listeStatusProjets item=statusp}
											<label class="checkbox">
												<input type="checkbox" id="statut_projet_{$statusp.status_id}" name="statutsProjet[]" value="{$statusp.status_id}" {if in_array($statusp.status_id, $filtreStatutProjet) || $filtreStatutProjet|count eq 0}checked="checked"{/if} />&nbsp;{$statusp.nom}
											</label>
											{/foreach}
											</div>
										</td>

										{* Filtres avancés emplacement *}
										{if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX == 1 and ($listeLieux|@count) > 0 }
											<td class="planningDropdownFilter">
											<input type="hidden" name="filtreGroupeLieu" value="1">
											<input type="hidden" name="maxGroupeLieu" value="{$listeLieux|@count}">
												<b>{#menuLieux#}</b>
												<div class="form-horizontal col-md-12">
												{assign var=groupeTemp value=""}
												{math assign=nbColonnes equation="ceil(nbLieux / nbLieuxParColonnes)" nbLieux=$listeLieux|@count nbLieuxParColonnes=$smarty.const.FILTER_NB_AERA_PER_COLUMN}
												{math assign=maxCol equation="ceil(nbLieux / nbColonnes)" nbLieux=$listeLieux|@count nbColonnes=$nbColonnes}
												{assign var=tmpNbDansColCourante value="0"}
												{foreach from=$listeLieux item=lieuCourant name=loopLieux}
													{if $tmpNbDansColCourante > $maxCol}
														{assign var=tmpNbDansColCourante value="0"}
														</td>
													<td class="planningDropdownFilter">
													{/if}
													<label class="checkbox">
														<input type="checkbox" id="lieu_{$lieuCourant.lieu_id}" name="lieu[]" value="{$lieuCourant.lieu_id}" {if in_array($lieuCourant.lieu_id, $filtreGroupeLieu)}checked="checked"{/if} /> {$lieuCourant.nom|xss_protect}
													</label>
													{assign var=tmpNbDansColCourante value=$tmpNbDansColCourante+1}
												{/foreach}
												</div>
											</td>
										{/if}

										{* Filtres avancés ressources *}
										{if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES == 1 and ($listeRessources|@count) > 0 }
											<td class="planningDropdownFilter">
											<input type="hidden" name="maxGroupeRessource" value="{$listeRessources|@count}">
											<input type="hidden" name="filtreGroupeRessource" value="1">
												<b>{#menuRessources#}</b>
												<div class="form-horizontal col-md-12">
												{assign var=groupeTemp value=""}
												{math assign=nbColonnes equation="ceil(nbRessources / nbRessourcesParColonnes)" nbRessources=$listeRessources|@count nbRessourcesParColonnes=$smarty.const.FILTER_NB_RESSOURCES_PER_COLUMN}
												{math assign=maxCol equation="ceil(nbRessources / nbColonnes)" nbRessources=$listeRessources|@count nbColonnes=$nbColonnes}
												{assign var=tmpNbDansColCourante value="0"}
												{foreach from=$listeRessources item=ressourceCourant name=loopRessources}
													{if $tmpNbDansColCourante > $maxCol}
														{assign var=tmpNbDansColCourante value="0"}
														</td>
														<td class="planningDropdownFilter">
													{/if}
													<label class="checkbox">
														<input type="checkbox" id="ressource_{$ressourceCourant.ressource_id}" value="{$ressourceCourant.ressource_id}" name="ressource[]" {if in_array($ressourceCourant.ressource_id, $filtreGroupeRessource)}checked="checked"{/if} /> {$ressourceCourant.nom|xss_protect}
													</label>
													{assign var=tmpNbDansColCourante value=$tmpNbDansColCourante+1}
												{/foreach}
												</div>
											</td>
										{/if}
									</tr>
								</table>
							</li>
						</ul>
						</form>
					</div>
					{* DIV POUR TRI AFFICHAGE *}
					<div class="btn-group pt-2" id="dropdownTri">
						<button class="btn dropdown-toggle btn-default" data-toggle="dropdown" onclick="javascript:multiselecthide();"><i class="fa fa-sort-amount-desc fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-xl-inline-block">&nbsp;&nbsp;{#formTrierPar#}</span>&nbsp;<span class="caret"></span></button>
						<div class="dropdown-menu">
							{if $baseLigne eq "projets"}
								{foreach from=$triPlanningPossibleProjet item=triTemp}
									{assign var=chaineTmp value="triProjet_"|cat:$triTemp|replace:' ':'_'|replace:',':'_'}
									<a class="dropdown-item" href="process/planning.php?triPlanning={$triTemp|urlencode}">{if $triTemp eq $triPlanning}<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;{else}<i style="margin-left:19px;">&nbsp;</i>{/if}{$smarty.config.$chaineTmp}</a>
								{/foreach}
							{elseif $baseLigne eq "users"}
								{foreach from=$triPlanningPossibleUser item=triTemp}
									{assign var=chaineTmp value="triUser_"|cat:$triTemp|replace:' ':'_'|replace:',':'_'}
									<a class="dropdown-item" href="process/planning.php?triPlanning={$triTemp|urlencode}">{if $triTemp eq $triPlanning}<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;{else}<i style="margin-left:19px;">&nbsp;</i>{/if}{$smarty.config.$chaineTmp}</a>
								{/foreach}
							{elseif $baseLigne eq "lieux" or $baseLigne eq "ressources" or $baseLigne eq "heures" }
								{foreach from=$triPlanningPossibleAutre item=triTemp}
									{assign var=chaineTmp value="triAutre_"|cat:$triTemp|replace:' ':'_'|replace:',':'_'}
									<a class="dropdown-item" href="process/planning.php?triPlanning={$triTemp|urlencode}">{if $triTemp eq $triPlanning}<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;{else}<i style="margin-left:19px;">&nbsp;</i>{/if}{$smarty.config.$chaineTmp}</a>
								{/foreach}
							{/if}
						</div>
					</div>
					{* DIV POUR CHOIX EXPORT *}
					<div class="btn-group pt-2" id="dropdownExport">
						<button class="btn dropdown-toggle btn-default" data-toggle="dropdown" onclick="javascript:multiselecthide();"><i class="fa fa-cloud-download fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-xl-inline-block">&nbsp;&nbsp;{#choix_export#}</span>&nbsp;<span class="caret"></span></button>
						<div class="dropdown-menu" style="">
							<a class="dropdown-item" href="javascript:window.print();"><i class="fa fa-fw fa-print" aria-hidden="true"></i> {#printAll#|xss_protect}</a>
							<a class="dropdown-item" href="export_csv.php"><i class="fa fa-fw fa-file-text-o" aria-hidden="true"></i> {#CSVExport#|xss_protect}</a>
							<a class="dropdown-item" href="export_csv_raw.php"><i class="fa fa-fw fa-file-text-o" aria-hidden="true"></i> {#CSVExportRaw#|xss_protect}</a>
							<a class="dropdown-item" href="javascript:xajax_choixPDF();undefined;"><i class="fa fa-fw fa-file-pdf-o" aria-hidden="true"></i> {#PDFExport#|xss_protect}</a>
							<a class="dropdown-item" href="export_xls.php" target="_blank"><i class="fa fa-fw fa-file-excel-o" aria-hidden="true"></i> {#xlsExport#|xss_protect}</a>
							<a class="dropdown-item" href="export_gantt.php" target="_blank"><i class="fa fa-fw fa-file-pdf-o" aria-hidden="true"></i> {#ganttExport#|xss_protect}</a>
							<a class="dropdown-item" href="export_pdf_calendrier.php" target="_blank"><i class="fa fa-fw fa-calendar-o" aria-hidden="true"></i> {#calendarExport#|xss_protect}</a>
							<a class="dropdown-item" href="javascript:xajax_choixIcal();undefined;"><i class="fa fa-fw fa-envelope-o" aria-hidden="true"></i> {#icalExport#|xss_protect}</a>
						</div>
					</div>
					{* DIV POUR CHOIX DIMENSION CASE ET AFFICHAGE LARGE REDUIT *}
					<div class="btn-group pt-2" id="dropdownLarge">
						{if $dimensionCase eq "reduit"}
							<a class="btn btn-default" title="{#menuPlanningLarge#}" href="process/planning.php?dimensionCase=large"><i class="fa fa-search-plus fa-lg fa-fw" aria-hidden="true"></i></a>
						{else}
							<a class="btn btn-default" title="{#menuPlanningReduit#}" href="process/planning.php?dimensionCase=reduit"><i class="fa fa-search-minus fa-lg fa-fw" aria-hidden="true"></i></a>
						{/if}
						<button class="btn dropdown-toggle btn-default" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-sort fa-lg fa-fw" aria-hidden="true"></i></button>
						<div class="dropdown-menu">						
							{if $fleches eq '1'}
								<a class="dropdown-item" href="{$BASE}/process/planning.php?fleches=0">
								<i class="fa fa-check" aria-hidden="true"></i>&nbsp;&nbsp;
							{else}
								<a class="dropdown-item" href="{$BASE}/process/planning.php?fleches=1"><i style="margin-left:19px;">&nbsp;</i>
							{/if}
							{#scrolls_fleches#}</a>
						</div>
					</div>

					{* DIV POUR RECHERCHE TEXTE *}
					<div class="btn-group pt-2 d-none d-xl-inline-block" id="searchboxPlanning">
						<form action="process/planning.php" method="POST">
							<div class="input-group">
								<input type="text" class="tooltipster form-control input-sm" name="filtreTexte" value="{$filtreTexte|xss_protect}" maxlength="50" title="{#formFiltreTexte#|escape}" id="filtreTexte" />
								<div class="input-group-append">
									<button type="submit" class="btn btn-sm {if $filtreTexte != ""}btn-danger{else}btn-default{/if}">
									<i class="fa fa-search fa-lg fa-fw" aria-hidden="true"></i></button>
									{if $filtreTexte != ""}
										<div class="btn-group">
											<button class="btn btn-default dropdown-toggle" data-toggle="dropdown">&nbsp;<span class="caret"></span></button>
											<ul class="dropdown-menu">
												<li><a href="process/planning.php?desactiverFiltreTexte=1">{#formFiltreUserDesactiver#}</a></li>
											</ul>
										</div>
									{/if}
								</div>
							</div>
						</form>
					</div>
			</div>
		</div>