{* Smarty *}
<form method="post" action="" target="_blank" name="formUser"  onSubmit="return false;">
	<div class="row">
		<label class="col-md-4 col-form-label">{#user_identifiant#} :</label>
		<div class="col-md-5 col-form-label">
				{$user_form.user_id|xss_protect}
		</div>
	</div>
	<div class="row">
		<label class="col-md-4 col-form-label">{#user_nom#} :</label>
		<div class="col-md-5 4 col-form-label">
			{$user_form.nom|xss_protect}
		</div>
	</div>
	<div class="row">
		<label class="col-md-4 col-form-label">{#user_login#} :</label>
		<div class="col-md-5 col-form-label">
			{$user_form.login|xss_protect}
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{#user_password#} :</label>
		<div class="col-md-5">
			<input id="password_tmp" type="password" class="form-control" value="" maxlength="50" autocomplete="off" />
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{#user_email#} :</label>
		<div class="col-md-5">
			<input id="email_user" type="text" class="form-control" value="{$user_form.email|xss_protect}" maxlength="255" autocomplete="off" />
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{#user_notifications#} :</label>
		<div class="col-md-6 form-check form-check-inline">
		<div class="form-check">
				<input class="form-check-input" type="radio" id="notificationsOui" name="notifications" value="oui" {if $user_form.notifications eq "oui"}checked="checked"{/if}>
				<label class="form-check-label" for="notificationsOui">{#oui#}</label>
		</div>
		<div class="form-check">
				<input class="form-check-input" type="radio" id="notificationsNon" name="notifications" value="non" {if $user_form.notifications eq "non"}checked="checked"{/if}>
				<label class="form-check-label" for="notificationsNon">{#non#}</label>
		</div>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{#langue#} :</label>
		<div class="col-md-5">
			<select id="langue" class="form-control" onChange="document.location='planning.php?language='+this.value;">
				<option value="fr" {if $lang eq "fr"}selected="selected"{/if}>French</option>
				<option value="en" {if $lang eq "en"}selected="selected"{/if}>English</option>
				<option value="nl" {if $lang eq "nl"}selected="selected"{/if}>Dutch</option>
				<option value="it" {if $lang eq "it"}selected="selected"{/if}>Italian</option>
				<option value="es" {if $lang eq "es"}selected="selected"{/if}>Spanish</option>
				<option value="de" {if $lang eq "de"}selected="selected"{/if}>German</option>
				<option value="pt" {if $lang eq "pt"}selected="selected"{/if}>Portuguese</option>
				<option value="br" {if $lang eq "br"}selected="selected"{/if}>Brazilian Portuguese</option>
				<option value="pl" {if $lang eq "pl"}selected="selected"{/if}>Polish</option>
				<option value="da" {if $lang eq "da"}selected="selected"{/if}>Danish</option>
				<option value="hu" {if $lang eq "hu"}selected="selected"{/if}>Hungarian</option>
				<option value="id" {if $lang eq "id"}selected="selected"{/if}>Indonesian</option>
			</select>
		</div>
	</div>
	<div class="row">
		<label class="col-md-4 col-form-label">{#user_dateformat#} :</label>
		<div class="col-md-5">
			<select name="dateformat" id="dateformat" class="form-control">
				<option value="fr" {if $user_form.tabPreferences.dateformat eq "fr"}selected="selected"{/if}>{#user_dateformatfr#}</option>
				<option value="us" {if $user_form.tabPreferences.dateformat eq "us"}selected="selected"{/if}>{#user_dateformatus#}</option>
				<option value="jp" {if $user_form.tabPreferences.dateformat eq "jp"}selected="selected"{/if}>{#user_dateformatjp#}</option>
			</select>
		</div>
	</div>
	<div class="row">
		<label class="col-md-4 col-form-label">{#planning_position#} :</label>
		<div class="col-md-6 form-check form-check-inline">
		<div class="form-check">
				<input class="form-check-input" type="radio" id="positionLast" name="position" value="last" {if $user_form.tabPreferences.positionPlanning eq "last" or $user_form.tabPreferences.positionPlanning eq ""}checked="checked"{/if}>
				<label class="form-check-label" for="positionLast">{#planning_position_last#}</label>
		</div>
		<div class="form-check">
				<input class="form-check-input" type="radio" id="positionToday" name="position" value="today" {if $user_form.tabPreferences.positionPlanning eq "today"}checked="checked"{/if}>
				<label class="form-check-label" for="positionToday">{#planning_position_today#}</label>
		</div>
		</div>
	</div>
	<div class="row">
		<label class="col-md-4 col-form-label">{#user_prefs_vuedefaut#} :</label>
		<div class="col-md-6 form-check form-check-inline">
		<div class="form-check">
				<input class="form-check-input" type="radio" id="vueDefautPlanning" name="user_prefs_vueplanning" value="vuePlanning" {if $user_form.tabPreferences.vuePlanning eq "" or $user_form.tabPreferences.vuePlanning eq "vuePlanning"}checked="checked"{/if}>
				<label class="form-check-label" for="vueDefautPlanning">{#menuPlanningVuePlanning#}</label>
		</div>
		<div class="form-check">
				<input class="form-check-input" type="radio" id="vueDefautTaches" name="user_prefs_vueplanning" value="vueTaches" {if $user_form.tabPreferences.vuePlanning eq "vueTaches"}checked="checked"{/if}>
				<label class="form-check-label" for="vueDefautTaches">{#menuPlanningVueTaches#}</label>
		</div>
		</div>
	</div>
	<div class="row">
		<div class="offset-md-4 col-md-6 form-check form-check-inline">
		<div class="form-check">
			<input class="form-check-input" type="radio" id="vueDefautPersonne" name="user_prefs_vuedefaut" value="vuePersonne" {if $user_form.tabPreferences.vueDefaut eq "" or $user_form.tabPreferences.vueDefaut eq "vuePersonne"}checked="checked"{/if}>
			<label class="form-check-label" for="vueDefautPersonne">{#menuPlanningCompletPersonne#}</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" id="vueDefautProjet" name="user_prefs_vuedefaut" value="vueProjet" {if $user_form.tabPreferences.vueDefaut eq "vueProjet"}checked="checked"{/if}>
			<label class="form-check-label" for="vueDefautProjet">{#menuPlanningCompletProjet#}</label>
		</div>
		</div>
	</div>
	
	<div class="form-group row">
		<div class="offset-md-4 col-md-6 form-check form-check-inline">
		<div class="form-check">
			<input class="form-check-input" type="radio" id="vueDefautMois" name="user_prefs_vuedefaut_jourmois" value="vueMois" {if $user_form.tabPreferences.vueJourMois eq "" or $user_form.tabPreferences.vueJourMois eq "vueMois"}checked="checked"{/if}>
			<label class="form-check-label" for="vueDefautMois">{#menuPlanningMois#}</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" id="vueDefautJour" name="user_prefs_vuedefaut_jourmois" value="vueJour" {if $user_form.tabPreferences.vueJourMois eq "vueJour"}checked="checked"{/if}>
			<label class="form-check-label" for="vueDefautJour">{#menuPlanningJour#}</label>
		</div>
		</div>
	</div>	

	<div class="form-group row">
		<div class="offset-md-4 col-md-6 form-check form-check-inline">
		<div class="form-check">
			<input class="form-check-input" type="radio" id="vueDefautCompacte" name="user_prefs_vuedefaut_largereduit" value="vueMois" {if $user_form.tabPreferences.vueJourMois eq "" or $user_form.tabPreferences.vueJourMois eq "vueMois"}checked="checked"{/if}>
			<label class="form-check-label" for="vueDefautCompacte">{#menuPlanningReduit#}</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" id="vueDefautLarge" name="user_prefs_vuedefaut_largereduit" value="vueJour" {if $user_form.tabPreferences.vueLargeReduit eq "vueLarge"}checked="checked"{/if}>
			<label class="form-check-label" for="vueDefautLarge">{#menuPlanningLarge#}</label>
		</div>
		</div>
	</div>	
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{#user_masquerLigneVide#} :</label>
		<div class="col-3">
			<select name="masquerLigneVide" id="masquerLigneVide" class="form-control">
				<option value="1" {if $user_form.tabPreferences.masquerLigneVide == 1}selected="selected"{/if}>{#oui#}</option>
				<option value="0" {if !isset($user_form.tabPreferences.masquerLigneVide) || $user_form.tabPreferences.masquerLigneVide === "0"}selected="selected"{/if}>{#non#}</option>

			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{#user_afficherTableauRecap#} :</label>
		<div class="col-3">
			<select name="afficherTableauRecap" id="afficherTableauRecap" class="form-control">
				<option value="1" {if !isset($user_form.tabPreferences.afficherTableauRecap) || $user_form.tabPreferences.afficherTableauRecap == 1}selected="selected"{/if}>{#oui#}</option>
				<option value="0" {if $user_form.tabPreferences.afficherTableauRecap === "0"}selected="selected"{/if}>{#non#}</option>
			</select>
		</div>
	</div>
	
	<div class="form-group row">
		<div class="col-md-4">&nbsp;</div>
		<div class="col-md-4">
			<input type="button" class="btn btn-primary" value="{#enregistrer#}" onClick="xajax_submitFormProfil('{$user_form.user_id|xss_protect}', $('#email_user').val(), $('#password_tmp').val(), $('#dateformat').val(), $('#notificationsOui').is(':checked'),$('#positionToday').is(':checked'),$('#vueDefautPlanning').is(':checked'),$('#vueDefautPersonne').is(':checked'),$('#vueDefautMois').is(':checked'),$('#vueDefautLarge').is(':checked'), $('#masquerLigneVide').val(), $('#afficherTableauRecap').val());"/>
		</div>
	</div>
</form>
<script>
	{literal}
	$('.tooltipster').tooltip({
		html: true,
		placement: 'auto',
		boundary: 'window'
	});
	{/literal}
</script>