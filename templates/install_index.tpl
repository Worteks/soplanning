<!DOCTYPE html>
<html lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="reply-to" content="support@soplanning.org" />
	<meta name="email" content="support@soplanning.org" />
	<meta name="Identifier-URL" content="http://www.soplanning.org" />
	<meta name="robots" content="noindex,follow" />
	<title>SoPlanning Installation</title>

	<link rel="apple-touch-icon" sizes="180x180" href="{$BASE}/apple-touch-icon.png" />
	<link rel="icon" type="image/png" sizes="32x32" href="{$BASE}/favicon-32x32.png" />
	<link rel="icon" type="image/png" sizes="16x16" href="{$BASE}/favicon-16x16.png" />
	<link rel="manifest" href="{$BASE}/site.webmanifest" />
	<link rel="mask-icon" href="{$BASE}/safari-pinned-tab.svg" color="#5bbad5" />
	<meta name="msapplication-TileColor" content="#da532c" />
	<meta name="theme-color" content="#ffffff" />

	<link rel="stylesheet" href="{$BASE}/assets/plugins/bootstrap-4.5.2/css/bootstrap.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/themes/{$smarty.const.CONFIG_SOPLANNING_THEME}" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/jquery-multiselect-2.4.1/jquery.multiselect.css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/styles.css" type="text/css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/mobile.css" media="screen and (max-width: 1165px)" type="text/css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/print.css" media="print">
	<link rel="stylesheet" href="{$BASE}/assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/select2-4.0.13/dist/css/select2.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/select2-bootstrap.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/spectrum-1.8.1/spectrum.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/timepicker/jquery.ui.timepicker.css" />
	<script src="{$BASE}/assets/js/fonctions.js"></script>
	<script src="{$BASE}/assets/js/jquery-3.5.1.min.js"></script>
	<script src="{$BASE}/assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
	<script src="{$BASE}/assets/plugins/jquery-multiselect-2.4.1/jquery.multiselect.js"></script>
	<script src="{$BASE}/assets/plugins/select2-4.0.13/dist/js/select2.min.js"></script>
	<script src="{$BASE}/assets/plugins/select2-4.0.13/dist/js/i18n/fr.js"></script>
	<script src="{$BASE}/assets/plugins/spectrum-1.8.1/spectrum.js"></script>
	<script src="{$BASE}/assets/plugins/jquery-timepicker-1.11.15/jquery.timepicker.min.js"></script>
	<script src="{$BASE}/assets/plugins/textarea-autosize/autosize.js"></script>
	<script src="{$BASE}/assets/plugins/timepicker/jquery.ui.timepicker.js"></script>

	<link rel="stylesheet" href="{$BASE}/assets/css/simplePage.css" />	
		{$xajax}
	</head>
	<body>
		<div class="container">
			<h3 class="text-center">
				<span class="soplanning_install_title">Simple Online Planning</span>
			</h3>
			<div class="small-container">
				{if isset($smartyData.message)}
					{assign var=messageFinal value=$smartyData.message|formatMessage}
					<div class="alert alert-danger" id="divMessage" >
						<div class="row noprint">
							<div class="col-md-10">
								{$messageFinal}
							</div>
						</div>
					</div>
				{/if}
				{if isset($checkInstall.checkPhpVersion)}
					<div class="alert alert-danger">
						<h4>{#install_wrongPhpVersion#}</h4>
						{#install_currentPhpVersion#} :{$phpversion}
						<br />
						{#install_requiredPhpVersion#} : 5.2
					</div>
				{/if}
				{if isset($checkInstall.checkWritableDatabaseInc)}
					<div class="alert alert-danger">
						{#install_checkWritableDatabaseInc#}
					</div>
				{/if}
				{if isset($checkInstall.checkGD)}
					<div class="alert alert-danger">
						{#install_checkGD#}
					</div>
				{/if}
				{if isset($checkInstall.checkDatabaseVersion)}
					<div class="alert alert-warning">
						<h4>{#install_DBUpgradeResult#}</h4>
						{if isset($checkInstall.databaseUpgradeResult)}
							{$checkInstall.databaseUpgradeResult}
						{/if}
						<br><br>
						<a href="../">{#install_clickLoginAgain#}</a><br />
					</div>
				{/if}
				{if isset($checkInstall.checkDBAccess)  || (isset($checkInstall.checkDatabaseVersion) && $checkInstall.checkDatabaseVersion eq 'database_empty')}
					<form action="database.php" method="post" class="form-horizontal box">
						<h2>{#install_installationDB#}</h2>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="cfgHostname">{#install_mysqlServer#} :</label>
							<div class="col-md-6">
								<input type="text" size="20" name="cfgHostname" id="cfgHostname" value="{$cfgHostname}" class="form-control" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="cfgDatabase">{#install_DBName#} :<br/><span>{#install_missingDBCreated#}.</span></label>
							<div class="col-md-6">
								<input type="text" size="20" name="cfgDatabase" id="cfgDatabase" value="{$cfgDatabase}" class="form-control" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="cfgHostname">{#install_mysqlLogin#} :</label>
							<div class="col-md-4">
								<input type="text" size="20" name="cfgUsername" id="cfgUsername" value="{$cfgUsername}" class="form-control" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="cfgPassword">{#install_mysqlPassword#} :</label>
							<div class="col-md-4">
								<input type="password" size="20" name="cfgPassword" id="cfgPassword" value="{$cfgPassword}" class="form-control" />
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 col-form-label"></label>
							<div class="col-md-6">
								<input class="btn btn-primary" type="submit" value="{#install_startInstallButton#}" />
							</div>
						</div>
					</form>
				{/if}
			<div id="divTranslation">
				<ul class="list-inline flag text-right">
					<li class="list-inline-item"><a tabindex="1" href="?language=pl" class="tooltipEvent" data-title="Polish"><img src="{$BASE}/assets/img/flag/pl.png" alt="Polish" title="Polish"/></a></li>
					<li class="list-inline-item"><a tabindex="2" href="?language=pt" class="tooltipEvent" data-title="Portuguese"><img src="{$BASE}/assets/img/flag/pt.png" alt="Portuguese" title="Portuguese"/></a></li>
					<li class="list-inline-item"><a tabindex="3" href="?language=es" class="tooltipEvent" data-title="Spanish"><img src="{$BASE}/assets/img/flag/es.png" alt="Spanish" title="Spanish" /></a></li>
					<li class="list-inline-item"><a tabindex="4" href="?language=de" class="tooltipEvent" data-title="German"><img src="{$BASE}/assets/img/flag/de.png" alt="German" title="German"/></a></li>
					<li class="list-inline-item"><a tabindex="5" href="?language=da" class="tooltipEvent" data-title="Danish"><img src="{$BASE}/assets/img/flag/da.png" alt="Danish" title="Danish"/></a></li>
					<li class="list-inline-item"><a tabindex="6" href="?language=hu" class="tooltipEvent" data-title="Hungarian"><img src="{$BASE}/assets/img/flag/hu.png" alt="Hungarian" title="Hungarian"/></a></li>
					<li class="list-inline-item"><a tabindex="7" href="?language=nl" class="tooltipEvent" data-title="Dutch"><img src="{$BASE}/assets/img/flag/nl.png" alt="Dutch" title="Dutch"/></a></li>
					<li class="list-inline-item"><a tabindex="8" href="?language=it" class="tooltipEvent" data-title="Italian"><img src="{$BASE}/assets/img/flag/it.png" alt="Italian" title="Italian"/></a></li>
					<li class="list-inline-item"><a tabindex="9" href="?language=fr" class="tooltipEvent" data-title="French"><img src="{$BASE}/assets/img/flag/fr.png" alt="French" title="French"/></a></li>
					<li class="list-inline-item"><a tabindex="10" href="?language=en" class="tooltipEvent" data-title="English"><img src="{$BASE}/assets/img/flag/en.png" alt="English" title="English"/></a></li>
				</ul>
				<p class="text-right text-info"><small><a href="mailto:support@soplanning.org">{#proposerTrad#}</a></small></p>
			</div>
				<p class="text-right text-info">{#install_intro#}</p>
			</div>
		</div>
{include file="www_footer.tpl"}