{* Smarty *}
<form method="POST" action="" target="_blank">
	<input type="hidden" name="old_ressource_id" id="old_ressource_id" value="{$ressource.ressource_id}" />
	<div class="form-group row col-md-12">
		<label for="ressource_id" class="col-md-4 col-form-label">{#ressource_identifiant#} :</label>
		<div class="col-md-4">
			{if $ressource.ressource_id neq ''}
			<input name="ressource_id" id="ressource_id" type="text" readonly class="form-control-plaintext" value="{$ressource.ressource_id}"> 			
			{else}
			<input name="ressource_id" id="ressource_id" type="text" class="form-control" maxlength="20" value="{$ressource.ressource_id}" onChange="xajax_checkRessourceId(this.value, '{$ressource.ressource_id}');" />
			</div>
			<span id="divStatutCheckRessourceId"></span>
			<div class="col-md-3">{#winPeriode_ressource_identifiantCarMax#}
			{/if}
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label for="ressource_nom" class="col-md-4 col-form-label">{#ressource_nom#} :</label>
		<div class="col-md-5">
			<input name="ressource_nom" id="ressource_nom" type="text" class="form-control" maxlength="50" value="{$ressource.nom|xss_protect}" />
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label for="ressource_commentaire" class="col-md-4 col-form-label">{#ressource_commentaire#} :</label>
		<div class="col-md-7">
			<textarea name="ressource_commentaire" id="ressource_commentaire" class="form-control" maxlength="255" type="text">{$ressource.commentaire|xss_protect}</textarea>
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#exclusivite#} :</label>
		<div class="col-md-7 form-check form-check-inline">
		&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" class="form-check-input" name="ressource_exclusif" id="ressource_exclusif" {if $ressource.exclusif == 1}checked="checked"{/if}><label class="form-check-label" for="ressource_exclusif">{#ressource_exclusive#}</label>
		&nbsp;&nbsp;<span data-tooltip-content="#tooltip-exclusivite" data-toggle="tooltip" data-html="true" data-position="auto" class="cursor-help tooltipster" title="{#options_aide_ressource_exclusive#}"><i class="fa fa-question-circle" aria-hidden="true" class="small"></i></span>
		<div class="tooltip-html"><span id="tooltip-exclusivite">{#options_aide_ressource_exclusive#}</span></div>
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<div class="col-md-4"></div>
		<div class="col-md-5">
			<br />
			<input type="button" value="{#enregistrer#|escape:"html"}" class="btn btn-primary" onClick="xajax_submitFormRessource('{$ressource.ressource_id}', $('#ressource_id').val(), $('#ressource_nom').val(), $('#ressource_commentaire').val(), $('#ressource_exclusif').is(':checked'))" />
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