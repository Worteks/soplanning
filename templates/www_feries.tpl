{* Smarty *}
{include file="www_header.tpl"}
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					<a href="{$BASE}/options.php" class="btn btn-default" ><i class="fa fa-cogs fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuOptions#}</a>
					<a href="javascript:xajax_modifFerie();undefined;" class="btn btn-default" ><i class="fa fa-plane fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuCreerFerie#}</a>
					<div class="btn-group" id="dropdownExport">
						<button class="btn dropdown-toggle btn-default" data-toggle="dropdown" data-display="static"><i class="fa fa-upload fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;&nbsp;{#feries_import#}</span>&nbsp;<span class="caret"></span></button>
						<div class="dropdown-menu" style="">
							{foreach from=$fichiers item=fichier}
								<a class="dropdown-item" onClick="event.cancelBubble=true;" href="javascript:if(confirm('{#feries_confirmImport#}')){literal}{{/literal}document.location='process/feries.php?fichier={$fichier|basename}'{literal}}{/literal}">{$fichier|basename}</a>
							{/foreach}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				{if $feries|@count > 0}
					<table class="table table-striped table-hover">
						<thead>
						<tr>
							<th class="w100">&nbsp;</th>
							<th class="w100">
								<b>{#feries_date#}</b>
							</th>
							<th>
								<b>{#feries_libelle#}</b>
							</th>
							<th class="text-center">
								<b>{#feries_couleurfond#}</b>
							</th>
						</tr>
						</thead>
						<tbody>
						{foreach name=feries item=ferie from=$feries}
							<tr>
								<td class="w100">
									<a href="javascript:xajax_modifFerie('{$ferie.date_ferie|urlencode}');undefined;"><i class="fa fa-pencil fa-lg fa-fw" aria-hidden="true"></i></a>
									<a href="javascript:xajax_supprimerFerie('{$ferie.date_ferie|urlencode}');undefined;" onClick="javascript:return confirm('{#confirm#|escape:"javascript"}')"><i class="fa fa-trash-o fa-lg fa-fw" aria-hidden="true"></i></a>
								</td>
								<td class="w100">
									{$ferie.date_ferie|sqldate2userdate}&nbsp;
								</td>
								<td>
									{$ferie.libelle}
								</td>
								<td>
									{if $ferie.couleur eq ''}
									<div class="pastille-statut mr-auto ml-auto feries"></div>
									{else}
									<div class="pastille-statut mr-auto ml-auto" style="background-color:#{$ferie.couleur}"></div>
									{/if}
								</td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				{else}
					{#info_noRecord#}
				{/if}
			</div>
		</div>
	</div>
</div>
{include file="www_footer.tpl"}