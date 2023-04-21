{* Smarty *}
{#usersBulkRights_help#}
<br><br>

<form class="form-horizontal" method="post" action="" onsubmit="return false;" name="usersBulkRightsForm" autocomplete="off">
<div class="container-fluid">
		<div class="row">
			<label class="col-form-label col-md-3"><b>{#usersBulkRights_users#}</b> :</label>
			<div>
				<select name="bulk_user_id" multiple="multiple" id="bulk_user_id" class="d-none multiselect">
					{if $listeUsers|@count eq 0}
						<option>&nbsp;{#formFiltreUserAucunProjet#}</option>
					{else}
						<optgroup id="g0" value="1" label="{#cocheUserSansGroupe#}">
						{assign var=groupeTemp value=""}
						{foreach from=$listeUsers item=userCourant name=loopUsers}
							{if $userCourant.user_groupe_id neq $groupeTemp}
								</optgroup><optgroup id="g{$userCourant.user_groupe_id}" value="1" label="{$userCourant.groupe_nom}">
							{/if}
						<option value="{$userCourant.user_id}">{$userCourant.nom|xss_protect} ({$userCourant.user_id|xss_protect})</option> 								
						{assign var=groupeTemp value=$userCourant.user_groupe_id}
						{/foreach}
					{/if}
					</optgroup>
				</select>
			</div>

	</div>
	<hr width="90%">
	<b>{#usersBulkRights_rights#}</b> :
	<br>
	<div class="form-group col-md-12">
			<div class="row">
				<label class="col-form-label col-md-3">{#droits_utilisateurs#} :</label>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="users_manage" id="droit1" value="" checked="checked">
					<label class="form-check-label" for="droit1">{#droits_aucundroitUser#}</label>
				</div>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="users_manage" id="users_manage_all" value="users_manage_all">
					<label class="form-check-label" for="users_manage_all">{#droits_gererTousUsers#}</label>
				</div>
			</div>
			<div class="row">
				<label class="col-form-label col-md-3">{#droits_projets#} :</label>
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="projects_manage" id="droit2" value=""checked="checked">
					<label class="form-check-label" for="droit2">{#droits_aucunDroitProjets#}</label>
				</div>	
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="projects_manage" id="projects_manage_all" value="projects_manage_all">
					<label class="form-check-label" for="projects_manage_all">{#droits_gererTousProjets#}</label>
				</div>						
				<div class="form-check form-check-inline">
					<input class="form-check-input" type="radio" name="projects_manage" id="projects_manage_own" value="projects_manage_own">
					<label class="form-check-label" for="projects_manage_own">{#droits_uniquementProjProprio#}</label>
				</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_groupesProjets#} :</label>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="projectgroups_manage" id="droit3" value="" checked="checked">
							<label class="form-check-label" for="droit3">{#droits_groupesProjetsAucun#}</label>
						</div>	
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="projectgroups_manage" id="projectgroups_manage_all" value="projectgroups_manage_all">
							<label class="form-check-label" for="projectgroups_manage_all">{#droits_gererTousGroupes#}</label>
						</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_modifPlanning#} :</label>
					<div class="col-form-label">					
					<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="droit3" value="tasks_readonly" checked="checked">
							<label class="form-check-label" for="tasks_readonly">{#droits_planningLectureSeule#}</label>
						</div>						
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="tasks_modify_all" value="tasks_modify_all">
							<label class="form-check-label" for="tasks_modify_all">{#droits_planningTousProjets#}</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="tasks_modify_team" value="tasks_modify_team">
							<label class="form-check-label" for="tasks_modify_team">{#droits_planningEquipe#}</label>
						</div><br />
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="tasks_modify_own_project" value="tasks_modify_own_project">
							<label class="form-check-label" for="tasks_modify_own_project">{#droits_planningProjetsProprio#}</label>
						</div>						
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_modif" id="tasks_modify_own_task" value="tasks_modify_own_task">
							<label class="form-check-label" for="tasks_modify_own_task">{#droits_planningTachesAssignees#}</label>
						</div>
					</div>	
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_projets_visibles#} :</label>
					<div class="col-form-label">
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view" id="tasks_view_all_projects" value="tasks_view_all_projects" checked="checked">
							<label class="form-check-label" for="tasks_view_all_projects">{#droits_vueTousProjets#}</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view" id="tasks_view_team_projects" value="tasks_view_team_projects">
							<label class="form-check-label" for="tasks_view_team_projects">{#droits_vueProjetsEquipe#}</label>
						</div><br />
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view" id="tasks_view_own_projects" value="tasks_view_own_projects">
							<label class="form-check-label" for="tasks_view_own_projects">{#droits_vueProjetsAssignes#}</label>
						</div>						
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view" id="tasks_view_only_own" value="tasks_view_only_own">
							<label class="form-check-label" for="tasks_view_only_own">{#droits_tasks_view_only_own#}</label>
						</div>
					</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_users_visibles#} :</label>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view_users" id="tasks_view_all_users" value="tasks_view_all_users" onChange="{literal}if(this.checked){document.getElementById('divSpecificUsers').style.display='none';}{/literal}" checked="checked">
							<label class="form-check-label" for="tasks_view_all_users">{#droits_tasks_view_all_users#}</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="planning_view_users" id="tasks_view_specific_users" value="tasks_view_specific_users" onChange="{literal}if(this.checked){document.getElementById('divSpecificUsers').style.display='inline-block';}{/literal}">
							<label class="form-check-label" for="tasks_view_specific_users">{#droits_tasks_view_specific_users#}</label>
						</div>
						<div id="divSpecificUsers" style="display:none" class="col-form-label">
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
									<option value="{$userCourant.user_id}">{$userCourant.nom|xss_protect} ({$userCourant.user_id|xss_protect})</option>		
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
						<input class="form-check-input" type="radio" name="lieux" id="droit6" value="" checked="checked">
						<label class="form-check-label" for="droit6">{#droits_aucunLieux#}</label>
					</div>	
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="lieux" id="lieux_all" value="lieux_all">
						<label class="form-check-label" for="lieux_all">{#droits_lieuxAcces#}</label>
					</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_ressources#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="ressources" id="droit7" value=""  checked="checked">
						<label class="form-check-label" for="droit7">{#droits_aucunRessources#}</label>
					</div>	
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="ressources" id="ressources_all" value="ressources_all">
						<label class="form-check-label" for="ressources_all">{#droits_ressourcesAcces#}</label>
					</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_audit#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="audit" id="audit_none" value=""checked="checked">
						<label class="form-check-label" for="audit_none">{#droits_aucunAudit#}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="audit" id="audit_restore_own" value="audit_restore_own">
						<label class="form-check-label" for="audit_restore_own">{#droits_auditRestoreProprietaire#}</label>
					</div>					
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="audit" id="audit_restore" value="audit_restore_own">
						<label class="form-check-label" for="audit_restore">{#droits_auditRestore#}</label>
					</div>
			</div>					
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_parametres#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="parameters" id="droit5" value="">
						<label class="form-check-label" for="droit5">{#droits_aucunParametres#}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="parameters" id="parameters_modify" value="parameters_all">
						<label class="form-check-label" for="parameters_modify">{#droits_parametresAcces#}</label>
					</div>
			</div>
			<div class="row">
					<label class="col-form-label col-md-3">{#droits_stats#} :</label>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="stats_users" id="stats_users" value="stats_users">
						<label class="form-check-label" for="stats_users">{#droits_stats_users#}</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="stats_projects" id="stats_projects" value="stats_projects">
						<label class="form-check-label" for="stats_projects">{#droits_stats_projects#}</label>
					</div>
			</div>
	</div>
	<div class="form-group col-md-12 text-center">

				<input type="button" class="btn btn-primary" value="{#submit#}" onClick="if(confirm('{#confirm#|xss_protect}'))bulk_users_ids=getSelectValue('bulk_user_id'); specific_users_ids=getSelectValue('specific_user_id'); xajax_usersBulkRightsSubmit(bulk_users_ids, new Array(getRadioValue('users_manage'), getRadioValue('projects_manage'), getRadioValue('projectgroups_manage'), getRadioValue('planning_modif'), getRadioValue('planning_view'), getRadioValue('planning_view_users'), getRadioValue('lieux'), getRadioValue('ressources'), getRadioValue('parameters'), ($('#stats_users').is(':checked') ? $('#stats_users').val() : ''), ($('#stats_projects').is(':checked') ? $('#stats_projects').val() : '')), specific_users_ids);" />
	</div>
{literal}
<script>
	$("#bulk_user_id").multiselect({
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
	$("#bulk_user_id").show();
	$("#search-user").css("overflow", "visible");
	$("#search-user").css("width", "380");
</script>
{/literal}