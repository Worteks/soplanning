{* Smarty *}
<form method="POST" action="" target="_blank" class="form-horizontal">
	<input type="hidden" name="old_lieu_id" id="old_lieu_id" value="{$lieu.lieu_id}" />
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#lieu_identifiant#} :</label>
		<div class="col-md-4">
			{if $lieu.lieu_id neq ''}
			<input name="lieu_id" id="lieu_id" type="text" readonly class="form-control-plaintext" value="{$lieu.lieu_id}">
			{else}
		<input name="lieu_id" id="lieu_id" type="text" maxlength="20" class="form-control" value="{$lieu.lieu_id}"  onChange="xajax_checkLieuId(this.value, '{$lieu.lieu_id}');" /> 
		</div>	
		<div class="col-md-4">
		<span class="col-form-label" id="divStatutCheckLieuId"></span>
			{#winPeriode_lieu_identifiantCarMax#}
			{/if}
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#lieu_nom#} :</label>
		<div class="col-md-5">
			<input name="nom" id="nom" class="form-control" type="text" maxlength="50" value="{$lieu.nom|xss_protect}" />
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#lieu_commentaire#} :</label>
		<div class="col-md-7">
			<textarea name="commentaire" id="commentaire" class="form-control" maxlength="255">{$lieu.commentaire|xss_protect}</textarea>
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#exclusivite#} :</label>
		<div class="col-md-7 form-check form-check-inline">
		&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" class="form-check-input" name="lieu_exclusif" id="lieu_exclusif" {if $lieu.exclusif == 1}checked="checked"{/if}><label class="form-check-label" for="lieu_exclusif">{#lieu_exclusif#}</label>
		&nbsp;&nbsp;<span data-tooltip-content="#tooltip-exclusivite" data-toggle="tooltip" data-html="true" data-position="auto" class="cursor-help tooltipster" title="{#options_aide_lieu_exclusif#}"><i class="fa fa-question-circle" aria-hidden="true"></i></span>
		<div class="tooltip-html"><span id="tooltip-exclusivite">{#options_aide_lieu_exclusif#}</span></div>
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4"></label>
		<div class="col-md-5">
			<br />
			<input type="button" value="{#enregistrer#|escape:"html"}" class="btn btn-primary" onClick="xajax_submitFormLieu('{$lieu.lieu_id}', $('#lieu_id').val(), $('#nom').val(), $('#commentaire').val(), $('#lieu_exclusif').is(':checked') )" />
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