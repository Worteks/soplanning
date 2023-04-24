{* Smarty *}
<form class="form-horizontal" id="formIcal">
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{#icalExport_users#} :</label>
		<div class="col-md-8 radio-inline">
			<input type="radio" name="ical_users" id="ical_users_moi" value="ical_users_moi" checked="checked" onClick="xajax_icalGenererLien($('#anciennete').val(), getRadioValue('ical_users'), getRadioValue('ical_projets'), getSelectValue('icalProjetsChoix'));">
			<label for="ical_users_moi">{#icalExport_users_moi#}</label>
			<br />
			<input type="radio" name="ical_users" id="ical_users_tous" value="ical_users_tous" onClick="xajax_icalGenererLien($('#anciennete').val(), getRadioValue('ical_users'), getRadioValue('ical_projets'), getSelectValue('icalProjetsChoix'));">
			<label for="ical_users_tous">{#icalExport_users_tous#}</label>
			<br />
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{#icalExport_projets#} :</label>
		<div class="col-md-8 radio-inline">
			<input type="radio" name="ical_projets" id="ical_projets_tous" value="ical_projets_tous" checked="checked" onClick="$('#divIcalProjets').addClass('hidden');" onClick="xajax_icalGenererLien($('#anciennete').val(), getRadioValue('ical_users'), getRadioValue('ical_projets'), getSelectValue('icalProjetsChoix'));">
			<label for="ical_projets_tous">{#icalExport_projets_tous#}</label>
			<br />
			<input type="radio" name="ical_projets" id="ical_projets_liste" value="ical_projets_liste" onClick="$('#divIcalProjets').removeClass('hidden');xajax_icalGenererLien($('#anciennete').val(), getRadioValue('ical_users'), getRadioValue('ical_projets'), getSelectValue('icalProjetsChoix'));">
			<label for="ical_projets_liste">{#icalExport_projets_liste#}</label>
			<div id="divIcalProjets" class="hidden">
					<select multiple="multiple" name="icalProjetsChoix" id="icalProjetsChoix" class="form-control {if !$smarty.session.isMobileOrTablet}select2{/if}" onfocus="blur();" tabindex="1" style="width:100%" onchange="xajax_icalGenererLien(getRadioValue('ical_users'), getRadioValue('ical_projets'), getSelectValue('icalProjetsChoix'), $('#anciennete').val());">
					<option value="">- - - - - - - - - - -</option>
					{assign var="groupeCourant" value="-1"}
					{foreach from=$listeProjets item=projet}
						{if $groupeCourant != $projet.groupe_id}
							{assign var="groupeCourant" value=$projet.groupe_id}
							{if $projet.groupe_id == ""}
								{assign var="nomgroupe" value=#projet_liste_sansGroupes#}
							{else}
								{assign var="nomgroupe" value=$projet.nom_groupe}
							{/if}
							<optgroup label="{$nomgroupe}"></optgroup>
						{/if}
						<option value="{$projet.projet_id}">{$projet.nom} ({$projet.projet_id}) {if $projet.livraison neq ''} - S{$projet.livraison}{/if}</option>
					{/foreach}
				</select>
			</div>
			<br />
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{#icalExport_delai#} :</label>
		<div class="col-md-6 form-inline">
			<select id="anciennete" name="anciennete" onchange="xajax_icalGenererLien($('#anciennete').val(), getRadioValue('ical_users'), getRadioValue('ical_projets'), getSelectValue('icalProjetsChoix'));" class="form-control">
				<option value="0">0 {#winPeriode_mois#} {#seulement_taches_a_venir#}</option>
				<option value="1">1 {#winPeriode_mois#}</option>
				<option value="3" selected="selected">3 {#winPeriode_mois#}</option>
				<option value="6">6 {#winPeriode_mois#}</option>
				<option value="12">12 {#winPeriode_mois#}</option>
				<option value="24">24 {#winPeriode_mois#}</option>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-3 col-form-label">{#icalExport_url#} :</label>
		<div class="col-md-9 form-inline">
			<input type="text" id="inputLienIcal" value="{$lienIcal}" class="form-control" style="width:350px">
			&nbsp;<span title="{#ical_instructions#}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
			<br /><br />
		</div>
	</div>
	<div class="form-group row">
		<label class="col-md-4 col-form-label">{#icalExport_download#} :</label>
		<div class="col-md-6 form-inline">
			<a href="export_ical.php"><img src="assets/img/pictos/download.png" /></a>
			&nbsp;<span title="{#ical_instructions2#}" class="align-self-center cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
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