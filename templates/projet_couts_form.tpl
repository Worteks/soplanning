<form method="POST" action="" target="_blank" id="projectTarifForm">
	<input type="hidden" name="saved" id="saved" value="{$projet.saved}" />
	<input type="hidden" name="old_projet_id" id="old_projet_id" value="{$projet.projet_id}" />
	<input type="hidden" name="origine" id="origine" value="{$origine}" />
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#winProjet_nomProjet#} :</label>
		<div class="col-md-6" style="padding-top:7px">
			{$projet.nom}
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_budget_montant#} :</label>
		<div class="col-md-4">
			<input type="number" step="0.01" class="form-control" id="budget_montant" maxlength="10" value="{$projet.budget_montant|default:0}" />
		</div>€
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_budget_consomme#} :</label>
		<div class="col-md-3" style="padding-top:7px">
			{$projet.budget_consomme|formaterNombreDecimal} €
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_budget_restant#} :</label>
		<div class="col-md-3" style="padding-top:7px">
			{$projet.budget_restant|formaterNombreDecimal} €
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_budget_temps#} :</label>
		<div class="col-md-4">
			<input type="number" step="0.01" class="form-control" id="budget_temps" maxlength="10" value="{$projet.budget_temps|default:0}" />
		</div> {#heures#} {#projet_soit#} {$projet.budget_temps|heures2Jours} {#winPeriode_jour#}
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_temps_consomme#} :</label>
		<div class="col-md-6" style="padding-top:7px">
			{$projet.temps_consomme|formaterNombreDecimal} {#heures#} {#projet_soit#} {$projet.temps_consomme|heures2Jours} {#winPeriode_jour#}
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_temps_restant#} :</label>
		<div class="col-md-6" style="padding-top:7px">
			{$projet.temps_restant|formaterNombreDecimal} {#heures#} {#projet_soit#} {$projet.temps_restant|heures2Jours} {#winPeriode_jour#} 
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_couts_existants#} :</label>
		<div class="col-md-8" style="padding-top:7px">
			<table width="100%">
			{foreach from=$tarifs item=tarif}
				<tr>
					<td width="65%">{$tarif.nom}</td>
					<td>
						<input type="number" step="0.01" class="form-control" id="tarif_{$tarif.projet_user_tarif_id}" maxlength="10" value="{$tarif.tarif_horaire|default:0}" style="width:90px;display:inline" />&nbsp;€
					</td>
					<td>
						&nbsp;
						<a href="javascript:xajax_projet_couts_supprimer_personne('{$projet.projet_id}', '{$tarif.projet_user_tarif_id}');undefined;" onclick="javascript: return confirm('{#winPeriode_supprimer#|xss_protect}')"><i style="margin-top:9px" class="fa fa-trash-o fa-lg fa-fw" aria-hidden="true"></i></a>
					</td>
				</tr>
			{/foreach}
			</table>
		</div>
	</div>
	<br>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_couts_ajouter#} :</label>
		<div class="col-md-8">
			<select multiple="multiple" id="user_id_couts" class="form-control {if $smarty.session.isMobileOrTablet!=1}select2{/if}" style="width:70%;display:inline-block">
				{assign var=groupeTemp value=""}
				{foreach from=$listeUsers item=userCourant name=loopUsers}
					{if $userCourant.user_groupe_id neq $groupeTemp}
						<optgroup label="{$userCourant.groupe_nom}">
					{/if}
					<option value="{$userCourant.user_id}" >{$userCourant.nom}</option>
					{if $userCourant.user_groupe_id neq $groupeTemp}
						</optgroup>
					{/if}
					{assign var=groupeTemp value=$userCourant.user_groupe_id}
				{/foreach}
			</select>
		</div>
	</div>
	<div class="form-group row col-md-12">
	<div class="col-md-4 col-form-label"></div>
		<div class="col-md-8">
			<br />
			<input type="button" value="{#enregistrer#|escape:"html"}" class="btn btn-primary" onClick="xajax_projet_couts_submit('{$projet.projet_id}', $('#budget_montant').val(), $('#budget_temps').val(), getSelectValue('user_id_couts'), getInputs('projectTarifForm', 'tarif'))" />
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
