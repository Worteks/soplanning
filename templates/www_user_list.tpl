{* Smarty *}

{include file="www_header.tpl"}

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box form-inline">
				<div class="btn-group">
					<a href="javascript:xajax_modifUser();undefined;" class="btn btn-default" ><i class="fa fa-user-plus fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuCreerUser#}</a>
					<a href="{$BASE}/user_groupes.php" class="btn btn-default"><i class="fa fa-users fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuGroupesUsers#}</a>
				</div>

				<div class="btn-group">
					<form method="POST">
					<a href="#" class="btn {if $filtreEquipe|@count > 0}btn-danger{else}btn-default{/if} dropdown-toggle" data-toggle="dropdown" data-display="static">{#filtreEquipe#}&nbsp;<span class="caret"></span></a>
					<ul class="dropdown-menu">
						{if $filtreEquipe|@count > 0}
							<a href="?desactiverfiltreEquipe=1" class="btn btn-danger btn-sm ml-2">{#formFiltreUserDesactiver#}</a>
						{/if}
						<li>
							<input type="hidden" name="filtreEquipe" value="1">
							<table onClick="event.cancelBubble=true;" class="ml-2 mr-2">
								<tr>
									<td>
										<input type="checkbox" id="gu0" name="gu0" value="1" {if in_array("gu0", $filtreEquipe)}checked="checked"{/if} /><label for="gu0" style="display:inline">&nbsp;<b>{#cocheUserSansGroupe#}</b></label>

										{if $equipes|@count > 0}
											{math assign=nbColonnes equation="ceil(nbEquipes / nbEquipesParColonnes)" nbEquipes=$equipes|@count nbEquipesParColonnes=$smarty.const.FILTER_NB_USERS_PER_COLUMN}
											{math assign=maxCol equation="ceil(nbEquipes / nbColonnes)" nbEquipes=$equipes|@count nbColonnes=$nbColonnes}
											{assign var=tmpNbDansColCourante value="0"}
											{foreach from=$equipes item=equipeCourante name=loopEquipes}
												<br/>
												{if $tmpNbDansColCourante >= $maxCol}
													{assign var=tmpNbDansColCourante value="0"}
													</td>
													<td>
												{/if}
												<input type="checkbox" id="gu{$equipeCourante.user_groupe_id}" name="gu[]" value="{$equipeCourante.user_groupe_id}" onClick="filtreCocheUserGroupe('{$equipeCourante.user_groupe_id}')" {if in_array($equipeCourante.user_groupe_id, $filtreEquipe)}checked="checked"{/if} /> <label for="gu{$equipeCourante.user_groupe_id}" style="display:inline">{$equipeCourante.nom|xss_protect}</label>
												{assign var=tmpNbDansColCourante value=$tmpNbDansColCourante+1}
											{/foreach}
										{/if}
									</td>
								</tr>
							</table>
						</li>
						<li><input type="submit" value="{#submit#}" class="btn btn-sm btn-primary ml-2 mt-2" /></li>
					</ul>
				</form>	
				</div>
				<div class="btn-group">
					<form method="POST">
					<div class="input-group">
						<input type="text" class="form-control" name="rechercheUser" value="{$rechercheUser|default:""}" />
						<span class="input-group-append">
							<button type="submit" class="btn {if $rechercheUser != ""}btn-danger{else}btn-default{/if}"><i class="fa fa-search" aria-hidden="true"></i></button>
						</span>
					</div>
					</form>
				</div>
				<div class="btn-group ml-md-1">
					<a href="javascript:xajax_usersBulkRightsForm();undefined;" class="btn btn-default" ><i class="fa fa-files-o fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuUsersBulkRights#}</a>
				</div>
			</div>
		</div>
	</div>

	{if $users|@count > 0}

		<div class="row">
			<div class="col-md-12">
				<div class="soplanning-box mt-2">
					<table class="table table-striped table-hover" id="userTab">
						<tr>
							<th class="w140">&nbsp;</th>
							<th class="userTabColId">
								{if $order eq "user_id"}
									{if $by eq "asc"}
										<a href="{$BASE}/user_list.php?page=1&order=user_id&by=desc">{#user_liste_identifiant#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
									{else}
										<a href="{$BASE}/user_list.php?page=1&order=user_id&by=asc">{#user_liste_identifiant#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
									{/if}
								{else}
									<a href="{$BASE}/user_list.php?page=1&order=user_id&by={$by}">{#user_liste_identifiant#}</a>
								{/if}
							</th>
							<th>
								{if $order eq "nom"}
									{if $by eq "asc"}
										<a href="{$BASE}/user_list.php?page=1&order=nom&by=desc">{#user_liste_nom#} ({$users|@count})</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
									{else}
										<a href="{$BASE}/user_list.php?page=1&order=nom&by=asc">{#user_liste_nom#} ({$users|@count})</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
									{/if}
								{else}
									<a href="{$BASE}/user_list.php?page=1&order=nom&by={$by}">{#user_liste_nom#} ({$users|@count})</a>
								{/if}
							</th>
							<th class="userTabColEmail">
								{if $order eq "email"}
									{if $by eq "asc"}
										<a href="{$BASE}/user_list.php?page=1&order=email&by=desc">{#user_liste_email#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
									{else}
										<a href="{$BASE}/user_list.php?page=1&order=email&by=asc">{#user_liste_email#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
									{/if}
								{else}
									<a href="{$BASE}/user_list.php?page=1&order=email&by={$by}">{#user_liste_email#}</a>
								{/if}
							</th>
							<th class="wrap d-none d-sm-table-cell d-lg-table-cell">
								{#user_liste_groupe#}
							</th>
							<th class="wrap d-none d-lg-table-cell">
								{#user_droits_court#}
							</th>
							<th class="wrap d-none d-lg-table-cell">
								{if $order eq "visible_planning"}
									{if $by eq "asc"}
										<a href="{$BASE}/user_list.php?page=1&order=visible_planning&by=desc">{#user_visiblePlanning#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
									{else}
										<a href="{$BASE}/user_list.php?page=1&order=visible_planning&by=asc">{#user_visiblePlanning#}</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
									{/if}
								{else}
									<a href="{$BASE}/user_list.php?page=1&order=visible_planning&by={$by}">{#user_visiblePlanning#}</a>
								{/if}
							</th>
							<th class="wrap d-none d-lg-table-cell">{#user_informations#}</th>
						</tr>
						{foreach name=users item=userTmp from=$users}
							<tr>
								<td class="w140 nowrap">
									<a href="javascript:xajax_modifUser('{$userTmp.user_id|escape}');undefined;"><i class="fa fa-pencil fa-lg fa-fw" aria-hidden="true"></i></a>
									<a href="javascript:xajax_supprimerUser('{$userTmp.user_id|escape}');undefined;" onClick="javascript:return confirm('{#user_liste_confirmSuppr#|escape:"javascript"}')"><i class="fa fa-trash-o fa-lg fa-fw" aria-hidden="true"></i></a>
									<a class="tooltipster" href="{$BASE}/process/planning.php?filtreSurUser={$userTmp.user_id}" title="{#planning_filtre_sur_user#|escape}"><i class="fa fa-calendar fa-lg fa-fw" aria-hidden="true"></i></a>
								</td>
								<td class="userTabColId"><span class="pastille-user" style="background-color:#{$userTmp.couleur};color:{"#"|cat:$userTmp.couleur|buttonFontColor}">{$userTmp.user_id}</span></td>
								<td>{$userTmp.nom|xss_protect}</td>
								<td class="userTabColEmail">
									{if $userTmp.email neq ""}
										<a href="mailto:{$userTmp.email|xss_protect}">{$userTmp.email|xss_protect}</a>
									{/if}
								</td>
								<td class="wrap d-none d-sm-table-cell d-lg-table-cell">{$userTmp.nom_groupe|xss_protect}</td>
								<td class="wrap d-none d-lg-table-cell">
									{if $userTmp.login_actif eq "non"}
										<span class="badge" style="color:#ff0000">{#compte_desactive#}</span>
									{else}
										{if in_array("users_manage_all", $userTmp.tabDroits)}<span class="badge badge-secondary">{#droits_utilisateurs#}</span>{/if}
										{if in_array("projects_manage_all", $userTmp.tabDroits) || in_array("projects_manage_own", $userTmp.tabDroits)}<span class="badge badge-secondary">{#droits_projets#}</span>{/if}
										{if in_array("projectgroups_manage_all", $userTmp.tabDroits)}<span class="badge badge-secondary">{#droits_groupesProjets#}</span>{/if}
										{if in_array("planning_modify_all", $userTmp.tabDroits) || in_array("planning_modify_own_project", $userTmp.tabDroits) || in_array("planning_modify_own_task", $userTmp.tabDroits)}<span class="badge badge-secondary">{#droits_modifPlanning#}</span>{/if}
										{if in_array("lieux_all", $userTmp.tabDroits)}<span class="badge badge-secondary">{#droits_lieux#}</span>{/if}
										{if in_array("ressources_all", $userTmp.tabDroits)}<span class="badge badge-secondary">{#droits_ressources#}</span>{/if}
										{if in_array("parameters_all", $userTmp.tabDroits)}<span class="badge badge-info">{#droits_parametres#}</span>{/if}
									{/if}
								</td>
								<td class="wrap d-none d-lg-table-cell text-center">
									{assign var=valTmp value=$userTmp.visible_planning}
									{$smarty.config.$valTmp}
									&nbsp;
								</td>
								<td class="wrap d-none d-lg-table-cell text-center">
									{assign var=cooltip value=$smarty.config.user_liste_NBPeriodes|cat:" : "|cat:$userTmp.totalPeriodes|cat:"<br>"|cat:$smarty.config.user_date_dernier_login|cat:" : "}
									{assign var=dateLogin value=$userTmp.date_dernier_login|sqldatetime2userdatetime}
									{assign var=cooltip value=$cooltip|cat:$dateLogin}
									{assign var=cooltip value=$cooltip|cat:"<br>"|cat:$smarty.config.periode_date_creation|cat:" : "}
									{assign var=dateCreation value=$userTmp.date_creation|sqldatetime2userdatetime}
									{assign var=cooltip value=$cooltip|cat:$dateCreation}
									{assign var=cooltip value=$cooltip|cat:"<br>"|cat:$smarty.config.periode_modifier|cat:" : "}
									{assign var=dateModif value=$userTmp.date_modif|sqldatetime2userdatetime}
									{assign var=cooltip value=$cooltip|cat:$dateModif}
									
									<span class="tooltipster" title="{$cooltip}"><i class="fa fa-info-circle fa-lg fa-fw cursor-help " aria-hidden="true"></i></span>
								</td>
							</tr>
						{/foreach}
						{if $nbPages > 1}
							<tr>
								<td colspan="7" class="text-right" style="white-space:normal">
									{if $currentPage > 1}<a href="{$BASE}/user_list.php?page={$currentPage-1}">&lt;&lt; {#action_precedent#}</a>&nbsp;&nbsp;{/if}
									{section name=pagination loop=$nbPages}
										{if $smarty.section.pagination.iteration == $currentPage}<b>{else}<a href="{$BASE}/user_list.php?page={$smarty.section.pagination.iteration}">{/if}
										{$smarty.section.pagination.iteration}
										{if $smarty.section.pagination.iteration == $currentPage}</b>{else}</a>{/if}&nbsp;
									{/section}
									{if $currentPage < $nbPages}<a href="{$BASE}/user_list.php?page={$currentPage+1}">{#action_suivant#} &gt;&gt;</a>{/if}
								</td>
							</tr>
						{/if}
					</table>
				</div>
			</div>
		</div>
		<br>

	{else}
	
		<div class="row">
			<div class="col-md-12">
				<div class="soplanning-box mt-2">
					{#info_noRecord#}
				</div>
			</div>
		</div>

	{/if}
</div>

{include file="www_footer.tpl"}