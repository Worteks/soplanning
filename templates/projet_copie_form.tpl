{* Smarty *}
<form method="post" action="" target="_blank" onsubmit="return false;">
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#tab_projet#} :</label>
		<div class="col-md-7">
			{$projet.nom}
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#taches_liste_taches#} :</label>
		<div class="col-md-6">
			{$total}
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_copie_projet_id#} :</label>
		<div class="col-md-6">
			<input class="form-control" name="projet_id_copie" id="projet_id_copie" type="text" maxlength="50" value="{$projet.projet_id}" />
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#projet_copie_nom#} :</label>
		<div class="col-md-6">
			<input class="form-control" name="nom_copie" id="nom_copie" type="text" maxlength="50" value="{$projet.nom} (2)" />
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#projet_copie_a_partir_de#} :</label>
		<div class="col-md-8 radio-inline">
			<input type="radio" name="radio_copie_a_partir" id="radio_copie_a_partir_debut" value="radio_copie_a_partir_debut" checked="checked" />
			<label for="radio_copie_a_partir_debut">
				{#projet_copie_radio_copie_a_partir_debut#}
				{if $tacheDebut.date_debut neq ""}({$tacheDebut.date_debut|sqldate2userdate}){/if}
			</label>
			<br>
			<input type="radio" name="radio_copie_a_partir" id="radio_copie_a_partir_date" value="radio_copie_a_partir_date" />
			<label for="radio_copie_a_partir_date">{#projet_copie_radio_copie_a_partir_date#}</label>
			{if $smarty.session.isMobileOrTablet==1}
				<input type="date" class="form-control" name="date_debut_copie" id="date_debut_copie" value="" style="display:inline;block" autocomplete="off" />
			{else}
				<input type="text" class="form-control datepicker" name="date_debut_copie" id="date_debut_copie" value="" style="display:inline-block" autocomplete="off" />		
			{/if}
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#projet_copie_jusqua#} :</label>
		<div class="col-md-8 radio-inline">
			<input type="radio" name="radio_copie_jusqua" id="radio_copie_jusqua_fin" value="radio_copie_jusqua_fin" checked="checked" />
			<label for="radio_copie_jusqua_fin">
				{#projet_copie_radio_copie_jusqua_fin#}
				{if $tacheFin.date_fin neq ""}({$tacheFin.date_fin|sqldate2userdate}){elseif $tacheFin.date_debut neq ""}({$tacheFin.date_debut|sqldate2userdate}){/if}
			</label>
			<br>
			<input type="radio" name="radio_copie_jusqua" id="radio_copie_jusqua_date" value="radio_copie_jusqua_date" />
			<label for="radio_copie_jusqua_date">{#projet_copie_radio_copie_jusqua_date#}</label>
			{if $smarty.session.isMobileOrTablet==1}
				<input type="date" class="form-control" name="date_fin_copie" id="date_fin_copie" value="" style="display:inline;block" autocomplete="off" />
			{else}
				<input type="text" class="form-control datepicker" name="date_fin_copie" id="date_fin_copie" value="" style="display:inline-block" autocomplete="off" />		
			{/if}
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#projet_copie_demarrer#} :</label>
		<div class="col-md-8 radio-inline">
			{if $smarty.session.isMobileOrTablet==1}
				<input type="date" class="form-control" name="date_demarrage" id="date_demarrage" value="" style="display:inline;block" autocomplete="off" />
			{else}
				<input type="text" class="form-control datepicker" name="date_demarrage" id="date_demarrage" value="" style="display:inline-block" autocomplete="off" />		
			{/if}
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<br />
			<input id="butSubmitCopie" type="button" class="btn btn-primary" value="{#enregistrer#|escape:"html"}" onclick="$('#divPatienter').removeClass('d-none');/*this.disabled=true*/; xajax_projet_copie_submit('{$projet.projet_id}', $('#projet_id_copie').val(), $('#nom_copie').val(), getRadioValue('radio_copie_a_partir'), $('#date_debut_copie').val(), getRadioValue('radio_copie_jusqua'), $('#date_fin_copie').val(), $('#date_demarrage').val());"/>
			<div id="divPatienter" class="d-none" style="margin-left:20px;display:inline-block"><img src="assets/img/pictos/loading16.gif" alt="" /></div>
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