{* Smarty *}

{include file="www_header.tpl"}

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					<a href="{$BASE}/options.php" class="btn btn-default" ><i class="fa fa-cogs fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuOptions#}</a>
					<a href="javascript:xajax_modifStatus();undefined;" class="btn btn-default" ><i class="fa fa-tags fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuCreerStatus#}</a>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				{if $status|@count > 0}
					<table class="table table-striped table-hover" id="locationTab">
						<tr>
							<th class="w100">&nbsp;</th>
							<th>
								<b>{#status_nom#}</b>
							</th>
							<th class="d-none d-md-block d-lg-block">
								<b>{#status_commentaire#}</b>
							</th>
							<th class="text-center d-none d-sm-table-cell d-md-table-cell d-lg-table-cell">
								<b>{#status_pourcentage#}</b>
							</th>
							<th class="text-center">
								<b>{#status_couleur#}</b>
							</th>
						</tr>
						{foreach name=status item=statusTmp from=$status}
							<tr>
								<td class="w100">
									<a href="javascript:xajax_modifStatus('{$statusTmp.status_id}');undefined;"><i class="fa fa-pencil fa-lg fa-fw" aria-hidden="true"></i></a>
									<a href="javascript:xajax_supprimerStatus('{$statusTmp.status_id}');undefined;" onClick="javascript:return confirm('{#confirm#|escape:"javascript"}')"><i class="fa fa-trash-o fa-lg fa-fw" aria-hidden="true"></i></a>
									<a href="{$BASE}/process/planning.php?filtreSurStatus={$statusTmp.status_id}" title="{#planning_filtre_sur_status#|escape}"><i class="fa fa-globe fa-lg fa-fw" aria-hidden="true"></i></a>
								</td>
								<td>
									{$statusTmp.nom}&nbsp;
								</td>
								<td class="d-none d-md-block d-lg-block">
									{$statusTmp.commentaire}
								</td>
								<td class="text-center d-none d-sm-table-cell d-md-table-cell d-lg-table-cell">
									{$statusTmp.pourcentage}
								</td>
								<td>
									<div class="pastille-statut mr-auto ml-auto" style="background-color:#{$statusTmp.couleur}"></div>
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