<!DOCTYPE html>
<html lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="reply-to" content="support@soplanning.org" />
	<meta name="email" content="support@soplanning.org" />
	<meta name="Identifier-URL" content="http://www.soplanning.org" />
	<meta name="robots" content="noindex,follow" />
	<title>
		{if $smarty.const.CONFIG_SOPLANNING_TITLE neq "SOPlanning"}
			{$smarty.const.CONFIG_SOPLANNING_TITLE|xss_protect}
		{else}
			SOPlanning - Simple Online Planning
		{/if}
	</title>
	<link rel="apple-touch-icon" sizes="180x180" href="{$BASE}/apple-touch-icon.png" />
	<link rel="icon" type="image/png" sizes="32x32" href="{$BASE}/favicon-32x32.png" />
	<link rel="icon" type="image/png" sizes="16x16" href="{$BASE}/favicon-16x16.png" />
	<link rel="manifest" href="{$BASE}/site.webmanifest" />
	<link rel="mask-icon" href="{$BASE}/safari-pinned-tab.svg" color="#5bbad5" />
	<meta name="msapplication-TileColor" content="#da532c" />
	<meta name="theme-color" content="#ffffff" />

	<link rel="stylesheet" href="{$BASE}/assets/plugins/bootstrap-4.5.2/css/bootstrap.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/themes/{$smarty.const.CONFIG_SOPLANNING_THEME}?{$infoVersion}" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/jquery-multiselect-2.4.1/jquery.multiselect.css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/styles.css?{$infoVersion}" type="text/css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/mobile.css?{$infoVersion}" media="screen and (max-width: 1165px)" type="text/css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/print.css?{$infoVersion}" media="print">
	<link rel="stylesheet" href="{$BASE}/assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/select2-4.0.13/dist/css/select2.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/css/select2-bootstrap.min.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/spectrum-1.8.1/spectrum.css" />
	<link rel="stylesheet" href="{$BASE}/assets/plugins/timepicker/jquery.ui.timepicker.css" />
	<script src="{$BASE}/assets/js/fonctions.js?{$infoVersion}"></script>
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
		{if $smarty.const.CONFIG_SOPLANNING_LOGO != ''}
				<img src="./upload/logo/{$smarty.const.CONFIG_SOPLANNING_LOGO}" alt='logo' style='height:40px;' id="logo" /><br />
		{/if}
		{if $smarty.const.CONFIG_SOPLANNING_TITLE neq "SOPlanning"}
			<span class="soplanning_index_title1">{$smarty.const.CONFIG_SOPLANNING_TITLE|xss_protect}</span>
		{else}
			<span class="soplanning_index_title2">Simple Online Planning</span>
		{/if}
		{if isset($infoVersion)}
			<small>v{$infoVersion}</small>
		{/if}
	</h3>
	<div class="small-container">
		{if isset($alerte)}
			<div class="alert alert-danger">{$alerte}</div>
		{/if}

		{if isset($smartyData.message)}
			{assign var=messageFinal value=$smartyData.message|formatMessage}
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<i class="fa fa-lg fa-exclamation-triangle" aria-hidden="true"></i>&nbsp;&nbsp;{$messageFinal}
			</div>
		{/if}

		{if isset($blocked)}
			<div class="alert alert-danger">{$blocked}</div>
			<br><br><br>
		{else}
			<form action="process/login.php" method="post" class="form-horizontal box" id="formLogin">
				<div class="form-group row col-md-12">
					<label for="login" class="col-md-4 col-sm-4 control-label">{#login_login#} :</label>
					<div class="col-md-8 col-sm-8">
						<input type="text" class="form-control" name="login" id="login" />
					</div>
				</div>
				<div class="form-group row col-md-12">
					<label for="password" class="col-md-4 col-sm-4 control-label">{#login_password#} :</label>
					<div class="col-md-8 col-sm-8">
						<input type="password" class="form-control" name="password" id="password" />
					</div>
				</div>
				<div class="form-group row col-md-12">
					<label for="password" class="col-md-4 col-sm-4 control-label">&nbsp;</label>
					<div class="col-md-8 col-sm-8">
						<input class="form-check-input" type="checkbox" name="remember" id="remember" value="remember" style="margin-left:0px">
						<label class="form-check-label" for="remember" style="margin-left:20px">{#remember_checkbox#}</label>
					</div>
				</div>
				<div class="form-group">
					<div  class="col-12 text-center">
						<input class="btn btn-primary" type="submit" value="{#loginTxt#}" />
					</div >
				</div>
			</form>
			<div class="form-group text-center">
			{if $smarty.const.CONFIG_SOPLANNING_OPTION_ACCES ==1}
				<a href="planning.php?public=1">{#accesPublic#}</a> &middot;
			{/if}
			<a href="#pwdReminderModal" role="button" data-toggle="modal">{#rappelPwdTitre#}</a><br /><br />
			</div>
			<div id="divTranslation">
				<ul class="list-inline flag text-right">
					<li class="list-inline-item"><a href="?language=pl" class="tooltipEvent" data-title="Polish"><img src="{$BASE}/assets/img/flag/pl.png" alt="Polish" title="Polish"/></a></li>
					<li class="list-inline-item"><a href="?language=pt" class="tooltipEvent" data-title="Portuguese"><img src="{$BASE}/assets/img/flag/pt.png" alt="Portuguese" title="Portuguese"/></a></li>
					<li class="list-inline-item"><a href="?language=br" class="tooltipEvent" data-title="English"><img src="{$BASE}/assets/img/flag/br.png" alt="Brazilian Portuguese" title="Brazilian Portuguese"/></a></li>
					<li class="list-inline-item"><a href="?language=es" class="tooltipEvent" data-title="Spanish"><img src="{$BASE}/assets/img/flag/es.png" alt="Spanish" title="Spanish" /></a></li>
					<li class="list-inline-item"><a href="?language=de" class="tooltipEvent" data-title="German"><img src="{$BASE}/assets/img/flag/de.png" alt="German" title="German"/></a></li>
					<li class="list-inline-item"><a href="?language=da" class="tooltipEvent" data-title="Danish"><img src="{$BASE}/assets/img/flag/da.png" alt="Danish" title="Danish"/></a></li>
					<li class="list-inline-item"><a href="?language=hu" class="tooltipEvent" data-title="Hungarian"><img src="{$BASE}/assets/img/flag/hu.png" alt="Hungarian" title="Hungarian"/></a></li>
					<li class="list-inline-item"><a href="?language=nl" class="tooltipEvent" data-title="Dutch"><img src="{$BASE}/assets/img/flag/nl.png" alt="Dutch" title="Dutch"/></a></li>
					<li class="list-inline-item"><a href="?language=it" class="tooltipEvent" data-title="Italian"><img src="{$BASE}/assets/img/flag/it.png" alt="Italian" title="Italian"/></a></li>
					<li class="list-inline-item"><a href="?language=fr" class="tooltipEvent" data-title="French"><img src="{$BASE}/assets/img/flag/fr.png" alt="French" title="French"/></a></li>
					<li class="list-inline-item"><a href="?language=en" class="tooltipEvent" data-title="English"><img src="{$BASE}/assets/img/flag/en.png" alt="English" title="English"/></a></li>
				</ul>
				<p class="text-right text-info"><small><a href="mailto:support@soplanning.org">{#proposerTrad#}</a></small></p>
			</div>
			<div id="infosVersion" class="alert alert-warning" style="display:none"></div>
		{/if}
	</div>
</div>
		<div class="modal" tabindex="-1" role="dialog" id="pwdReminderModal">
		<div class="modal-dialog modal-dialog-normal" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{#rappelPwdTitre#}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
				<input type="text" id="rappel_pwd" placeholder="{#rappelPwdVotreEmail#}" class="form-control" />
				</div>
				<div class="modal-footer">
					<button class="btn btn-primary" id="changePwd">{#submit#}</button>
				</div>
			</div>
		</div>
		</div>
	<script type="text/javascript" src="assets/js/login.js"></script>
	<script type="text/javascript" src="assets/js/fonctions.js"></script>
	<script>
	document.getElementById('login').focus();
	setTimeout("xajax_checkAvailableVersion('home');", 3000);
	</script>
{include file="www_footer.tpl"}