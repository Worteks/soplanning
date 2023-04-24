{* Smarty *}
{include file="www_header.tpl"}

<div class="container">
	{if !in_array("tasks_readonly", $user.tabDroits)}
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					<a href="javascript:xajax_ajoutPeriode();undefined;" class="btn btn-default"><i class="fa fa-calendar-plus-o fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuAjouterPeriode#}</a>
				</div>				
			</div>
		</div>
	</div>
	{/if}
	<form action="taches.php" method="POST" id="filtreTaches">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				<div class="form-group row col-md-12">
					<label class="col-form-label w100">{#taches_filtreDate#} :</label>
					<div class="form-inline">
					&nbsp;&nbsp;{#formDebut#} :&nbsp;&nbsp;
						{if $smarty.session.isMobileOrTablet==1}
							<input name="date_debut_affiche_tache" id="date_debut_affiche_tache" type="date" value="{$dateDebut|forceISODateFormat}" class="form-control" />
						{else}
							<input name="date_debut_affiche_tache" id="date_debut_affiche_tache" type="text" value="{$dateDebut}" class="form-control datepicker" />
						{/if}
					&nbsp;&nbsp;{#formFin#} :&nbsp;&nbsp;
						{if $smarty.session.isMobileOrTablet==1}
						<input name="date_fin_affiche_tache" id="date_fin_affiche_tache" type="date" value="{$dateFin|forceISODateFormat}" class="form-control" />
						{else}
						<input name="date_fin_affiche_tache" id="date_fin_affiche_tache" type="text" value="{$dateFin}" class="form-control datepicker" />
						{/if}
				<button id="dateFilterButtonTask" class="btn btn-default ml-2" onClick="$('filtreTaches').submit();"><i class="fa fa-search fa-lg fa-fw" aria-hidden="true"></i></button>
					</div>
						<div id="taskListGroupeLabel">
					<label class="col-form-label offset-md-2 w100">{#taches_groupeTaches#} :</label>
				</div>
				<div id="taskListGroupeButton">
				<div class="btn-group" data-toggle="buttons-radio">
					<button type="button" class="btn btn-default{if $grouperpar eq 'status'} btn-primary active{/if}" onclick="top.location='?grouperpar=status';">{#taches_groupeStatus#}</button>
					<button type="button" class="btn btn-default{if $grouperpar eq 'project'} btn-primary active{/if}" onclick="top.location='?grouperpar=project';">{#taches_groupeProjet#}</button>
					<button type="button" class="btn btn-default{if $grouperpar eq 'utilisateur'} btn-primary active{/if}" onclick="top.location='?grouperpar=utilisateur';">{#taches_groupeUtilisateur#}</button>
				</div>
				</div>
				</div>
			</div>
		</div>
	</div>	
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
					<div id="taskListFiltreLabel">
					<label class="col-form-label w100">{#taches_filtreTaches#} :</label>
					</div>
					<div id="taskListFiltreButton">
					<div class="btn-group" data-toggle="buttons-radio">
						<button type="button" class="btn btn-default{if $filtreTaches eq 'mestaches'} btn-primary active{/if}" onclick="top.location='?filtreTaches=mestaches';">{#taches_MesTaches#}</button>
						<button type="button" class="btn btn-default{if $filtreTaches eq 'tous'} btn-primary active{/if}" onclick="top.location='?filtreTaches=tous';">{#taches_ToutesTaches#}</button>
					</div>				
					<div class="btn-group" id="dropdownTaskProjectFilter">
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
					</div>

					<div class="btn-group" id="dropdownTaskUserFilter">
						<input type="hidden" name="filtreUser" value="1" />
						<select name="filtreUser" multiple="multiple" id="filtreUser" class="d-none multiselect">
							{if $users|@count eq 0}
								<option>&nbsp;{#formFiltreProjetAucunProjet#}</option>
							{else}
								<optgroup id="e0" label="{#cocheUserSansGroupe#}">
								{assign var=groupeTemp value=""}
								{foreach from=$users item=userCourant}
									{if $userCourant.user_groupe_id neq $groupeTemp}
										</optgroup><optgroup id="e{$userCourant.user_groupe_id}" label="{$userCourant.groupe_nom}">
									{/if}
								<option value="{$userCourant.user_id}" {if in_array($userCourant.user_id, $filtreUser)}selected="selected"{/if}>{$userCourant.nom|xss_protect} ({$userCourant.user_id|xss_protect})</option> 								
								{assign var=groupeTemp value=$userCourant.user_groupe_id}
								{/foreach}
							{/if}
							</optgroup></select>
					</div>
					
					<div class="btn-group" id="dropdownTaskLieuxFilter">
										{* Filtres avancés emplacement *}
										{if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX == 1 and ($listeLieux|@count) > 0 }
										<button class="btn {if $filtreGroupeLieu|@count > 0}btn-danger{else}btn-default{/if} dropdown-toggle" data-toggle="dropdown" data-display="static"><i class="fa fa-map-marker fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;{#taches_filtreLieux#}</span></button>
										<ul class="dropdown-menu">
										<li>
										<table onClick="event.cancelBubble=true;" class="planning-filter"><tr>
										<td class="planningDropdownFilter">											
											<input type="hidden" name="filtreGroupeLieu" value="1">
											<input type="hidden" name="maxGroupeLieu" value="{$listeLieux|@count}">
												<div class="form-horizontal col-md-12">
												{assign var=groupeTemp value=""}
												{math assign=nbColonnes equation="ceil(nbLieux / nbLieuxParColonnes)" nbLieux=$listeLieux|@count nbLieuxParColonnes=$smarty.const.FILTER_NB_AERA_PER_COLUMN}
												{math assign=maxCol equation="ceil(nbLieux / nbColonnes)" nbLieux=$listeLieux|@count nbColonnes=$nbColonnes}
												{assign var=tmpNbDansColCourante value="0"}
												{foreach from=$listeLieux item=lieuCourant name=loopLieux}
													{if $tmpNbDansColCourante >= $maxCol}
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
											</tr></table>
										</li>
										<li><input type="submit" value="{#submit#}" class="btn btn-default ml-2" /></li>
										</ul>
										{/if}							
					</div>										
										{* Filtres avancés ressources *}
										{if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES == 1 and ($listeRessources|@count) > 0 }									
					<div class="btn-group" id="dropdownTaskRessourceFilter">						
						<button class="btn {if $filtreGroupeRessource|@count > 0}btn-danger{else}btn-default{/if} dropdown-toggle" data-toggle="dropdown" data-display="static"><i class="fa fa-plug fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;{#taches_filtreRessources#}</span></button>
										<ul class="dropdown-menu">
										<li>
										<table onClick="event.cancelBubble=true;" class="planning-filter"><tr>
											<td class="planningDropdownFilter">
											<input type="hidden" name="maxGroupeRessource" value="{$listeRessources|@count}">
											<input type="hidden" name="filtreGroupeRessource" value="1">
												<div class="form-horizontal col-md-12">
												{assign var=groupeTemp value=""}
												{math assign=nbColonnes equation="ceil(nbRessources / nbRessourcesParColonnes)" nbRessources=$listeRessources|@count nbRessourcesParColonnes=$smarty.const.FILTER_NB_RESSOURCES_PER_COLUMN}
												{math assign=maxCol equation="ceil(nbRessources / nbColonnes)" nbRessources=$listeRessources|@count nbColonnes=$nbColonnes}
												{assign var=tmpNbDansColCourante value="0"}
												{foreach from=$listeRessources item=ressourceCourant name=loopRessources}
													{if $tmpNbDansColCourante >= $maxCol}
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
											</tr></table>
											</li>
											<li><input type="submit" value="{#submit#}" class="btn btn-default ml-2" /></li>
											</ul>
											</div>
										{/if}
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
					<div id="taskStatusLabel">
					<label class="col-form-label w140">{#taches_filtreStatut#} :</label>
					</div>
					<div id="taskStatusCheckbox">
					{foreach from=$listeStatusTaches item=statut}
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="statut[]" id="{$statut.status_id}" value="{$statut.status_id}" onclick="$('#filtreTaches').submit();" {if in_array($statut.status_id, $listeStatuts)}checked="checked"{/if}>
						<label class="form-check-label" for="{$statut.status_id}">{$statut.nom}</label>
					</div>
					{/foreach}
					<div class="btn-group" id="taskSearchbox" >
						<div class="input-group">
							<input type="text" class="form-control input-sm" name="rechercheTaches" value="{$rechercheTaches|default:""}" placeholder="{#taches_groupeRecherche#}" />
							<span class="input-group-append">
								<button class="btn {if $rechercheTaches != ""}btn-danger{else}btn-default{/if}" type="submit"><i class="fa fa-search fa-lg fa-fw" aria-hidden="true"></i></button>
							</span>
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
				<table class="table table-striped table-hover" id="taskTab">
					<thead>
					<tr>
						<th colspan="3">
							{if $order eq "nom"}
								{if $by eq "asc"}
									<a href="?order=nom&by=desc">{#taches_liste_taches#} ({$projets|@count})</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=nom&by=asc">{#taches_liste_taches#} ({$projets|@count})</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=nom&by={$by}">{#taches_liste_taches#} ({$projets|@count})</a>
							{/if}
						</th>
						<th class="taskTabColTitre">
							{if $order eq "titre"}
								{if $by eq "asc"}
									<a href="?order=titre&by=desc">{#taches_tache#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=titre&by=asc">{#taches_tache#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=titre&by={$by}">{#taches_tache#}</a>
							{/if}
						</th>
						<th class="taskTabColCreator">
							{if $order eq "nom_personne"}
								{if $by eq "asc"}
									<a href="?order=nom_personne&by=desc">{#taches_personne#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=nom_personne&by=asc">{#taches_personne#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=nom_personne&by={$by}">{#taches_personne#}</a>
							{/if}
						</th>

						<th class="wrap d-none d-lg-table-cell">
							{if $order eq "date_debut"}
								{if $by eq "asc"}
									<a href="?order=date_debut&by=desc">{#taches_date_debut#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=date_debut&by=asc">{#taches_date_debut#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=date_debut&by={$by}">{#taches_date_debut#}</a>
							{/if}
						</th>
						<th class="wrap d-none d-lg-table-cell">
							{if $order eq "date_fin"}
								{if $by eq "asc"}
									<a href="?order=date_fin&by=desc">{#taches_date_fin#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
								{else}
									<a href="?order=date_fin&by=asc">{#taches_date_fin#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
								{/if}
							{else}
								<a href="?order=date_fin&by={$by}">{#taches_date_fin#}</a>
							{/if}
						</th>
						<th class="wrap d-none d-md-table-cell d-lg-table-cell">
							{#projet_liste_commentaires#}
						</th>
					</tr>
					</thead>
					<tbody>
					{if $grouperpar eq 'project' }
						{assign var=projetCourant value=""}
					{elseif $grouperpar eq 'status' }
						{assign var=statutCourant value=""}
					{elseif $grouperpar eq 'utilisateur' }
						{assign var=personneCourant value=""}
					{/if}
					{foreach from=$projets item=projet}
						{if $grouperpar eq 'project' }
							{if $projet.projet_id neq $projetCourant}
								<tr>
								<td colspan="8" class="task-group-head">{$projet.nom|xss_protect}</td>
							{/if}
						{elseif $grouperpar eq 'status'}
							{if $projet.statut_tache neq $statutCourant}
								<tr>
								<td colspan="8" class="task-group-head">
									{$projet.status_nom}
								</td>
							{/if}
						{elseif $grouperpar eq 'utilisateur'}
							{if $projet.nom_personne neq $personneCourant}
								<tr>
								<td colspan="8" class="task-group-head">{$projet.nom_personne}</td>
							{/if}
						{/if}
						<tr>
							<td style="background-color:#{$projet.couleur};color:{"#"|cat:$projet.couleur|buttonFontColor}">{$projet.projet_id}</td>
							<td class="w140">
								{if in_array("tasks_modify_all", $user.tabDroits) || (in_array("tasks_modify_own_project", $user.tabDroits) && $projet.createur_id eq $user.user_id) || (in_array("tasks_modify_own_task", $user.tabDroits) && ($projet.user_id eq $user.user_id) || $projet.createur_id eq $user.user_id) || ( in_array("tasks_modify_team", $user.tabDroits) && $user.user_groupe_id eq $projet.user_groupe_id)}									<a href="javascript:xajax_modifPeriode('{$projet.periode_id}');undefined;"><i class="fa fa-pencil fa-lg fa-fw" aria-hidden="true"></i></a>
									<a href="javascript:xajax_supprimerPeriode('{$projet.periode_id}');undefined;" 
									onclick="javascript: return confirm('{#taches_tache_confirmSuppr#|escape:"javascript"}')"><i class="fa fa-trash-o fa-lg fa-fw" aria-hidden="true"></i></a>
								{/if}
								<a href="{$BASE}/process/planning.php?filtreSurProjet={$projet.projet_id}" title="{#planning_filtre_sur_projet#|escape}"><i class="fa fa-calendar fa-lg fa-fw" aria-hidden="true"></i></a>
								{if $projet.lien <> ''}
								<a href="{if $projet.lien|strpos:"http" !== 0 && $projet.lien|strpos:"\\" !== 0}http://{/if}{$projet.lien}" title="{#winProjet_gotoLien#|escape}" target="_blank"><i class="fa fa-globe fa-lg fa-fw" aria-hidden="true"></i></a>
								{else}
								{/if}
							</td>
							{if $grouperpar neq 'status'}
								<td>
								{$projet.status_nom}
								</td>
							{else}
							<td></td>
							{/if}
							<td class="taskTabColTitre">
								{$projet.titre|xss_protect}
							</td>
							<td class="taskTabColCreator">
								{$projet.nom_personne|xss_protect}
							</td>
							<td class="wrap d-none d-lg-table-cell">
								{if $projet.date_debut neq '' && $projet.date_debut neq '0000-00-00'}
									{$projet.date_debut|sqldate2userdate}
								{/if}
							</td>
							<td class="wrap d-none d-lg-table-cell">
								{if $projet.date_fin neq '' && $projet.date_debut neq '0000-00-00'}
									{$projet.date_fin|sqldate2userdate}
								{else}
									{if $projet.duree_details == 'AM'}
										{#options_dureeDefautMatin#}
									{elseif $projet.duree_details == 'PM'}
										{#options_dureeDefautApresmidi#}
									{elseif $projet.duree_details == 'duree'}
										{$projet.duree}									
									{else}
										{$projet.duree_details}
									{/if}	
								{/if}
							</td>
							<td class="wrap d-none d-md-table-cell d-lg-table-cell">{$projet.notes|xss_protect}</td>
						</tr>
						{if $grouperpar eq 'project' }
							{assign var=projetCourant value=$projet.projet_id}
						{elseif $grouperpar eq 'status'}
							{assign var=statutCourant value=$projet.statut_tache}
						{elseif $grouperpar eq 'utilisateur'}
							{assign var=personneCourant value=$projet.nom_personne}
						{/if}
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

	var yscroll = getCookie('yposTaches');
	window.onscroll = function() {document.cookie='yposTaches=' + window.pageYOffset;};
	addEvent(window, 'load', chargerYScrollPos);
	$('#rechercheTaches').keypress(function(event) {
		if (event.keyCode == 13 || event.which == 13) {
			$('#filtreTaches').submit();
			event.preventDefault();
		}
	});
	{/literal}
</script>
<script>
var listeProjets = new Array();
listeProjets[0] = new Array();
{assign var=groupeTemp value=""}
{foreach from=$listeProjets item=projetCourant}
	{if $projetCourant.groupe_id neq $groupeTemp}
		listeProjets[{$projetCourant.groupe_id}] = new Array();
	{/if}
	{if $projetCourant.groupe_id eq ''}
		listeProjets[0].push('{$projetCourant.projet_id}');
	{else}
		listeProjets[{$projetCourant.groupe_id}].push('{$projetCourant.projet_id}');
	{/if}
	{assign var=groupeTemp value=$projetCourant.groupe_id}
{/foreach}

{literal}
// coche ou decoche tous les projets
function filtreGroupeProjetCocheTous(action) {
	for (var groupe in listeProjets) {
		if (!document.getElementById('g' + groupe)) {
			// si pas une case ? cocher existantes, on sort
			continue;
		}
		document.getElementById('g' + groupe).checked = action;
		for (var projet in listeProjets[groupe]) {
			if (!document.getElementById('projet_' + listeProjets[groupe][projet])) {
				// si pas une case ? cocher existantes, on sort
				continue;
			}
			document.getElementById('projet_' + listeProjets[groupe][projet]).checked = action;
		}
	}
}

// coche ou decoche les projets d'un groupe
function filtreCocheGroupe(groupe) {
	var action = document.getElementById('g' + groupe).checked;
	for (var projet in listeProjets[groupe]) {
		if (!document.getElementById('projet_' + listeProjets[groupe][projet])) {
			// si pas une case ? cocher existantes, on sort
			continue;
		}
		document.getElementById('projet_' + listeProjets[groupe][projet]).checked = action;
	}
}
{/literal}
{literal}
$("#filtreGroupeProjet").multiselect({
	selectAll:false,
	noUpdatePlaceholderText:true,
	nameSuffix: 'projet',
	desactivateUrl: 'taches.php?desactiverFiltreGroupeProjet=1',
	placeholder: '{/literal}<i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;{#taches_filtreProjets#}</span>{literal}',
	texts: {
       selectAll    : '{/literal}{#formFiltreProjetCocherTous#}{literal}',
       unselectAll    : '{/literal}{#formFiltreProjetDecocherTous#}{literal}',
	   disableFilter : '{/literal}{#formFiltreProjetDesactiver#}{literal}',
	   validateFilter : '{/literal}{#submit#}{literal}',
	   search : '{/literal}{#search#}{literal}'
	},
});
$("#filtreGroupeProjet").show();

$("#filtreUser").multiselect({
	selectAll:false,
	noUpdatePlaceholderText:true,
	nameSuffix: 'user',
	desactivateUrl: 'taches.php?desactiverFiltreUser=1',
	placeholder: '{/literal}<i class="fa fa-user fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;{#formChoixUser#}</span>{literal}',
	texts: {
       selectAll    : '{/literal}{#formFiltreProjetCocherTous#}{literal}',
       unselectAll    : '{/literal}{#formFiltreProjetDecocherTous#}{literal}',
	   disableFilter : '{/literal}{#formFiltreProjetDesactiver#}{literal}',
	   validateFilter : '{/literal}{#submit#}{literal}',
	   search : '{/literal}{#search#}{literal}'
	},
});
$("#filtreUser").show();
$(document).scroll(function() {
	$(".ms-search").css({'position': 'relative'});
	$(".ms-options-wrap > .ms-options > ul").css({'margin-top': '0px'});
});
$(".ms-options").scroll(function() {
	$(".ms-search").css({'position': 'fixed'});
	$(".ms-options-wrap > .ms-options > ul").css({'margin-top': '70px'});
});		
{/literal}
</script>
{include file="www_footer.tpl"}