<!DOCTYPE html>
<html lang="fr">
<head>
	{if !isset($infoVersion)}
		{assign var=infoVersion value="temp"}
	{/if}
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no" />
	<meta name="reply-to" content="support@soplanning.org" />
	<meta name="email" content="support@soplanning.org" />
	<meta name="Identifier-URL" content="http://www.soplanning.org" />
	<meta name="robots" content="noindex,follow" />
	<title>
		{if isset($smarty.const.CONFIG_SOPLANNING_TITLE) && $smarty.const.CONFIG_SOPLANNING_TITLE neq "SOPlanning"}
			{$smarty.const.CONFIG_SOPLANNING_TITLE|xss_protect}
		{else}
			SOPlanning - Simple Online Planning
		{/if}
	</title>
	<link rel="apple-touch-icon" sizes="180x180" href="{$BASE}/apple-touch-icon.png" />
	<link rel="icon" type="image/png" sizes="32x32" href="{$BASE}/favicon-32x32.png" />
	<link rel="icon" type="image/png" sizes="16x16" href="{$BASE}/favicon-16x16.png" />
	<link rel="manifest" href="{$BASE}/manifest.php" />
	<link rel="mask-icon" href="{$BASE}/safari-pinned-tab.svg" color="#5bbad5" />
	<meta name="msapplication-TileColor" content="#da532c" />
	<meta name="theme-color" content="#ffffff" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/bootstrap-4.6.2/css/bootstrap.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/jquery-ui-1.13.2.custom/jquery-ui.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/themes/{$smarty.const.CONFIG_SOPLANNING_THEME}?{$infoVersion}" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/jquery-multiselect-2.4.1/jquery.multiselect.css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/styles.css?{$infoVersion}" type="text/css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/mobile.css?{$infoVersion}" media="screen and (max-width: 1165px)" type="text/css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/print.css?{$infoVersion}" media="print">
	<link rel="stylesheet" href="{$BASE}/assets/plugins/select2-4.0.13/dist/css/select2.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/select2-bootstrap.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/spectrum-1.8.1/spectrum.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/timepicker/jquery.ui.timepicker.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/bootstrap-datepicker-1.10.0/css/bootstrap-datepicker3.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/jquery-timepicker-1.14.0/jquery.timepicker.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/fontawesome-6.4.0/css/all.min.css" />
	<script src="{$BASE}/assets/js/fonctions.js?{$infoVersion}"></script>
	<script src="{$BASE}/assets/js/jquery-3.7.0.min.js"></script>
	<script src="{$BASE}/assets/plugins/jquery-ui-1.13.2.custom/jquery-ui.js"></script>
	<script src="{$BASE}/assets/plugins/jquery-multiselect-2.4.1/jquery.multiselect.js"></script>
	<script src="{$BASE}/assets/plugins/select2-4.0.13/dist/js/select2.min.js"></script>
	<script src="{$BASE}/assets/plugins/select2-4.0.13/dist/js/i18n/fr.js" charset="UTF-8"></script>
	<script src="{$BASE}/assets/plugins/spectrum-1.8.1/spectrum.js"></script>
	<script src="{$BASE}/assets/plugins/jquery-timepicker-1.14.0/jquery.timepicker.min.js"></script>
	<script src="{$BASE}/assets/plugins/textarea-autosize/autosize.js"></script>
	<script src="{$BASE}/assets/plugins/timepicker/jquery.ui.timepicker.js"></script>	
	<script defer src="{$BASE}/assets/plugins/fontawesome-6.4.0/js/all.min.js"></script>
	<script defer src="{$BASE}/assets/plugins/fontawesome-6.4.0/js/v4-shims.min.js"></script>
	<style>
	{if $smarty.const.CONFIG_SOPLANNING_LOGO != ''}
		{literal}
		.week td {min-width:30px;}
		{/literal}
	{/if}
	{if $smarty.const.CONFIG_PLANNING_LINE_HEIGHT > 0 || $smarty.const.CONFIG_PLANNING_COL_WIDTH > 0 || $smarty.const.CONFIG_PLANNING_COL_WIDTH_LARGE > 0}
		{literal}td.week, td.weekend, td.sumcell, #tdtotal, #total2 {{/literal}
		{if $smarty.const.CONFIG_PLANNING_LINE_HEIGHT > 0}
			height:{$smarty.const.CONFIG_PLANNING_LINE_HEIGHT}px;
		{/if}
		{if isset($smarty.session.dimensionCase) && $smarty.session.dimensionCase == "reduit"}
			{if $smarty.const.CONFIG_PLANNING_COL_WIDTH > 0}
				min-width:{$smarty.const.CONFIG_PLANNING_COL_WIDTH}px;
			{/if}
		{else}
			{if $smarty.const.CONFIG_PLANNING_COL_WIDTH_LARGE > 0}
				min-width:{$smarty.const.CONFIG_PLANNING_COL_WIDTH_LARGE}px;
			{/if}
		{/if}
		{literal}}{/literal}
	{/if}
	{if $smarty.const.CONFIG_PLANNING_CELL_FONTSIZE > 0}{literal}.cellHolidays,.cellProjectBiseau1,.cellProjectBiseau2,.cellProject{font-size:{/literal}{$smarty.const.CONFIG_PLANNING_CELL_FONTSIZE}{literal}px;}{/literal}
	{/if}
	{literal}

	{/literal}
	</style>
