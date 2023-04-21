{* Smarty *}
{include file="www_header.tpl"}

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					{if in_array("projectgroups_manage_all", $user.tabDroits)}
						<a href="groupe_list.php" class="btn btn-default"><i class="fa fa-folder-o fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp; {#menuListeGroupes#}</a>
					{/if}
					<a href="javascript:xajax_ajoutProjet('projets');undefined;" class="btn btn-default"><i class="fa fa-bookmark fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuAjouterProjet#}</a>
				</div>				
			</div>
		</div>
	</div>
	<form action="projets.php" method="POST" id="filtreprojet">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				<div class="form-group row col-md-12">
					<div id="projectListLabel">
					<label class="col-form-label w140">{#projet_liste_afficherProjets#} :</label>
					</div>
					<div id="projectListButton">
					<div class="btn-group" data-toggle="buttons-radio">
						<button type="button" class="btn btn-default{if $filtrageProjet eq 'tous'} btn-primary active{/if}" onclick="top.location='?filtrageProjet=tous';">{#projet_liste_afficherProjetsTous#}</button>
						<button type="button" class="btn btn-default{if $filtrageProjet neq 'tous'} btn-primary active{/if}" onclick="top.location='?filtrageProjet=date';">{#projet_liste_afficherProjetsParDate#}</button>
					</div>
				
					<div class="btn-group">
					<button type="button" class="btn {if $filtreGroupeProjet|@count > 0}btn-danger{else}btn-default{/if} dropdown-toggle" data-toggle="dropdown" data-display="static">{#filtreGroupeProjet#}&nbsp;<span class="caret"></span></button>
					<ul class="dropdown-menu">
						{if $filtreGroupeProjet|@count > 0}
							<a href="?desactiverfiltreGroupe=1" class="btn btn-sm btn-danger ml-2">{#formFiltreProjetDesactiver#}</a>
						{/if}
						<li>
							<input type="hidden" name="filtreGroupeProjet" value="1" />
							<table onClick="event.cancelBubble=true;" class="ml-2 mr-2">
								<tr>
									<td>
										<input type="checkbox" id="gp0" name="gp0" value="1" {if in_array("gp0", $filtreGroupeProjet)}checked="checked"{/if} /><label for="gp0" style="display:inline">&nbsp;<b>{#projet_liste_sansGroupes#}</b></label>
										{if $groupeProjets|@count > 0}
											{math assign=nbColonnes equation="ceil(nbGroupes / nbGroupesParColonnes)" nbGroupes=$groupeProjets|@count nbGroupesParColonnes=$smarty.const.FILTER_NB_USERS_PER_COLUMN}
											{math assign=maxCol equation="ceil(nbGroupes / nbColonnes)" nbGroupes=$groupeProjets|@count nbColonnes=$nbColonnes}
											{assign var=tmpNbDansColCourante value="0"}
											{foreach from=$groupeProjets item=groupeCourant name=loopGroupes}
												<br/>
												{if $tmpNbDansColCourante >= $maxCol}
													{assign var=tmpNbDansColCourante value="0"}
													</td>
													<td>
												{/if}
												<input type="checkbox" id="gp{$groupeCourant.groupe_id}" name="gp[]" value="{$groupeCourant.groupe_id}" {if in_array($groupeCourant.groupe_id, $filtreGroupeProjet)}checked="checked"{/if} /> <label for="gp{$groupeCourant.groupe_id}" style="display:inline">{$groupeCourant.nom|xss_protect}</label>
												{assign var=tmpNbDansColCourante value=$tmpNbDansColCourante+1}
											{/foreach}
										{/if}
									</td>
								</tr>
							</table>
						</li>
						<li><input type="submit" value="{#submit#}" class="btn btn-sm btn-primary ml-2 mt-2" /></li>
					</ul>
					</div>
					</div>
					
					{if $filtrageProjet neq "tous"}
						<div id="projectNbMonth" class="form-group form-inline">					
						<label class="col-form-label">{#formNbMois#} :&nbsp;</label>
							<div class="input-group">
								<input type="text" name="nb_mois" class="form-control" size="1" value="{$nbMois|xss_protect}" />
								<span class="input-group-append">
									<button class="btn btn-default" type="submit"><i class="fa fa-arrow-right fa-lg fa-fw" aria-hidden="true"></i></button>
								</span>
							</div>
						</div>
						<div id="projectFromDate" class="form-group form-inline">					
						<label class="col-form-label">&nbsp;{#formDebut#} :&nbsp;</label>
							<div class="input-group">
							{if $smarty.session.isMobileOrTablet==1}
								<input type="date" name="date_debut_affiche_projet" id="date_debut_affiche_projet" value="{$dateDebut|forceISODateFormat}" class="form-control" />
							{else}
								<input type="text" name="date_debut_affiche_projet" id="date_debut_affiche_projet" value="{$dateDebut}" class="form-control datepicker" />
							{/if}
							<span class="input-group-append">
									<button class="btn btn-default" type="button"><i class="fa fa-arrow-right fa-lg fa-fw" aria-hidden="true"></i></button>
								</span>
							</div>
						</div>
						<div id="projectToDate">
						<label class="col-form-label">{#formInfoDateFin#} : {$dateFin}</label>
						</div>	
					{/if}
			</div>
		</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
					<div id="projectStatusLabel">
					<label class="col-form-label w140 nowrap">{#projet_liste_filtreProjets#} :</label>
					</div>
					<div id="projectStatusCheckbox">
					<div class="form-group">
						{foreach from=$listeStatus item=status}
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="checkbox" name="statut[]" id="{$status.status_id}" value="{$status.status_id}" onclick="javascript:$('#filtreprojet').submit();" {if in_array($status.status_id, $listeStatuts)}checked="checked"{/if}>
							<label class="form-check-label" for="{$status.status_id}">{$status.nom}</label>
						</div>
						{/foreach}

					<div class="btn-group" id="projectSearchbox">
						<div class="input-group">
							<input type="text" class="form-control" name="rechercheProjet" value="{$rechercheProjet|xss_protect|default:""}" placeholder="{#taches_groupeRecherche#}" />
							<span class="input-group-append">
								<button type="submit" class="btn {if $rechercheProjet != ""}btn-danger{else}btn-default{/if}"><i class="fa fa-search fa-lg fa-fw" aria-hidden="true"></i></button>
							</span>
						</div>
					</div>
				</div>
					</div>
			</div>
		</div>
	</div>
	</form>
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				<table class="table table-striped table-hover" id="projectTab">
					<thead>
					<tr>
						<th colspan="" class="d-none d-md-table-cell">{$projets|@count} {#projet_liste_projet#}</th>
						<th class="">
							{if $order eq "projet_id"}
								{if $by eq "asc"}
									<a href="?order=projet_id&by=desc">{#winProjet_identifiant#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=projet_id&by=asc">{#winProjet_identifiant#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=projet_id&by={$by}">{#winProjet_identifiant#}</a>
							{/if}
						</th>
						<th class="">
							{if $order eq "nom"}
								{if $by eq "asc"}
									<a href="?order=nom&by=desc">{#winProjet_nomProjet#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=nom&by=asc">{#winProjet_nomProjet#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=nom&by={$by}">{#winProjet_nomProjet#}</a>
							{/if}
						</th>
						<th class="projectTabColCreator">
							{if $order eq "nom_createur"}
								{if $by eq "asc"}
									<a href="?order=nom_createur&by=desc">{#projet_liste_createur#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=nom_createur&by=asc">{#projet_liste_createur#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=nom_createur&by={$by}">{#projet_liste_createur#}</a>
							{/if}
						</th>
						<th class="d-none d-md-table-cell">
							{if $order eq "charge"}
								{if $by eq "asc"}
									<a href="?order=charge&by=desc">{#projet_liste_charge#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=charge&by=asc">{#projet_liste_charge#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=charge&by={$by}">{#projet_liste_charge#}</a>
							{/if}
						</th>
						<th class="d-none d-md-table-cell">
							{if $order eq "livraison"}
								{if $by eq "asc"}
									<a href="?order=livraison&by=desc">{#projet_liste_livraison#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=livraison&by=asc">{#projet_liste_livraison#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=livraison&by={$by}">{#projet_liste_livraison#}</a>
							{/if}
						</th>
						<th class="projectTabColComment">
							{#projet_liste_commentaires#}
						</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td colspan="7" class="project-group-head">{#projet_liste_sansGroupes#}</td>
					</tr>
					{assign var=groupeCourant value=""}
					{foreach from=$projets item=projet}
						{if $projet.groupe_id neq $groupeCourant}
							<tr>
							<td colspan="7" class="project-group-head">{$projet.nom_groupe|xss_protect}</td>
						{/if}
						<tr>
							<td class="w140">
								{if in_array("projects_manage_all", $user.tabDroits) || (in_array("projects_manage_own", $user.tabDroits) && $projet.createur_id eq $user.user_id)}
									<a href="javascript:xajax_modifProjet('{$projet.projet_id}', 'projets');undefined;"><i class="fa fa-pencil fa-lg fa-fw" aria-hidden="true"></i></a>
									<a href="javascript:xajax_supprimerProjet('{$projet.projet_id}');undefined;" 
									onclick="javascript: return confirm('{#projet_liste_confirmSuppr#|xss_protect}')"><i class="fa fa-trash-o fa-lg fa-fw" aria-hidden="true"></i></a>
								{/if}
								<a class="tooltipster" href="{$BASE}/process/planning.php?filtreSurProjet={$projet.projet_id}" title="{#planning_filtre_sur_projet#|xss_protect}"><i class="fa fa-calendar fa-lg fa-fw" aria-hidden="true"></i></a>
								{if in_array("projects_manage_all", $user.tabDroits) || (in_array("projects_manage_own", $user.tabDroits) && $projet.createur_id eq $user.user_id)}
									<a class="tooltipster" href="javascript:xajax_projet_copie_form('{$projet.projet_id}');undefined;" title="{#projet_copie_infobulle#|xss_protect}"><i class="fa fa-copy fa-lg fa-fw" aria-hidden="true"></i></a>
								{/if}
								{if $projet.lien <> ''}
									<a href="{if $projet.lien|strpos:"http" !== 0 && $projet.lien|strpos:"\\" !== 0}http://{/if}{$projet.lien}" title="{#winProjet_gotoLien#|xss_protect}" target="_blank"><i class="fa fa-globe fa-lg fa-fw" aria-hidden="true"></i></a>
								{/if}
								{if $smarty.const.CONFIG_PLANNING_AFFICHAGE_STATUS eq 'aucun'}
									{elseif $smarty.const.CONFIG_PLANNING_AFFICHAGE_STATUS eq 'nom'}{$projet.statut_nom}
									{elseif $smarty.const.CONFIG_PLANNING_AFFICHAGE_STATUS eq 'pourcentage'}
									<div class="progress tooltipster" title="{$projet.statut_nom}">
											<div class="progress-bar" style="width: {$projet.statut_pourcentage}%;background-color:#{$projet.statut_couleur};color:{"#"|cat:$projet.couleur|buttonFontColor}">{$projet.statut_pourcentage}%</div>
									</div>
									{elseif $smarty.const.CONFIG_PLANNING_AFFICHAGE_STATUS eq 'pastille'}<div class="pastille-statut tooltipster" style="background-color:#{$projet.statut_couleur};display:inline-block;margin-left:20px" title="{$projet.statut_nom}"></div>
								{/if}
							</td>
							<td>
								<span class="pastille-projet" style="background-color:#{$projet.couleur};color:{"#"|cat:$projet.couleur|buttonFontColor}">{$projet.projet_id}</span>
							</td>
							<td>
								{$projet.nom|xss_protect}
							</td>
							<td class="projectTabColCreator">
								{$projet.nom_createur|xss_protect}
							</td>
							<td class="d-none d-md-table-cell">{$projet.charge}</td>
							<td class="d-none d-md-table-cell">
								{if $projet.livraison neq '' && $projet.livraison neq '0000-00-00'}
									<a href="planning.php?livraison={$projet.livraison|sqldate2userdate}">{$projet.livraison|sqldate2userdate}</a>
								{/if}
							</td>
							<td class="wrap projectTabColComment">
								{assign var=cooltip value=$smarty.config.user_liste_NBPeriodes|cat:" : "|cat:$projet.totalPeriodes}
								
								<span class="tooltipster" title="{$cooltip}"><i class="fa fa-info-circle fa-lg fa-fw cursor-help " aria-hidden="true"></i></span>
								&nbsp;&nbsp;&nbsp;
								{$projet.iteration|xss_protect}
							</td>
						</tr>
						{assign var=groupeCourant value=$projet.groupe_id}
					{/foreach}
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

{* CHARGEMENT SCROLL Y *}

<script>
	{literal}

	var yscroll = getCookie('yposProjets');
	window.onscroll = function() {document.cookie='yposProjets=' + window.pageYOffset;};
	addEvent(window, 'load', chargerYScrollPos);
	$('#rechercheProjet').keypress(function(event) {
		if (event.keyCode == 13 || event.which == 13) {
			$('#filtreProjet').submit();
			event.preventDefault();
		}
	});
	{/literal}
</script>

{include file="www_footer.tpl"}