{* Smarty *}
{include file="www_header.tpl"}

<div class="container">
	<div class="form-group row col-md-12 align-items-center">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					<a href="{$BASE}/feries.php" class="btn btn-default" ><i class="fa fa-plane fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuFeries#}</a>
					<a href="{$BASE}/status.php" class="btn btn-default" ><i class="fa fa-tags fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuStatus#}</a>
					<a href="{$BASE}/lieux.php" class="btn btn-default" ><i class="fa fa-map-marker fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuLieux#}</a>
					<a href="{$BASE}/ressources.php" class="btn btn-default" ><i class="fa fa-plug fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuRessources#}</a>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group row col-md-12 mt-2" id="optionsRow">
		<div class="col-3">
			<div  class="nav flex-column nav-pills soplanning-box" role="tablist" aria-orientation="vertical" id="myTab">
				<a class="nav-link {if !isset($smarty.get.tab) || $smarty.get.tab eq "params-global" || $smarty.get.tab eq ""}active{/if}" id="param-global-tab" data-toggle="pill" href="#param-global" role="tab" aria-controls="param-global" aria-selected="true">{#options_configGenerale#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "param-modules"}active{/if}" id="param-modules-tab" data-toggle="pill" href="#param-modules" role="tab" aria-controls="param-modules" aria-selected="false">{#options_modules#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "param-planning"}active{/if}" id="param-planning-tab" data-toggle="pill" href="#param-planning" role="tab" aria-controls="param-planning" aria-selected="false">{#options_planning#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "param-taches"}active{/if}" id="param-taches-tab" data-toggle="pill" href="#param-taches" role="tab" aria-controls="param-taches" aria-selected="false">{#options_taches#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "param-divers"}active{/if}" id="param-divers-tab" data-toggle="pill" href="#param-divers" role="tab" aria-controls="param-divers" aria-selected="false">{#options_divers#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "param-smtp"}active{/if}" id="param-smtp-tab" data-toggle="pill" href="#param-smtp" role="tab" aria-controls="param-smtp" aria-selected="false">{#options_smtp#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "param-testmail"}active{/if}" id="param-testmail-tab" data-toggle="pill" href="#param-testmail" role="tab" aria-controls="param-testmail" aria-selected="false">{#options_envoyerMailTest#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "param-api"}active{/if}" id="param-api-tab" data-toggle="pill" href="#param-api" role="tab" aria-controls="param-api" aria-selected="false">{#options_api#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "google-login"}active{/if}" id="google-login-tab" data-toggle="pill" href="#google-login" role="tab" aria-controls="google-login" aria-selected="false">{#options_google_login#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "google-2fa"}active{/if}" id="google-2fa-tab" data-toggle="pill" href="#google-2fa" role="tab" aria-controls="google-2fa" aria-selected="false">{#options_2fa#}</a>
				<a class="nav-link {if isset($smarty.get.tab) && $smarty.get.tab eq "param-audit"}active{/if}" id="param-audit-tab" data-toggle="pill" href="#param-audit" role="tab" aria-controls="param-audit" aria-selected="false">{#options_audit#}</a>
			</div >
		</div>
		<div class="col-9">
			<div class="soplanning-box">
				<div class="tab-content">
					<div class="tab-pane fade show {if !isset($smarty.get.tab) || $smarty.get.tab == 'param-global' || $smarty.get.tab eq ""}active{/if}" id="param-global" role="tabpanel" aria-labelledby="param-global-tab">
						<form action="process/options.php" method="POST" class="form-horizontal" enctype="multipart/form-data" id="setupForm">
							<input type="hidden" name="tab" value="param-global">
							<fieldset>
								<legend>
									{#options_configGenerale#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label" for="SOPLANNING_TITLE">{#options_titre#} :</label>
									<div class="col-md-4">
										<input type="text" class="form-control" name="SOPLANNING_TITLE" id="SOPLANNING_TITLE" value="{$smarty.const.CONFIG_SOPLANNING_TITLE|xss_protect}">
									</div>
									<div title="{#options_aide_titre#}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label" for="SOPLANNING_URL">{#options_url#} :</label>
									<div class="col-6">
										<input type="text" class="form-control" name="SOPLANNING_URL" id="SOPLANNING_URL" value="{$smarty.const.CONFIG_SOPLANNING_URL}">
									</div>
									<div title="{#options_aide_url#|cat:'<br>'|cat:$urlSuggeree}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label" for="SOPLANNING_LOGO">{#options_logo#} :</label>
									<div class="col-md-5">
										<input type="hidden" name="old_logo" value="{$smarty.const.CONFIG_SOPLANNING_LOGO}"/>
										<input type="file" accept=".jpg, .png, .jpeg, .gif" name="SOPLANNING_LOGO" id="SOPLANNING_LOGO" class="col-form-label" />
									</div>
									<div title="{#options_aide_logo#}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								{if $smarty.const.CONFIG_SOPLANNING_LOGO != ''}
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label" for="SOPLANNING_LOGO"></label>
									<div class="col-md-8">
										<img src="./upload/logo/{$smarty.const.CONFIG_SOPLANNING_LOGO}" alt='logo' />
										<label class="checkbox-inline">
										<input type="checkbox" name="SOPLANNING_LOGO_SUPPRESSION" id="SOPLANNING_LOGO_SUPPRESSION">
										&nbsp;{#options_logo_supprimer#}
										</label>
									</div>
								</div>
								{/if}
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label" for="SOPLANNING_THEME">{#options_theme#} :</label>
									<div class="col-md-4">
										<input type="hidden" name="old_theme" value="{$smarty.const.CONFIG_SOPLANNING_THEME}"/>
										<select name='SOPLANNING_THEME' id='SOPLANNING_THEME' class="form-control">
										{foreach from=$themes item=t}
											<option value='{$t}.css' {if $t|cat:'.css' == $smarty.const.CONFIG_SOPLANNING_THEME}selected="selected"{/if}>{$t}</option>
										{/foreach}
										</select>
									</div>
									<div title="{#options_aide_theme#}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_acces#} :</label>
									<div class="ml-3 col-md-4 form-check col-form-label">
										<input class="form-check-input" type="radio" name="SOPLANNING_OPTION_ACCES" id="SOPLANNING_OPTION_ACCES_PRIVE" onclick="$('#optionscle').hide();" {if $smarty.const.CONFIG_SOPLANNING_OPTION_ACCES ==0}checked="checked"{/if} value="0">
										<label class="form-check-label" for="SOPLANNING_OPTION_ACCES_PRIVE">
										{#config_options_accesprive#}
										</label>
									</div>
									<div title="{#config_aide_options_accesprive#}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
								<label class="col-md-4 col-form-label"></label>
								<div class="ml-3 col-md-5 form-check col-form-label">
										<input class="form-check-input" type="radio" name="SOPLANNING_OPTION_ACCES" id="SOPLANNING_OPTION_ACCES_PUBLIC" onclick="$('#optionscle').hide();" {if $smarty.const.CONFIG_SOPLANNING_OPTION_ACCES ==1}checked="checked"{/if} value="1">
										<label class="form-check-label" for="SOPLANNING_OPTION_ACCES_PUBLIC">
										{#config_options_accespublic#}
										</label>
									</div>
									<div title="{#config_aide_options_accespublic#}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
								<label class="col-md-4 col-form-label"></label>
								<div class="ml-3 col-md-7 form-check col-form-label">
										<input class="form-check-input" type="radio" name="SOPLANNING_OPTION_ACCES" id="SOPLANNING_OPTION_ACCES_PUBLICCLE" onclick="$('#optionscle').show();" {if $smarty.const.CONFIG_SOPLANNING_OPTION_ACCES ==2}checked="checked"{/if} value="2">
										<label class="form-check-label" for="SOPLANNING_OPTION_ACCES_PUBLICCLE">
										{#config_options_accespubliccle#}
										</label>
									</div>
									<div title="{#config_aide_options_accespubliccle#}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div id="optionscle" style="display:{if $smarty.const.CONFIG_SOPLANNING_OPTION_ACCES ==2}block{else}none{/if};">
											<div class="form-group row col-md-12 align-items-center">
												<label class="col-md-4 col-form-label"></label>
												<label class="col-3 col-form-label" for="CONFIG_SECURE_KEY">{#config_options_clesecurite#} :</label>
												<div class="col-3">
													<input type="text" class="form-control" name="CONFIG_SECURE_KEY" id="CONFIG_SECURE_KEY" value="{$smarty.const.CONFIG_SECURE_KEY}">
												</div>
												<div class="col-1">												
												<a onclick="javascript:token();"><img src="{$BASE}/assets/img/pictos/options.png" title="{#config_options_genererclesecurite#}" class="tooltipster" alt="" /></a>
												</div>
											</div>
											<div class="form-group row col-md-12 align-items-center">
												<label class="col-md-4 col-form-label"></label>
												<label class="col-3 col-form-label">{#config_options_urlpubliccle#} :</label>
												<div class="col-md-5">
												{if $smarty.const.CONFIG_SOPLANNING_URL|substr:-1 == '/' }{assign var='sep' value=''}{else}{assign var='sep' value='/'}{/if}
												<a href="{if $smarty.const.CONFIG_SOPLANNING_URL == ''}{$urlSuggeree}planning.php?public=1&cle={$smarty.const.CONFIG_SECURE_KEY}" target="_blank">{$urlSuggeree}{else}{$smarty.const.CONFIG_SOPLANNING_URL}{$sep}planning.php?public=1&cle={$smarty.const.CONFIG_SECURE_KEY}" target="_blank">{$smarty.const.CONFIG_SOPLANNING_URL}{$sep}{/if}planning.php?public=1&cle={$smarty.const.CONFIG_SECURE_KEY}</a>
												</div>
											</div>
										</div>

								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_visiteur#} :</label>
									<div class="ml-3 col-6 form-check col-form-label">
										<input type="hidden" id="SOPLANNING_OPTION_VISITEUR" name="SOPLANNING_OPTION_VISITEUR" value="{$smarty.const.SOPLANNING_OPTION_VISITEUR}">
										<input class="form-check-input" type="checkbox" name="SOPLANNING_OPTION_VISITEUR_checkbox" id="SOPLANNING_OPTION_VISITEUR_checkbox" {if $smarty.const.CONFIG_SOPLANNING_OPTION_VISITEUR == 1}checked="checked"{/if} onChange="document.getElementById('SOPLANNING_OPTION_VISITEUR').value=(document.getElementById('SOPLANNING_OPTION_VISITEUR_checkbox').checked ? '1' : '0');">
										<label class="form-check-label" for="SOPLANNING_OPTION_VISITEUR_checkbox">{#options_visiteur_modification#}</label>
									</div>
									<div title="{#config_aide_options_visiteur#}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label" for="TIMEZONE">{#option_fuseau#} :</label>
									<div class="col-7">
										<input type="hidden" name="old_timezone" value="{$smarty.const.CONFIG_TIMEZONE}"/>
										<select name='TIMEZONE' id='TIMEZONE' class="form-control">
										{foreach from=$timezones item=libelle key=valeur}
											<option value='{$valeur}' {if $valeur == $smarty.const.CONFIG_TIMEZONE}selected="selected"{/if}>{$libelle}</option>
										{/foreach}
										</select>
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-4"></div>
									<div class="col-md-8">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}"/>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "param-modules"}active{/if}"" id="param-modules">
						<form action="process/options.php" method="POST">
							<input type="hidden" name="tab" value="param-modules">
							<fieldset>
								<legend>
									{#modules#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_lieux#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_LIEUX" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
										<div title="{#config_aide_options_lieux#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_ressources#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_RESSOURCES" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
										<div title="{#config_aide_options_ressources#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
										<div title="{#config_aide_options_audit#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-4"></div>
									<div class="col-6">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}"/>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "param-planning"}active{/if}"" id="param-planning">
						<form action="process/options.php" method="POST">
							<input type="hidden" name="tab" value="param-planning">
							<fieldset>
								<legend>
									{#options_planning#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{assign var=jours value=","|explode:$smarty.const.CONFIG_DAYS_INCLUDED} {#options_joursInclus#} :</label>
									<div class="col-md-8 form-inline">
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="DAYS_INCLUDED[]" value="1" id="chklundi" {if in_array('1', $jours)}checked="checked"{/if}>
											<label class="form-check-label" for="chklundi">{#day_1#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="DAYS_INCLUDED[]" value="2" id="chkmardi" {if in_array('2', $jours)}checked="checked"{/if}>
											<label class="form-check-label" for="chkmardi">{#day_2#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="DAYS_INCLUDED[]" value="3" id="chkmercredi" {if in_array('3', $jours)}checked="checked"{/if}>
											<label class="form-check-label" for="chkmercredi">{#day_3#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="DAYS_INCLUDED[]" value="4" id="chkjeudi" {if in_array('4', $jours)}checked="checked"{/if}>
											<label class="form-check-label" for="chkjeudi">{#day_4#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="DAYS_INCLUDED[]" value="5" id="chkvendredi" {if in_array('5', $jours)}checked="checked"{/if}>
											<label class="form-check-label" for="chkvendredi">{#day_5#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="DAYS_INCLUDED[]" value="6" id="chksamedi" {if in_array('6', $jours)}checked="checked"{/if}>
											<label class="form-check-label" for="chksamedi">{#day_6#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="DAYS_INCLUDED[]" value="0" id="chkdimanche" {if in_array('0', $jours)}checked="checked"{/if}>
											<label class="form-check-label" for="chkdimanche">{#day_0#}</label>
										</div>
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_differencier_weekend#} :</label>
									<div class="col-3">
										<select name="PLANNING_DIFFERENCIE_WEEKEND" class="form-control">
											<option value="1" {if $smarty.const.CONFIG_PLANNING_DIFFERENCIE_WEEKEND eq 1}selected="selected"{/if}>{#options_differencier_weekend_1#}</option>
											<option value="0" {if $smarty.const.CONFIG_PLANNING_DIFFERENCIE_WEEKEND eq 0}selected="selected"{/if}>{#options_differencier_weekend_0#}</option>
										</select>
									</div>
									<div title="{#options_aide_differencier_weekend#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{assign var=heuresAffichees value=","|explode:$smarty.const.CONFIG_HOURS_DISPLAYED} {#options_heuresIncluses#} :</label>
									<div class="col-md-8 form-inline">
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="0" id="hour0" {if in_array('0', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour0">00{#tab_h#}-01{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="1" id="hour1" {if in_array('1', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour1">01{#tab_h#}-02{#tab_h#}</label>
										</div>										
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="2" id="hour2" {if in_array('2', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour2">02{#tab_h#}-03{#tab_h#}</label>
										</div>										
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="3" id="hour3" {if in_array('3', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour3">03{#tab_h#}-04{#tab_h#}</label>
										</div>										
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="4" id="hour4" {if in_array('4', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour4">04{#tab_h#}-05{#tab_h#}</label>
										</div>										
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="5" id="hour5" {if in_array('5', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour5">05{#tab_h#}-06{#tab_h#}</label>
										</div>										
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="6" id="hour6" {if in_array('6', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour6">06{#tab_h#}-07{#tab_h#}</label>
										</div>										
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="7" id="hour7" {if in_array('7', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour7">07{#tab_h#}-08{#tab_h#}</label>
										</div>										
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="8" id="hour8" {if in_array('8', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour8">08{#tab_h#}-09{#tab_h#}</label>
										</div>										
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="9" id="hour9" {if in_array('9', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour9">09{#tab_h#}-10{#tab_h#}</label>
										</div>			
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="10" id="hour10" {if in_array('10', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour10">10{#tab_h#}-11{#tab_h#}</label>
										</div>				
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="11" id="hour11" {if in_array('11', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour11">11{#tab_h#}-12{#tab_h#}</label>
										</div>								
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="12" id="hour12" {if in_array('12', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour12">12{#tab_h#}-13{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="13" id="hour13" {if in_array('13', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour13">13{#tab_h#}-14{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="14" id="hour14" {if in_array('14', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour14">14{#tab_h#}-15{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="15" id="hour15" {if in_array('15', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour15">15{#tab_h#}-16{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="16" id="hour16" {if in_array('16', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour16">16{#tab_h#}-17{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="17" id="hour17" {if in_array('17', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour17">17{#tab_h#}-18{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="18" id="hour18" {if in_array('18', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour18">18{#tab_h#}-19{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="19" id="hour19" {if in_array('19', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour19">19{#tab_h#}-20{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="20" id="hour20" {if in_array('20', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour20">20{#tab_h#}-21{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="21" id="hour21" {if in_array('21', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour21">21{#tab_h#}-22{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="22" id="hour22" {if in_array('22', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour22">22{#tab_h#}-23{#tab_h#}</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="HOURS_DISPLAYED[]" value="23" id="hour23" {if in_array('23', $heuresAffichees)}checked="checked"{/if}>
											<label class="form-check-label" for="hour23">23{#tab_h#}-00{#tab_h#}</label>
										</div>						
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_dureeDefautJour#} :</label>
									<div class="col-md-8 form-inline">
										<input name="DURATION_DAY" {if $smarty.session.isMobileOrTablet==1}type="time" class="form-control"{else}size="2" type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_DURATION_DAY}"/>
										<div title="{#options_aide_dureeDefaut#}" class="cursor-help tooltipster">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></div>
										&nbsp;&nbsp;{#options_dureeDefautMatin#} :&nbsp;
										<input name="DURATION_AM" {if $smarty.session.isMobileOrTablet==1}type="time" class="form-control"{else}size="2" type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_DURATION_AM}" />
										<div title="{#options_aide_dureeDefaut#}" class="cursor-help tooltipster">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></div>
										&nbsp;&nbsp;{#options_dureeDefautApresmidi#} :&nbsp;
										<input name="DURATION_PM" {if $smarty.session.isMobileOrTablet==1}type="time" class="form-control"{else}size="2" type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_DURATION_PM}" />
										<div title="{#options_aide_dureeDefaut#}" class="cursor-help tooltipster">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></div>
									</div>
								</div>
								<input type="hidden" name="PLANNING_DATE_FORMAT" value="1"/>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_nbMoisDefaut#} :</label>
									<div class="col-2">
										<input name="DEFAULT_NB_MONTHS_DISPLAYED" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_DEFAULT_NB_MONTHS_DISPLAYED}" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_nbjoursDefaut#} :</label>
									<div class="col-2">
										<input name="DEFAULT_NB_DAYS_DISPLAYED" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_DEFAULT_NB_DAYS_DISPLAYED}" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_nbLignes#} :</label>
									<div class="col-2">
										<input name="DEFAULT_NB_ROWS_DISPLAYED" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_DEFAULT_NB_ROWS_DISPLAYED}" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_statusAffichage#} :</label>
									<div class="col-md-4">
										<select name="PLANNING_AFFICHAGE_STATUS" class="form-control">
											<option value="aucun" {if $smarty.const.CONFIG_PLANNING_AFFICHAGE_STATUS eq 'aucun'}selected="selected"{/if}>{#options_statusAffichage_aucun#}</option>
											<option value="nom" {if $smarty.const.CONFIG_PLANNING_AFFICHAGE_STATUS eq 'nom'}selected="selected"{/if}>{#options_statusAffichage_nom#}</option>
											<option value="pourcentage" {if $smarty.const.CONFIG_PLANNING_AFFICHAGE_STATUS eq 'pourcentage'}selected="selected"{/if}>{#options_statusAffichage_pourcentage#}</option>
											<option value="pastille" {if $smarty.const.CONFIG_PLANNING_AFFICHAGE_STATUS eq 'pastille'}selected="selected"{/if}>{#options_statusAffichage_pastille#}</option>
										</select>
									</div>
										<div title="{#options_aide_statusAffichage#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_raffraichissement#} :</label>
									<div class="col-2">
										<input name="REFRESH_TIMER" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_REFRESH_TIMER}" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_hauteurLigne#} :</label>
									<div class="col-2">
										<input name="PLANNING_LINE_HEIGHT" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_PLANNING_LINE_HEIGHT}" />
									</div>
										<div title="{#options_aide_hauteurLigne#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>

								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_largeurColonne#} :</label>
									<div class="col-2">
										<input name="PLANNING_COL_WIDTH" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_PLANNING_COL_WIDTH}" />
									</div>
										<div title="{#options_aide_largeurColonne#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_largeurColonneLarge#} :</label>
									<div class="col-2">
										<input name="PLANNING_COL_WIDTH_LARGE" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_PLANNING_COL_WIDTH_LARGE}" />
									</div>
										<div title="{#options_aide_largeurColonneLarge#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_repeterHeaderDate#} :</label>
									<div class="col-2">
										<input name="PLANNING_REPEAT_HEADER" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_PLANNING_REPEAT_HEADER}" />
									</div>
										<div title="{#options_aide_repeterHeaderDate#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-4"></div>
									<div class="col-md-8">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}"/>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "param-taches"}active{/if}"" id="param-taches">
						<form action="process/options.php" method="POST" class="form-horizontal">
							<input type="hidden" name="tab" value="param-taches">
							<fieldset>
								<legend>
									{#options_taches#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#options_uneTacheParJour#} :</label>
									<div class="col-2">
										<select name="PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_PLANNING_ONE_ASSIGNMENT_MAX_PER_DAY eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
										<div title="{#options_aide_uneTacheParJour#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#option_masquer_projet_weekend#} :</label>
									<div class="col-2">
										<select name="PLANNING_HIDE_WEEKEND_TASK" class="form-control">
											<option value="1" {if $smarty.const.CONFIG_PLANNING_HIDE_WEEKEND_TASK eq 1}selected="selected"{/if}>{#non#}</option>
											<option value="0" {if $smarty.const.CONFIG_PLANNING_HIDE_WEEKEND_TASK eq 0}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
										<div title="{#options_aide_hide_weekend_task#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#options_differencier_tache_lien#} :</label>
									<div class="col-2">
										<select name="PLANNING_DIFFERENCIE_TACHE_LIEN" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_PLANNING_DIFFERENCIE_TACHE_LIEN eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_PLANNING_DIFFERENCIE_TACHE_LIEN eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
										<div title="{#options_aide_differencier_tache_lien#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#options_differencier_tache_commentaire#} :</label>
									<div class="col-2">
										<select name="PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
										<div title="{#options_aide_differencier_tache_commentaire#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>

								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#options_differencier_tache_partielle#} :</label>
									<div class="col-2">
										<select name="PLANNING_DIFFERENCIE_TACHE_PARTIELLE" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_PLANNING_DIFFERENCIE_TACHE_PARTIELLE eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_PLANNING_DIFFERENCIE_TACHE_PARTIELLE eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
										<div title="{#options_aide_differencier_tache_partielle#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#options_masquer_feries#} :</label>
									<div class="col-2">
										<select name="PLANNING_MASQUER_FERIES" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_PLANNING_MASQUER_FERIES eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_PLANNING_MASQUER_FERIES eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
										<div title="{#options_aide_masquer_feries#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#option_couleur_taches#} :</label>
									<div class="col-md-6">
										<select name="PLANNING_COULEUR_TACHE" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_PLANNING_COULEUR_TACHE eq 0}selected="selected"{/if}>{#option_couleur_taches_contextuelles#}</option>
											<option value="1" {if $smarty.const.CONFIG_PLANNING_COULEUR_TACHE eq 1}selected="selected"{/if}>{#option_couleur_taches_status#}</option>
										</select>
									</div>
										<div title="{#option_aide_couleur_taches#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#option_contenu_taches_projet#} :</label>
									<div class="col-md-4">
										<select name="PLANNING_TEXTE_TACHES_PROJET" class="form-control">
											<option value="code_projet" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'code_projet'}selected="selected"{/if}>{#option_contenu_taches_code_projet#}</option>
											<option value="code_personne" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'code_personne'}selected="selected"{/if}>{#option_contenu_taches_code_personne#}</option>
											<option value="code_lieu" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'code_lieu'}selected="selected"{/if}>{#option_contenu_taches_code_lieu#}</option>
											<option value="code_ressource" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'code_ressource'}selected="selected"{/if}>{#option_contenu_taches_code_ressource#}</option>
											<option value="nom_projet" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'nom_projet'}selected="selected"{/if}>{#option_contenu_taches_nom_projet#}</option>
											<option value="nom_personne" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'nom_personne'}selected="selected"{/if}>{#option_contenu_taches_nom_personne#}</option>
											<option value="nom_lieu" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'nom_lieu'}selected="selected"{/if}>{#option_contenu_taches_nom_lieu#}</option>
											<option value="nom_ressource" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'nom_ressource'}selected="selected"{/if}>{#option_contenu_taches_nom_ressource#}</option>
											<option value="nom_tache" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'nom_tache'}selected="selected"{/if}>{#option_contenu_taches_nom_tache#}</option>
											<option value="vide" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PROJET eq 'vide'}selected="selected"{/if}>{#option_contenu_taches_vide#}</option>
										</select>
									</div>
										<div title="{#option_aide_contenu_taches_projet#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#option_contenu_taches_personne#} :</label>
									<div class="col-md-4">
										<select name="PLANNING_TEXTE_TACHES_PERSONNE" class="form-control">
											<option value="code_projet" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'code_projet'}selected="selected"{/if}>{#option_contenu_taches_code_projet#}</option>
											<option value="code_personne" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'code_personne'}selected="selected"{/if}>{#option_contenu_taches_code_personne#}</option>
											<option value="code_lieu" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'code_lieu'}selected="selected"{/if}>{#option_contenu_taches_code_lieu#}</option>
											<option value="code_ressource" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'code_ressource'}selected="selected"{/if}>{#option_contenu_taches_code_ressource#}</option>
											<option value="nom_projet" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'nom_projet'}selected="selected"{/if}>{#option_contenu_taches_nom_projet#}</option>
											<option value="nom_personne" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'nom_personne'}selected="selected"{/if}>{#option_contenu_taches_nom_personne#}</option>
											<option value="nom_lieu" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'nom_lieu'}selected="selected"{/if}>{#option_contenu_taches_nom_lieu#}</option>
											<option value="nom_ressource" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'nom_ressource'}selected="selected"{/if}>{#option_contenu_taches_nom_ressource#}</option>
											<option value="nom_tache" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'nom_tache'}selected="selected"{/if}>{#option_contenu_taches_nom_tache#}</option>
											<option value="vide" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_PERSONNE eq 'vide'}selected="selected"{/if}>{#option_contenu_taches_vide#}</option>
										</select>
									</div>
										<div title="{#option_aide_contenu_taches_personne#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								{if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX == 1 }
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#option_contenu_taches_lieu#} :</label>
									<div class="col-md-4">
										<select name="PLANNING_TEXTE_TACHES_LIEU" class="form-control">
											<option value="code_projet" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'code_projet'}selected="selected"{/if}>{#option_contenu_taches_code_projet#}</option>
											<option value="code_personne" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'code_personne'}selected="selected"{/if}>{#option_contenu_taches_code_personne#}</option>
											<option value="code_lieu" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'code_lieu'}selected="selected"{/if}>{#option_contenu_taches_code_lieu#}</option>
											<option value="code_ressource" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'code_ressource'}selected="selected"{/if}>{#option_contenu_taches_code_ressource#}</option>
											<option value="nom_projet" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'nom_projet'}selected="selected"{/if}>{#option_contenu_taches_nom_projet#}</option>
											<option value="nom_personne" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'nom_personne'}selected="selected"{/if}>{#option_contenu_taches_nom_personne#}</option>
											<option value="nom_lieu" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'nom_lieu'}selected="selected"{/if}>{#option_contenu_taches_nom_lieu#}</option>
											<option value="nom_ressource" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'nom_ressource'}selected="selected"{/if}>{#option_contenu_taches_nom_ressource#}</option>
											<option value="nom_tache" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'nom_tache'}selected="selected"{/if}>{#option_contenu_taches_nom_tache#}</option>
											<option value="vide" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_LIEU eq 'vide'}selected="selected"{/if}>{#option_contenu_taches_vide#}</option>
										</select>
									</div>
										<div title="{#option_aide_contenu_taches_lieu#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								{/if}
								{if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES == 1 }
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#option_contenu_taches_ressource#} :</label>
									<div class="col-md-4">
										<select name="PLANNING_TEXTE_TACHES_RESSOURCE" class="form-control">
											<option value="code_projet" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'code_projet'}selected="selected"{/if}>{#option_contenu_taches_code_projet#}</option>
											<option value="code_personne" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'code_personne'}selected="selected"{/if}>{#option_contenu_taches_code_personne#}</option>
											<option value="code_lieu" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'code_lieu'}selected="selected"{/if}>{#option_contenu_taches_code_lieu#}</option>
											<option value="code_ressource" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'code_ressource'}selected="selected"{/if}>{#option_contenu_taches_code_ressource#}</option>
											<option value="nom_projet" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'nom_projet'}selected="selected"{/if}>{#option_contenu_taches_nom_projet#}</option>
											<option value="nom_personne" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'nom_personne'}selected="selected"{/if}>{#option_contenu_taches_nom_personne#}</option>
											<option value="nom_lieu" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'nom_lieu'}selected="selected"{/if}>{#option_contenu_taches_nom_lieu#}</option>
											<option value="nom_ressource" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'nom_ressource'}selected="selected"{/if}>{#option_contenu_taches_nom_ressource#}</option>
											<option value="nom_tache" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'nom_tache'}selected="selected"{/if}>{#option_contenu_taches_nom_tache#}</option>
											<option value="vide" {if $smarty.const.CONFIG_PLANNING_TEXTE_TACHES_RESSOURCE eq 'vide'}selected="selected"{/if}>{#option_contenu_taches_vide#}</option>
										</select>
									</div>
										<div title="{#option_aide_contenu_taches_ressource#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								{/if}
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#option_taille_police_cellule#} :</label>
									<div class="col-md-4">
										<select name="PLANNING_CELL_FONTSIZE" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_PLANNING_CELL_FONTSIZE eq 0}selected="selected"{/if}>{#option_taille_police_cellule_defaut#}</option>
											<option value="9" {if $smarty.const.CONFIG_PLANNING_CELL_FONTSIZE eq 9}selected="selected"{/if}>{#option_taille_police_cellule_micro#}</option>	
											<option value="10" {if $smarty.const.CONFIG_PLANNING_CELL_FONTSIZE eq 10}selected="selected"{/if}>{#option_taille_police_cellule_mini#}</option>
											<option value="12" {if $smarty.const.CONFIG_PLANNING_CELL_FONTSIZE eq 12}selected="selected"{/if}>{#option_taille_police_cellule_medium#}</option>
											<option value="14" {if $smarty.const.CONFIG_PLANNING_CELL_FONTSIZE eq 14}selected="selected"{/if}>{#option_taille_police_cellule_maxi#}</option>
										</select>
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#options_largeurCode#} :</label>
									<div class="col-2">
										<input name="PLANNING_CODE_WIDTH" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_PLANNING_CODE_WIDTH}" />
									</div>
										<div title="{#options_aide_largeurCode#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-5 col-form-label">{#options_largeurCodeLarge#} :</label>
									<div class="col-2">
										<input name="PLANNING_CODE_WIDTH_LARGE" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_PLANNING_CODE_WIDTH_LARGE}" />
									</div>
										<div title="{#options_aide_largeurCodeLarge#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-4"></div>
									<div class="col-6">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}"/>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					
					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "param-divers"}active{/if}"" id="param-divers">
						<form action="process/options.php" method="POST" class="form-horizontal">
							<input type="hidden" name="tab" value="param-divers">
							<fieldset>
								<legend>
									{#options_divers#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_couleursProjets#} :</label>
									<div class="col-6">
										<input name="PROJECT_COLORS_POSSIBLE" type="text" value="{$smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE|xss_protect}" class="form-control" />
									</div>
										<div title="{#options_aide_couleursPossibles#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_lienDefaut#} :</label>
									<div class="col-6">
										<input name="DEFAULT_PERIOD_LINK" type="text" value="{$smarty.const.CONFIG_DEFAULT_PERIOD_LINK}" class="form-control" />
									</div>
										<div title="{#options_aide_LinkPeriod#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_urlRedirection#} :</label>
									<div class="col-6">
										<input name="LOGOUT_REDIRECT" type="text" value="{$smarty.const.CONFIG_LOGOUT_REDIRECT}" class="form-control" />
									</div>
										<div title="{#options_aide_redirect#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_dureeCreneauHoraire#} :</label>
									<div class="col-3">
										<select name="PLANNING_DUREE_CRENEAU_HORAIRE" class="form-control">
											<option value="2" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 2}selected="selected"{/if}>2 {#minutes#}</option>
											<option value="5" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 5}selected="selected"{/if}>5 {#minutes#}</option>
											<option value="10" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 10}selected="selected"{/if}>10 {#minutes#}</option>
											<option value="15" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 15}selected="selected"{/if}>15 {#minutes#}</option>
											<option value="20" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 20}selected="selected"{/if}>20 {#minutes#}</option>
											<option value="30" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 30}selected="selected"{/if}>30 {#minutes#}</option>
											<option value="60" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 60}selected="selected"{/if}>1 {#heure#}</option>
											<option value="120" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 120}selected="selected"{/if}>2 {#heures#}</option>
											<option value="180" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 180}selected="selected"{/if}>3 {#heures#}</option>
											<option value="240" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 240}selected="selected"{/if}>4 {#heures#}</option>
											<option value="300" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 300}selected="selected"{/if}>5 {#heures#}</option>
											<option value="360" {if $smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE eq 360}selected="selected"{/if}>6 {#heures#}</option>
										</select>
									</div>
									<div title="{#options_aide_dureeCreneauHoraire#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>

								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-4"></div>
									<div class="col-6">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}"/>
									</div>
								</div>
							</fieldset>
						</form>
					</div>

					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "param-smtp"}active{/if}"" id="param-smtp">
						<form action="process/options.php" method="POST">
							<input type="hidden" name="tab" value="param-smtp">
							<fieldset>
								<legend>
									{#options_smtp_titre#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_smtp_host#} :</label>
									<div class="col-md-4">
										<input name="SMTP_HOST" type="text" value="{$smarty.const.CONFIG_SMTP_HOST|xss_protect}" class="form-control" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_smtp_port#} :</label>
									<div class="col-2">
										<input name="SMTP_PORT" {if $smarty.session.isMobileOrTablet==1}type="number" class="form-control"{else}type="text" class="form-control"{/if} value="{$smarty.const.CONFIG_SMTP_PORT|xss_protect}" />
									</div>
										<div title="{#options_aide_smtp#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_smtp_secure#}</label>
									<div class="col-md-4">
										<select name="SMTP_SECURE" class="form-control">
											<option value="" {if $smarty.const.CONFIG_SMTP_SECURE eq ""}selected="selected"{/if}>{#options_smtp_nonSecurise#}</option>
											<option value="ssl" {if $smarty.const.CONFIG_SMTP_SECURE eq "ssl"}selected="selected"{/if}>SSL</option>
											<option value="tls" {if $smarty.const.CONFIG_SMTP_SECURE eq "tls"}selected="selected"{/if}>TLS</option>
										</select>
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_smtp_from#} :</label>
									<div class="col-md-4">
										<input name="SMTP_FROM" type="text" value="{$smarty.const.CONFIG_SMTP_FROM|xss_protect}" class="form-control" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_smtp_login#} :</label>
									<div class="col-md-4">
										<input name="SMTP_LOGIN" type="text" value="{$smarty.const.CONFIG_SMTP_LOGIN|xss_protect}" class="form-control" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_smtp_password#} :</label>
									<div class="col-md-4">
										<input name="SMTP_PASSWORD" type="password" size="30" value="{if $smarty.const.CONFIG_SMTP_LOGIN neq ""}XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX{/if}" class="form-control"/>
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-4"></div>
									<div class="col-md-4">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}"/>
									</div>
								</div>
							</fieldset>
						</form>
					</div>

					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "param-testmail"}active{/if}"" id="param-testmail">
						<form action="process/options.php" method="POST" class="form-horizontal">
							<input type="hidden" name="tab" value="param-testmail">
							<fieldset>
								<legend>
									{#options_envoyerMailTest#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_envoyerMailTest_destinataire#} :</label>
									<div class="col-6">
										<input name="mailTestDestinataire" type="text" class="form-control" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label"></label>
									<div class="col-6">
										<input name="smtp_traces" type="checkbox" /> {#afficher_logs_smtp#}
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-4"></div>
									<div class="col-6">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}" {if $smarty.const.CONFIG_SMTP_HOST eq '' || $smarty.const.CONFIG_SMTP_PORT eq '' || $smarty.const.CONFIG_SMTP_FROM eq ''}disabled="disabled"{/if}/>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "param-api"}active{/if}"" id="param-api">
						<form action="process/options.php" method="POST" class="form-horizontal">
							<input type="hidden" name="tab" value="param-api">
							<fieldset>
								<legend>
									{#options_api#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_api_url#} :</label>
									<div class="col-6">
										{if $smarty.const.CONFIG_SOPLANNING_URL neq ""}{$smarty.const.CONFIG_SOPLANNING_URL}{else}SOPLANNING_URL{/if}/api/endpoint
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_api_key#} :</label>
									<div class="col-6">
										<input name="SOPLANNING_API_KEY_NAME" type="text" class="form-control" value="{$smarty.const.CONFIG_SOPLANNING_API_KEY_NAME|xss_protect}" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_api_value#} :</label>
									<div class="col-6">
										<input name="SOPLANNING_API_KEY_VALUE" type="text" class="form-control" value="{$smarty.const.CONFIG_SOPLANNING_API_KEY_VALUE|xss_protect}" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#options_api_doc#} :</label>
									<div class="col-6">
										<a target="_blank" href="https://documenter.getpostman.com/view/13456412/Tz5s4wwD"><i class="fa fa-external-link" aria-hidden="true"></i></a>
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-4"></div>
									<div class="col-6">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}">
									</div>
								</div>
							</fieldset>
						</form>
					</div>

					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "google-login"}active{/if}" id="google-login">
						<form action="process/options.php" method="POST" class="form-horizontal">
							<input type="hidden" name="tab" value="google-login">
							<fieldset>
								<legend>
									{#options_google_login#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-12 col-form-label" style="font-size:13px">{#options_google_login_help#}</label>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-6 col-form-label">{#google_sso_active#} :</label>
									<div class="col-6">
										<input type="checkbox" name="GOOGLE_OAUTH_ACTIVE" id="GOOGLE_OAUTH_ACTIVE" {if $smarty.const.CONFIG_GOOGLE_OAUTH_ACTIVE ==1}checked="checked"{/if} value="1">
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-6 col-form-label">{#google_sso_return_url#} :</label>
									<div class="col-6">
										{if $smarty.const.CONFIG_SOPLANNING_URL eq ""}
											{#google_sso_return_url_need_setup#}
										{else}
											{$smarty.const.CONFIG_SOPLANNING_URL}
										{/if}
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-6 col-form-label">{#google_sso_client_id#} :</label>
									<div class="col-6">
										<input name="GOOGLE_OAUTH_CLIENT_ID" type="text" class="form-control" value="{$smarty.const.CONFIG_GOOGLE_OAUTH_CLIENT_ID|xss_protect}" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-6 col-form-label">{#google_sso_secret#} :</label>
									<div class="col-6">
										<input name="GOOGLE_OAUTH_CLIENT_SECRET" type="text" class="form-control" value="{$smarty.const.CONFIG_GOOGLE_OAUTH_CLIENT_SECRET|xss_protect}" />
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-6"></div>
									<div class="col-6">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}">
									</div>
								</div>
							</fieldset>
						</form>
					</div>

					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "google-2fa"}active{/if}" id="google-2fa">
						<form action="process/options.php" method="POST" class="form-horizontal">
							<input type="hidden" name="tab" value="google-2fa">
							<fieldset>
								<legend>
									{#options_2fa#}
								</legend>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-12 col-form-label" style="font-size:13px">{#options_2fa_help#}</label>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-6 col-form-label">{#google_2fa_active#} :</label>
									<div class="col-6">
										{if $phpversion >= "7"}
											<input type="checkbox" name="GOOGLE_2FA_ACTIVE" id="GOOGLE_2FA_ACTIVE" {if $smarty.const.CONFIG_GOOGLE_2FA_ACTIVE ==1}checked="checked"{/if} value="1">
										{else}
											{#options_2fa_not_compatible#}
										{/if}
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-6"></div>
									<div class="col-6">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}">
									</div>
								</div>
							</fieldset>
						</form>
					</div>

					<div class="tab-pane {if isset($smarty.get.tab) && $smarty.get.tab eq "param-audit"}active{/if}" id="param-audit">
						<form action="process/options.php" method="POST" class="form-horizontal">
							<input type="hidden" name="tab" value="param-audit">
							<fieldset>
								<legend>
									{#options_audit#}
								</legend>
								{if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT eq 0}
									<br>
									<span style="color:#ff0000;font-weight:bold">{#audit_inactif#}</span>
									<br><br>
								{/if}
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit_taches#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT_TACHES" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_TACHES eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_TACHES eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>	
										<div title="{#config_aide_options_audit_taches#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit_projets#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT_PROJETS" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_PROJETS eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_PROJETS eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>	
										<div title="{#config_aide_options_audit_projets#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit_groupes#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT_GROUPES" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_GROUPES eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_GROUPES eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>
											<div title="{#config_aide_options_audit_groupes#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit_utilisateurs#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT_UTILISATEURS" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_UTILISATEURS eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_UTILISATEURS eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>	
											<div title="{#config_aide_options_audit_utilisateurs#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit_equipes#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT_EQUIPES" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_EQUIPES eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_EQUIPES eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>	
											<div title="{#config_aide_options_audit_equipes#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit_lieux#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT_LIEUX" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_LIEUX eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_LIEUX eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>	
											<div title="{#config_aide_options_audit_lieux#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit_ressources#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT_RESSOURCES" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_RESSOURCES eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_RESSOURCES eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>	
											<div title="{#config_aide_options_audit_ressources#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit_statuts#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT_STATUTS" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_STATUTS eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_STATUTS eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>	
											<div title="{#config_aide_options_audit_statuts#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label">{#config_options_audit_connexions#}  :</label>
									<div class="col-2">
										<select name="SOPLANNING_OPTION_AUDIT_CONNEXIONS" class="form-control">
											<option value="0" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_CONNEXIONS eq 0}selected="selected"{/if}>{#non#}</option>
											<option value="1" {if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_CONNEXIONS eq 1}selected="selected"{/if}>{#oui#}</option>
										</select>
									</div>	
											<div title="{#config_aide_options_audit_connexions#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<label class="col-md-4 col-form-label" for="SOPLANNING_OPTION_AUDIT_RETENTION">{#config_options_audit_retention#} :</label>
									<div class="col-2">
										<select name='SOPLANNING_OPTION_AUDIT_RETENTION' id='SOPLANNING_OPTION_AUDIT_RETENTION' class="form-control">
											<option value='5' {if 5 == $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_RETENTION}selected="selected"{/if}>{#config_options_audit_retention_5J#}</option>
											<option value='10' {if 10 == $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_RETENTION}selected="selected"{/if}>{#config_options_audit_retention_10J#}</option>
											<option value='30' {if 30 == $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_RETENTION}selected="selected"{/if}>{#config_options_audit_retention_30J#}</option>
											<option value='60' {if 60 == $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_RETENTION}selected="selected"{/if}>{#config_options_audit_retention_60J#}</option>
											<option value='90' {if 90 == $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_RETENTION}selected="selected"{/if}>{#config_options_audit_retention_90J#}</option>
											<option value='180' {if 180 == $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_RETENTION}selected="selected"{/if}>{#config_options_audit_retention_180J#}</option>
											<option value='360' {if 360 == $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT_RETENTION}selected="selected"{/if}>{#config_options_audit_retention_360J#}</option>
										</select>
									</div>
								</div>
								<div class="form-group row col-md-12 align-items-center">
									<div class="col-md-4"></div>
									<div class="col-6">
										<br />
										<input type="submit" class="btn btn-primary" value="{#enregistrer#}" />
									</div>
								</div>
							</fieldset>
						</form>
					</div>					
				</div>
			</div>
		</div>
	</div>
</div>
<script>
{literal}
	jQuery(document).ready(function(){
		jQuery("a[data-toggle=popover]")
			.popover()
			.click(function(e) {
			e.preventDefault()
		});
	});
	
	var rand = function() {
	return Math.random().toString(36).substr(2); // remove `0.`
	};
	
	var token = function() {
	var key=rand() + rand() + rand(); // to make it longer
	jQuery('#CONFIG_SECURE_KEY').attr('value', key);	
	};
{/literal}
</script>

{include file="www_footer.tpl"}