{* Smarty *}
<form method="post" action="" target="_blank" name="formUser"  onSubmit="return false;">
	<div class="row">
		<label class="col-md-4 col-form-label">{#action_multi_concernees#} ({$taches|@count}) :</label>
		<div class="col-md-8 col-form-label">
				{foreach from=$taches item=tache}
					{$tache.date_debut|sqldate2userdate} -
				{/foreach}
				<br><br>
		</div>
	</div>	
	<div class="form-group row">
		<div class="col-md-12">
			<div align="center">
				<input type="button" class="btn btn-warning" value="{#action_multi_supprimer#}" onClick="if(confirm('{#winPeriode_confirmSuppr#|xss_protect}'))$('#divPatienter').removeClass('d-none');this.disabled=true;xajax_selection_multi_tache_suppr('{$chaineTaches}');"/>
				<input type="button" class="btn btn-default" value="{#action_multi_annuler_selection#}" onClick="annulerSelectionTaches();$('#myModal').modal('hide');"/>
				<input type="button" class="btn btn-default" value="{#action_multi_annuler_fermer#}" onClick="$('#myModal').modal('hide');"/>
				<div id="divPatienter" class="d-none justify-content-end form-group" style="display:inline-block;margin-left:20px"><img src="assets/img/pictos/loading16.gif" alt="" /></div>
			</div>
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