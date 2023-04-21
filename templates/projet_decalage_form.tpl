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
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#decaler_a_partir_du#} :</label>
		<div class="col-md-6">
			{if $smarty.session.isMobileOrTablet==1}
				<input type="date" class="form-control" name="date_decalage" id="date_decalage" value="" />
			{else}
				<input type="text" class="form-control datepicker" name="date_decalage" id="date_decalage" value="" />		
			{/if}
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#decaler_tout_au#} : <div title="{#decaler_tout_au_aide#}" class="align-self-center cursor-help tooltipster" style="display:inline"><i class="fa fa-question-circle" aria-hidden="true"></i></div></label>
		<div class="col-md-6">
			{if $smarty.session.isMobileOrTablet==1}
				<input type="date" class="form-control" name="date_nouvelle" id="date_nouvelle" value="" />
			{else}
				<input type="text" class="form-control datepicker" name="date_nouvelle" id="date_nouvelle" value="" />		
			{/if}
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<br />
			<input id="butSubmitDecalage" type="button" class="btn btn-primary" value="{#enregistrer#|escape:"html"}" onclick="$('#divPatienter').removeClass('d-none');this.disabled=true; xajax_projet_decalage_submit('{$projet.projet_id}', document.getElementById('date_decalage').value, document.getElementById('date_nouvelle').value);"/>
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