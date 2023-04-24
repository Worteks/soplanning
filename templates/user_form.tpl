{* Smarty *}

<form class="form-horizontal" method="post" action="" onsubmit="return false;" name="formUser" autocomplete="off">
{* pour tester si compte déjà existant ou pas *}
<input type="hidden" id="user_id_origine" value="{$user_form.user_id}">
<div class="container-fluid">
	<div class="form-group row col-md-12">
			<label class="col-form-label col-md-2">{#user_identifiant#} :</label>
			<div class="col-md-4">
				<input class="form-control" id="user_id" type="text" value="{$user_form.user_id|xss_protect}" maxlength="20" />
			</div>
			<label class="col-form-label col-md-2">{#user_groupe#} :</label>
			<div class="col-md-4">
				<select id="user_groupe_id" class="form-control{if $smarty.session.isMobileOrTablet==0} select2{/if}">
					<option value="">- - - - - - - - - - -</option>
					{foreach from=$groupes item=groupe}
						<option value="{$groupe.user_groupe_id}" {if $user_form.user_groupe_id eq $groupe.user_groupe_id}selected="selected"{/if}>{$groupe.nom|xss_protect}</option>
					{/foreach}
				</select>
			</div>
	</div>
	<div class="form-group row col-md-12">
				<label class="col-form-label col-md-2">{#user_nom#} :</label>
				<div class="col-md-4">
					<input id="nom" class="form-control" type="text" value="{$user_form.nom|xss_protect}" maxlength="100" />
				</div>
				<label class="col-form-label col-md-2">{#user_email#} :</label>
				<div class="col-md-4">
					<input id="email_user" class="form-control" type="text" value="{$user_form.email|xss_protect}" maxlength="255" />
				</div>
	</div>
	<div class="form-group row col-md-12">
				<label class="col-form-label col-md-2">{#user_login#} :</label>
				<div class="col-md-4">
					<input id="tmp_lo" class="form-control" type="text" value="{$user_form.login|xss_protect}" maxlength="30" autocomplete="new-password" />
				</div>
				<label class="col-form-label col-md-2">{#user_password#} :</label>
				<div class="col-md-4">
					<input id="tmp_pa" class="form-control" type="password" value="" maxlength="50" autocomplete="new-password" />
				</div>
	</div>
	<div class="form-group row col-md-12">
	 <ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active" data-toggle="tab" href="#droits">{#user_droits_court#}</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#perso">{#user_perso_notif#}</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" data-toggle="tab" href="#infos">{#user_infos_perso#}</a>
		</li>
	</ul> 

	</div>
	<div class="form-group">
	<div class="form-group">
	<div class="tab-content">	
		<div class="tab-pane container active" id="droits">
			{if in_array("users_manage_all", $user.tabDroits)}
				<div class="row">
					<label class="col-form-label col-md-3">{#droits_utilisateurs#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="users_manage" id="droit1" value="" {if !in_array("users_manage_all", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="droit1">{#droits_aucundroitUser#}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="users_manage" id="users_manage_team" value="users_manage_team" {if in_array("users_manage_team", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="users_manage_team">{#droits_gererUsersTeam#}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="users_manage" id="users_manage_all" value="users_manage_all" {if in_array("users_manage_all", $user_form.tabDroits)}checked="checked"{/if} {if in_array("users_manage_team", $user.tabDroits)}disabled{/if}>
						<label class="form-check-label" for="users_manage_all">{#droits_gererTousUsers#}</label>
					</div>
				</div>
			{/if}
			<div class="row">
				<label class="col-form-label col-md-3">{#droits_projets#} :</label>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="projects_manage" id="droit2" value="" {if !in_array("projects_manage_all", $user_form.tabDroits) && !in_array("projects_manage_own", $user_form.tabDroits)}checked="checked"{/if}>
					<label class="form-check-label" for="droit2">{#droits_aucunDroitProjets#}</label>
				</div>	
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="projects_manage" id="projects_manage_all" value="projects_manage_all" {if in_array("projects_manage_all", $user_form.tabDroits)}checked="checked"{/if}>
					<label class="form-check-label" for="projects_manage_all">{#droits_gererTousProjets#}</label>
				</div>						
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="projects_manage" id="projects_manage_own" value="projects_manage_own" {if in_array("projects_manage_own", $user_form.tabDroits)}checked="checked"{/if}>
					<label class="form-check-label" for="projects_manage_own">{#droits_uniquementProjProprio#}</label>
				</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_groupesProjets#} :</label>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="projectgroups_manage" id="droit3" value="" {if !in_array("projectgroups_manage_all", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="droit3">{#droits_groupesProjetsAucun#}</label>
						</div>	
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="projectgroups_manage" id="projectgroups_manage_all" value="projectgroups_manage_all" {if in_array("projectgroups_manage_all", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="projectgroups_manage_all">{#droits_gererTousGroupes#}</label>
						</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_modifPlanning#} :</label>
					<div class="col-form-label">					
					<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="tasks_readonly" value="tasks_readonly" {if in_array("tasks_readonly", $user_form.tabDroits) || (!in_array("tasks_modify_all", $user_form.tabDroits) && !in_array("tasks_modify_own_project", $user_form.tabDroits) && !in_array("tasks_modify_own_task", $user_form.tabDroits))}checked="checked"{/if}>
							<label class="form-check-label" for="tasks_readonly">{#droits_planningLectureSeule#}</label>
						</div>	
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="tasks_modify_all" value="tasks_modify_all" {if in_array("tasks_modify_all", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="tasks_modify_all">{#droits_planningTousProjets#}</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="tasks_modify_team" value="tasks_modify_team" {if in_array("tasks_modify_team", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="tasks_modify_team">{#droits_planningEquipe#}</label>
						</div><br />				
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="tasks_modify_own_project" value="tasks_modify_own_project" {if in_array("tasks_modify_own_project", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="tasks_modify_own_project">{#droits_planningProjetsProprio#}</label>
						</div>						
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="tasks_modify_own_task" value="tasks_modify_own_task" {if in_array("tasks_modify_own_task", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="tasks_modify_own_task">{#droits_planningTachesAssignees#}</label>
						</div>
					</div>	
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_projets_visibles#} :</label>
					<div class="col-form-label">
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view" id="tasks_view_all_projects" value="tasks_view_all_projects" {if in_array("tasks_view_all_projects", $user_form.tabDroits) || !in_array("tasks_view_own_projects", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="tasks_view_all_projects">{#droits_vueTousProjets#}</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view" id="tasks_view_team_projects" value="tasks_view_team_projects" {if in_array("tasks_view_team_projects", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="tasks_view_team_projects">{#droits_vueProjetsEquipe#}</label>
						</div><br />
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view" id="tasks_view_own_projects" value="tasks_view_own_projects" {if in_array("tasks_view_own_projects", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="tasks_view_own_projects">{#droits_vueProjetsAssignes#}</label>
						</div>						
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view" id="tasks_view_only_own" value="tasks_view_only_own" {if in_array("tasks_view_only_own", $user_form.tabDroits)}checked="checked"{/if}>
							<label class="form-check-label" for="tasks_view_only_own">{#droits_tasks_view_only_own#}</label>
						</div>
					</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_users_visibles#} :</label>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view_users" id="tasks_view_all_users" value="tasks_view_all_users" {if in_array("tasks_view_all_users", $user_form.tabDroits) || !in_array("tasks_view_all_users", $user_form.tabDroits)}checked="checked"{/if} onChange="{literal}if(this.checked){document.getElementById('divSpecificUsers').style.display='none';}{/literal}">
							<label class="form-check-label" for="tasks_view_all_users">{#droits_tasks_view_all_users#}</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view_users" id="droits_tasks_view_team_users" value="droits_tasks_view_team_users" {if in_array("droits_tasks_view_team_users", $user_form.tabDroits)}checked="checked"{/if} onChange="{literal}if(this.checked){document.getElementById('divSpecificUsers').style.display='none';}{/literal}">
							<label class="form-check-label" for="droits_tasks_view_team_users">{#droits_tasks_view_team_users#}</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view_users" id="tasks_view_specific_users" value="tasks_view_specific_users" {if in_array("tasks_view_specific_users", $user_form.tabDroits)}checked="checked"{/if} onChange="{literal}if(this.checked){document.getElementById('divSpecificUsers').style.display='inline-block';}{/literal}">
							<label class="form-check-label" for="tasks_view_specific_users">{#droits_tasks_view_specific_users#}</label>
						</div>
						<div id="divSpecificUsers" style="display:{if in_array("tasks_view_specific_users", $user_form.tabDroits)}inline-block{else}none{/if}" class="col-form-label">
							<select name="specific_user_id" multiple="multiple" id="specific_user_id" class="d-none multiselect">
								{if $listeUsers|@count eq 0}
									<option>&nbsp;{#formFiltreUserAucunProjet#}</option>
								{else}
									<optgroup id="g0" value="1" label="{#cocheUserSansGroupe#}">
									{assign var=groupeTemp value=""}
									{foreach from=$listeUsers item=userCourant name=loopUsers}
										{if $userCourant.user_groupe_id neq $groupeTemp}
											</optgroup><optgroup id="g{$userCourant.user_groupe_id}" value="1" label="{$userCourant.groupe_nom}">
										{/if}
									<option value="{$userCourant.user_id}" {if in_array($userCourant.user_id, $listUsersRights)}selected="selected"{/if}>{$userCourant.nom|xss_protect} ({$userCourant.user_id|xss_protect})</option> 								
									{assign var=groupeTemp value=$userCourant.user_groupe_id}
									{/foreach}
								{/if}
								</optgroup>
							</select>
							{literal}
							<script>
								$("#specific_user_id").multiselect({
									selectAll:false,
									validateCloseOnly:true,
									noUpdatePlaceholderText:true,
									nameSuffix: 'user',
									desactivateUrl: 'process/planning.php?desactiverFiltreUser=1',
									placeholder: '{/literal}{#formChoixUser#}{literal}',
									texts: {
									   selectAll    : '{/literal}{#formFiltreUserCocherTous#}{literal}',
									   unselectAll    : '{/literal}{#formFiltreUserDecocherTous#}{literal}',
									   disableFilter : '{/literal}{#formFiltreUserDesactiver#}{literal}',
									   validateFilter : '{/literal}{#submit#}{literal}',
									   search : '{/literal}{#search#}{literal}'
									},
								});
								$("#specific_user_id").show();
								$("#search-user").css("overflow", "visible");
								$("#search-user").css("width", "380");
							</script>
							{/literal}
						</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_lieux#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="lieux" id="droit6" value="" {if !in_array("lieux_all", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="droit6">{#droits_aucunLieux#}</label>
					</div>	
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="lieux" id="lieux_all" value="lieux_all" {if in_array("lieux_all", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="lieux_all">{#droits_lieuxAcces#}</label>
					</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_ressources#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="ressources" id="droit7" value="" {if !in_array("ressources_all", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="droit7">{#droits_aucunRessources#}</label>
					</div>	
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="ressources" id="ressources_all" value="ressources_all" {if in_array("ressources_all", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="ressources_all">{#droits_ressourcesAcces#}</label>
					</div>
			</div>
			{if !(in_array("users_manage_team", $user_form.tabDroits))}
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_audit#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="audit" id="audit_none" value="" {if (!in_array("audit_visualisation", $user_form.tabDroits)) and (!in_array("audit_restore", $user_form.tabDroits))}checked="checked"{/if}>
						<label class="form-check-label" for="audit_none">{#droits_aucunAudit#}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="audit" id="audit_restore_own" value="audit_restore_own" {if in_array("audit_restore_own", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="audit_restore_own">{#droits_auditRestoreProprietaire#}</label>
					</div>					
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="audit" id="audit_restore" value="audit_restore" {if in_array("audit_restore", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="audit_restore">{#droits_auditRestore#}</label>
					</div>
			</div>					
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_parametres#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="parameters" id="droit5" value="" {if !in_array("parameters_modify", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="droit5">{#droits_aucunParametres#}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="parameters" id="parameters_modify" value="parameters_all" {if in_array("parameters_all", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="parameters_modify">{#droits_parametresAcces#}</label>
					</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_stats#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="stats_users" id="stats_users" value="stats_users" {if in_array("stats_users", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="stats_users">{#droits_stats_users#}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="stats_projects" id="stats_projects" value="stats_projects" {if in_array("stats_projects", $user_form.tabDroits)}checked="checked"{/if}>
						<label class="form-check-label" for="stats_projects">{#droits_stats_projects#}</label>
					</div>
			</div>
			{/if}
		</div>
		<div class="tab-pane container fade" id="perso">
			<div class="form-group row">
			<label class="col-form-label col-md-3">{#user_login_actif#} :</label>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="login_actif" id="login_actifOui" value="oui" {if $user_form.login_actif eq "oui"}checked="checked"{/if}>
					<label class="form-check-label" for="login_actifOui">{#oui#}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="login_actif" id="login_actifNon" value="non" {if $user_form.login_actif eq "non"}checked="checked"{/if}>
					<label class="form-check-label" for="login_actifNon">{#non#}</label>
				</div>
			
				<label class="col-form-label offset-md-2 col-md-3">{#user_visiblePlanning#} :</label>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="visible_planning" id="visible_planningOui" value="oui" {if ($user_form.saved eq 0) || ($user_form.visible_planning eq "oui")}checked="checked"{/if}>
					<label class="form-check-label" for="visible_planningOui">{#oui#}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="visible_planning" id="visible_planningNon" value="non" {if $user_form.visible_planning eq "non"}checked="checked"{/if}>
					<label class="form-check-label" for="visible_planningNon">{#non#}</label>
				</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-md-3">{#user_notifications#} :</label>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="notifications" id="notificationsOui" value="oui" {if $user_form.notifications eq "oui"}checked="checked"{/if}>
					<label class="form-check-label" for="notificationsOui">{#oui#}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="notifications" id="notificationsNon" value="non" {if $user_form.notifications eq "non"}checked="checked"{/if}>
					<label class="form-check-label" for="notificationsNon">{#non#}</label>
				</div>
				<label class="col-form-label offset-md-2 col-md-3">{#user_couleur#} :</label>
				<div>
					{if $smarty.session.couleurExUser neq ""}
						{assign var=couleurExUser value=$smarty.session.couleurExUser}
					{else}
						{assign var=couleurExUser value="ffffff"}
					{/if}
					{if $smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE neq ""}
						{if $smarty.session.isMobileOrTablet==1}
							<input name="couleur_user" id="couleur_user" maxlength="6" type="color" list="colors" value="#{if $projet.couleur eq ''}{$couleurExProjet}{else}{$projet.couleur}{/if}" />
							<datalist id="colors">
								{foreach from=","|explode:$smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE item=couleurTmp}
								<option>{$couleurTmp}</option>
								{/foreach}
							</datalist>
						{else}
							<select name="couleur2" id="couleur2" style="background-color:#{$user_form.couleur};color:{'#'|cat:$user_form.couleur|buttonFontColor}" class="form-control" >
							{if $user_form.couleur neq ""}<option value="#{$user_form.couleur}" style="background-color:#{$user_form.couleur};color:{'#'|cat:$user_form.couleur|buttonFontColor}" selected="selected">{$user_form.couleur}</option>{else}<option value="">{#winProjet_couleurchoix#}</option>{/if}
							{foreach from=","|explode:$smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE item=couleurTmp}
								<option value="{$couleurTmp}" style="background-color:{$couleurTmp};color:{$couleurTmp|buttonFontColor}" {if $couleurTmp eq "#"|cat:$user_form.couleur}selected="selected"{/if}>{$couleurTmp|xss_protect|replace:'#':''}</option>
							{/foreach}
						</select>
						{/if}
					{else}
						<input id="couleur_user" name="couleur_user" maxlength="6" {if $smarty.session.isMobileOrTablet==1}type="color"{else}type="text"{/if} value="#{$user_form.couleur|xss_protect}"/>
					{/if}
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-12">
					<label class="checkbox-inline">
						<input type="checkbox" id="envoiMailPwd" name="envoiMailPwd" value="true" />{#user_mailPwd#}
					</label>
				</div>
			</div>
			{if $smarty.const.CONFIG_GOOGLE_2FA_ACTIVE eq "1" && $user_form.google_2fa eq "ok"}
				<div class="form-group">
					<div class="col-md-12">
						<label class="checkbox-inline">
							<input type="checkbox" id="google_2fa_reset" name="google_2fa_reset" value="true" />{#google_2fa_reset#}
						</label>
					</div>
				</div>
			{else}
				<input type="hidden" id="google_2fa_reset" value="0" />
			{/if}
		</div>	
        <div class="tab-pane container fade" id="infos">
			<div class="form-group row">
				<label class="col-form-label col-md-3">{#user_adress#} :</label>
				<div class="col-md-6">
					<input id="user_adress" class="form-control" type="text" value="{$user_form.adresse|xss_protect}" maxlength="100" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-md-3">{#user_phone#} :</label>
				<div class="col-md-2">
					<input id="user_phone" class="form-control" type="text" value="{$user_form.telephone|xss_protect}" maxlength="20" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-md-3">{#user_mobile#} :</label>
				<div class="col-md-2">
					<input id="user_mobile" class="form-control" type="text" value="{$user_form.mobile|xss_protect}" maxlength="20" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-md-3">{#user_metier#} :</label>
				<div class="col-md-6">
					<input id="user_metier" class="form-control" type="text" value="{$user_form.metier|xss_protect}" maxlength="100" />
				</div>
			</div>
			<div class="form-group row">
				<label class="col-form-label col-md-3">{#user_comment#} :</label>
				<div class="col-md-6">
					<textarea id="user_comment" class="form-control">{$user_form.commentaire|xss_protect}</textarea>
				</div>
			</div>
		</div>
	</div>
	</div>
	<div class="form-group col-md-12 text-center">
				<input type="button" class="btn btn-primary" value="{#enregistrer#}" onClick="specific_users_ids=getSelectValue('specific_user_id');xajax_submitFormUser($('#user_id').val(), $('#user_id_origine').val(), $('#user_groupe_id').val(), $('#nom').val(), $('#email_user').val(), $('#tmp_lo').val(), $('#tmp_pa').val(), $('#visible_planningOui').is(':checked'), {if $smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE neq ""}$('#couleur2 option:selected').val(){else}$('#couleur_user').val(){/if}, $('#notificationsOui').is(':checked'), $('#envoiMailPwd').is(':checked'), new Array(getRadioValue('users_manage'), getRadioValue('projects_manage'), getRadioValue('projectgroups_manage'), getRadioValue('planning_modif'), getRadioValue('planning_view'), getRadioValue('planning_view_users'), getRadioValue('lieux'), getRadioValue('ressources'), getRadioValue('audit'), getRadioValue('parameters'), ($('#stats_users').is(':checked') ? $('#stats_users').val() : ''), ($('#stats_projects').is(':checked') ? $('#stats_projects').val() : '')), $('#user_adress').val(), $('#user_phone').val(),$('#user_mobile').val(), $('#user_metier').val(), $('#user_comment').val(), $('#login_actifOui').is(':checked'), specific_users_ids, $('#google_2fa_reset').is(':checked'));" />
		</div>
	</div>
</div>