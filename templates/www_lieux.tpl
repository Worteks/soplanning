{* Smarty *}

{include file="www_header.tpl"}

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					<a href="{$BASE}/options.php" class="btn btn-default" ><i class="fa fa-cogs fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuOptions#}</a>
					<a href="javascript:xajax_modifLieu();undefined;" class="btn btn-default" ><i class="fa fa-map-marker fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuCreerLieu#}</a>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				{if $lieux|@count > 0}
					<table class="table table-striped table-hover" id="locationTab">
						<tr>
							<th class="w100">&nbsp;</th>
							<th>
								<b>{#lieu_nom#}</b>
							</th>
							<th class="d-none d-md-table-cell d-lg-table-cell">
								<b>{#lieu_commentaire#}</b>
							</th>
							<th class="text-center d-none d-sm-table-cell d-md-table-cell d-lg-table-cell">
								<b>{#exclusivite#}</b>
							</th>
						</tr>
						{foreach name=lieux item=lieu from=$lieux}
							<tr>
								<td class="w100">
									<a href="javascript:xajax_modifLieu('{$lieu.lieu_id|urlencode}');undefined;"><i class="fa fa-pencil fa-lg fa-fw" aria-hidden="true"></i></a>
									<a href="javascript:xajax_supprimerLieu('{$lieu.lieu_id|urlencode}');undefined;" onClick="javascript:return confirm('{#confirm#|escape:"javascript"}')"><i class="fa fa-trash-o fa-lg fa-fw" aria-hidden="true"></i></a>
									<a href="{$BASE}/process/planning.php?filtreSurLieu={$lieu.lieu_id}" title="{#planning_filtre_sur_lieu#|escape}"><i class="fa fa-globe fa-lg fa-fw" aria-hidden="true"></i></a>
								</td>
								<td class="wrap">
									{$lieu.nom|xss_protect}&nbsp;
								</td>
								<td class="wrap d-none d-md-table-cell d-lg-table-cell">
									{$lieu.commentaire|xss_protect}
								</td>
								<td class="text-center d-none d-sm-table-cell d-md-table-cell d-lg-table-cell">
									{if $lieu.exclusif eq 1}{#oui#}{else}{#non#}{/if}
								</td>
							</tr>
						{/foreach}
					</table>
				{else}
					{#info_noRecord#}
				{/if}
			</div>
		</div>
	</div>
</div>

{include file="www_footer.tpl"}