</head>
<body>
{if isset($user)}
	<nav class="navbar navbar-expand-lg navbar-dark fixed-top flex-lg-nowrap bg-dark">
		{if $smarty.const.CONFIG_SOPLANNING_LOGO != ''}
			<a class="navbar-brand navbar-brand-logo mr-auto d-inline-block align-items-center" href="{$BASE}/"><img src="{$BASE}/upload/logo/{$smarty.const.CONFIG_SOPLANNING_LOGO}" alt='logo' class="mr-3 logo" style="max-height:23px" />
		{else}
			<a class="navbar-brand mr-auto" href="{$BASE}/">
		{/if}
		<span id="soplanning_title">{$smarty.const.CONFIG_SOPLANNING_TITLE|xss_protect}&nbsp;<span class="versionNumber">v{$infoVersion}</span></span>
		</a>

		<div id="divWarningSpace" style="width:15px">
			<div id="divWarningVersion" style="display:none">
				<a style="margin-left:5px" href="javascript:jQuery('#myModal .modal-header h5').html('{#version_version_dispo#}');jQuery('#myModal .modal-body').html(jQuery('#divContenuVersion').html());jQuery('#myModal').modal();undefined;"  title="{#warning_version#}" class="tooltipster">
					<i class="fa fa-warning fa-lg" aria-hidden="true" style="color:orange;font-size:1em"></i>
				</a>
			</div>
		</div>
		<div id="divContenuVersion" style="display:none;">
		</div>

		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav ml-3 mr-auto">
			<li class="nav-item dropdown">
				<a class="nav-link" href="{$BASE}/planning.php" id="menuPlanning" role="button" {if $smarty.session.isMobileOrTablet==1}data-toggle="dropdown"{/if} >
					<i class="fa fa-calendar fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;{#menuPlanning#}
				</a>
				<div class="dropdown-menu mt-0" aria-labelledby="menuPlanning">
				<a href="{$BASE}/planning.php" class="dropdown-item">
					<i class="fa fa-calendar fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuAfficherPlanning#}
				</a>
				<a href="{$BASE}/taches.php" class="dropdown-item">
					<i class="fa fa-list fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuAfficherTaches#}
				</a>
				<div class="dropdown-divider"></div>
				{if !in_array("tasks_readonly", $user.tabDroits)}
				<a href="javascript:Reloader.stopRefresh();xajax_ajoutPeriode();undefined;" class="dropdown-item">
					<i class="fa fa-calendar-plus-o fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuAjouterPeriode#}
				</a>
				{/if}
			</div>
			</li>	
			{if in_array("projects_manage_all", $user.tabDroits) || in_array("projects_manage_own", $user.tabDroits)}
				<li class="nav-item dropdown">
					<a class="nav-link" href="{$BASE}/projets.php" id="menuProjet" {if $smarty.session.isMobileOrTablet==1}data-toggle="dropdown"{/if} role="button">
						<i class="fa fa-book fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;{#menuProjets#}
					</a>
					<div class="dropdown-menu mt-0" aria-labelledby="menuProjet">
						<a href="{$BASE}/projets.php" class="dropdown-item">
							<i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuListeProjets#}
						</a>
						{if in_array("projectgroups_manage_all", $user.tabDroits)}
						<a href="{$BASE}/groupe_list.php" class="dropdown-item">
							<i class="fa fa-folder-o fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuListeGroupes#}
						</a>
						{/if}
						<div class="dropdown-divider"></div>
						<a href="javascript:Reloader.stopRefresh();xajax_ajoutProjet();undefined;" class="dropdown-item">
							<i class="fa fa-bookmark fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuAjouterProjet#}
						</a>
					</div>
				 </li>
			{/if}
			{if in_array("users_manage_all", $user.tabDroits)|| in_array("users_manage_team", $user.tabDroits)}
				<li class="divider-vertical"></li>
				<li class="nav-item dropdown">
					<a class="nav-link" href="{$BASE}/user_list.php" id="menuUser" {if $smarty.session.isMobileOrTablet==1}data-toggle="dropdown"{/if} role="button">
						<i class="fa fa-users fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;{#menuUsers#}
					</a>
					<div class="dropdown-menu mt-0" aria-labelledby="menuUser">
						<a href="{$BASE}/user_list.php" class="dropdown-item">
							<i class="fa fa-address-card-o fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuGestionUsers#}
						</a>
						<a href="{$BASE}/user_groupes.php" class="dropdown-item">
							<i class="fa fa-users fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuGroupesUsers#}
						</a>
						<div class="dropdown-divider"></div>
						<a href="javascript:xajax_modifUser();undefined;" class="dropdown-item">
							<i class="fa fa-user-plus fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuCreerUser#}
						</a>
					</div>
				</li>
			{/if}
			{if in_array("stats_users", $user.tabDroits) || in_array("stats_projects", $user.tabDroits) || in_array("audit_restore_own", $user.tabDroits) || in_array("stats_roi_projects", $user.tabDroits) || in_array("audit_restore", $user.tabDroits)}	
				<li class="divider-vertical"></li>
				<li class="nav-item dropdown">
					<a class="nav-link" href="{$BASE}/stats_users.php" id="menuStats" role="button" {if $smarty.session.isMobileOrTablet==1}data-toggle="dropdown"{/if} aria-haspopup="true" data-target="#menuStatsToggle" aria-expanded="true">
						<i class="fa fa-bar-chart fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;{#droits_stats#}
					</a>
					<div class="dropdown-menu mt-0" id="menuStatsToggle" aria-labelledby="menuStats">
						{if in_array("stats_users", $user.tabDroits)}
							<a href="{$BASE}/stats_users.php" class="dropdown-item">
								<i class="fa fa-bar-chart fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#droits_stats_users#}
							</a>
						{/if}
						{if in_array("stats_projects", $user.tabDroits)}
							<a href="{$BASE}/stats_projects.php" class="dropdown-item">
								<i class="fa fa-bar-chart fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#droits_stats_projects#}
							</a>
						{/if}
						{if in_array("stats_roi_projects", $user.tabDroits)}
							<a href="{$BASE}/stats_roi_projects.php" class="dropdown-item">
								<i class="fa fa-coins fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#droits_stats_roi_projects#}
							</a>
						{/if}
						{if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT == 1 && in_array("audit_restore", $user.tabDroits) }
							<div class="dropdown-divider"></div>
							<a href="{$BASE}/audit.php"  class="dropdown-item">
								<i class="fa fa-user-secret fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuAudit#}
							</a>
						{/if}
					</div>
				</li>	
			{/if}
			{if in_array("parameters_all", $user.tabDroits) || in_array("lieux_all", $user.tabDroits) || in_array("ressources_all", $user.tabDroits)}
				<li class="divider-vertical"></li>
				<li class="nav-item dropdown">
					<a class="nav-link" href="{if in_array("parameters_all", $user.tabDroits)}{$BASE}/options.php{else}#{/if}" data-target="#menuOptionsToggle" id="menuOptions" {if $smarty.session.isMobileOrTablet==1}data-toggle="dropdown"{/if} role="button">
						<i class="fa fa-cogs fa-lg" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;{#menuOptions#}
					</a>
					<div class="dropdown-menu mt-0" id="menuOptionsToggle" aria-labelledby="menuOptions">
						{if in_array("parameters_all", $user.tabDroits)}
							<a href="{$BASE}/options.php" class="dropdown-item">
								<i class="fa fa-cogs fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuOptions#}
							</a>
							<div class="dropdown-divider"></div>
							<a href="{$BASE}/feries.php" class="dropdown-item">
								<i class="fa fa-plane fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuFeries#}
							</a>
							<a href="{$BASE}/status.php" class="dropdown-item">
								<i class="fa fa-tags fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuStatus#}
							</a>
						{/if}
						{if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX == 1 && in_array("lieux_all", $user.tabDroits) }
							<a href="{$BASE}/lieux.php" class="dropdown-item">
								<i class="fa fa-map-marker fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuLieux#}
							</a>			
						{/if}
						{if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES == 1 && in_array("ressources_all", $user.tabDroits) }
							<a href="{$BASE}/ressources.php" class="dropdown-item">
								<i class="fa fa-plug fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuRessources#}
							</a>				
						{/if}
						{if in_array("parameters_all", $user.tabDroits)}
							<div class="dropdown-divider"></div>
							<a href="{$BASE}/backup.php" class="dropdown-item">
								<i class="fa fa-exchange fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuImportExport#}
							</a>
						{/if}
					</div>
				 </li>	
			{/if}
			<li class="nav-item">
				<a class="nav-link tooltipster" title="{#menu_aide#}" href="{$lienAide}" data-target="#"><i class="fa fa-question-circle fa-lg fa-fw" aria-hidden="true"></i></a>
			</li>
		</ul> 
		<ul class="navbar-nav ml-auto">
			{if isset($dateAbo) && $smarty.session.isMobileOrTablet==0}
				<li class="nav-item" style="margin-right:50px;">
					<a class="nav-link navbar-right tooltipster" target="_blank" href="{#texte_abo_lien#}" title="{"%1%"|str_replace:$dateAbo:$smarty.config.texte_abo2}">
						<i class="fa fa-warning fa-lg fa-fw" aria-hidden="true" style="color:orange"></i>&nbsp;{"%1%"|str_replace:$dateAbo:$smarty.config.texte_abo}
					</a>
				</li>
			{/if}
			{if $smarty.const.CONFIG_SOPLANNING_URL neq ""}
				<li class="nav-item" style="margin-right:10px;">
					<a class="nav-link navbar-right tooltipster" href="javascript:xajax_qrcode();undefined;" title="{#acces_mobile#}">
						<i class="fa fa-qrcode fa-lg fa-fw" aria-hidden="true"></i>
					</a>
				</li>
			{/if}
			{if $user.user_id == 'publicspl' }
				<li class="nav-item">
					<a class="nav-link" href="#" data-target="#" style="color:white">
						<i class="fa fa-user-o fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#accesPublicUsername#}
					</a>
				</li>
			{else}
				<li class="nav-item">
					<a class="nav-link navbar-right tooltipster" href="javascript:xajax_modifProfil();undefined;" title="{#menu_modifier_profil#}" data-target="#">
						<i class="fa fa-user fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{$user.nom} ({$user.user_id})
					</a>
				</li>
			{/if}
			<li class="nav-item">
				<a href="{$BASE}/process/login.php?action=logout&language={$lang}" class="nav-link tooltipster navbar-right" title="{#menu_deconnecter#}">
					<i class="fa fa-lg fa-sign-out" aria-hidden="true" style="color:red"></i>
				</a>
			</li>
		</ul>
		</div>
	</nav>
{/if}
{if isset($smarty.session.message)}
	{assign var=messageFinal value=$smarty.session.message|formatMessage}
	{"message"|delete_session_value}

	<div class="container-fluid" style="{if isset($htmlTableau)}{* displayed fixed if planning page otherwise not visible *}cursor:pointer;position:fixed;top:10px;z-index:1000{else}margin-bottom:60px;{/if}" onClick="this.style.display='none'">
		<div id="divMessage" class="alert alert-success">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<i class="fa fa-lg fa-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;{$messageFinal}
		</div>
	</div>
{/if}
{if isset($smarty.session.erreur)}
	{assign var=messageFinal value=$smarty.session.erreur|formatMessage}
	{"erreur"|delete_session_value}

	<div class="container-fluid" style="{if isset($htmlTableau)}{* displayed fixed if planning page otherwise not visible *}cursor:pointer;position:fixed;top:10px;z-index:1000{else}margin-bottom:60px;{/if}" onClick="this.style.display='none'">
		<div id="divMessage" class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		<i class="fa fa-lg fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;&nbsp;{$messageFinal}
		</div>
	</div>
{/if}

