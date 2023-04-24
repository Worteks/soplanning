{* Smarty *}
<form class="form-horizontal" method="POST" target="_blank" id="periodForm">
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#periode_scinder_dates#}</label>
		<div class="col-md-5">
			{$periode.date_debut|sqldate2userdate} - {$periode.date_fin|sqldate2userdate}
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label">{#periode_scinder_texte#}</label>
		<div class="col-md-3">
			{if $smarty.session.isMobileOrTablet==1}
				<input type="date" class="form-control" name="date_scinder" id="date_scinder" maxlength="10" value="{$periode.date_debut|forceISODateFormat}" tabindex="4" />
			{else}
				<input type="text" class="form-control datepicker" name="date_scinder" id="date_scinder" maxlength="10" value="{$periode.date_debut|sqldate2userdate}" tabindex="4" />
			{/if}
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label class="col-md-4 col-form-label"></label>
		<div class="col-md-3">
			<button type="button" id="butSubmitPeriode" class="btn btn-primary" tabindex="24" onClick="this.disabled=true;xajax_periode_scinder_submit('{$periode.periode_id}', $('#date_scinder').val());">{#winPeriode_valider#|xss_protect}</button>
		</div>
	</div>
</form>

