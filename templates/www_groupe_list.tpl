{* Smarty *}
{include file="www_header.tpl"}

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					<a href="{$BASE}/projets.php" class="btn btn-default" ><i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp; {#menuListeProjets#}</a>
					<a href="{$BASE}/groupe_form.php" class="btn btn-default"><i class="fa fa-bookmark-o fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp; {#menuCreerGroupe#}</a>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				<form method="GET" id="filtreprojet">
				<label class="col-form-label nowrap">{#projet_liste_afficherGroupesProjets#} :&nbsp;</label>
				<div class="form-group">
					{foreach from=$listeStatus item=status}
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="statut[]" id="{$status.status_id}" value="{$status.status_id}" onclick="javascript:$('#filtreprojet').submit();" {if in_array($status.status_id, $listeStatuts)}checked="checked"{/if}>
						<label class="form-check-label" for="{$status.status_id}">{$status.nom}</label>
					</div>
					{/foreach}

					&nbsp;	
					<div class="btn-group">
						<div class="input-group">
							<input type="text" class="form-control input-sm" name="rechercheProjet" value="{$rechercheProjet|xss_protect|default:""}" />
							<div class="input-group-append">
								<button type="submit" class="btn btn-sm {if $rechercheProjet != ""}btn-danger{else}btn-default{/if}"><i class="fa fa-search fa-lg fa-fw" aria-hidden="true"></i></button>
							</div>
						</div>
					</div>
				  </div>
				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				{if $groupes|@count > 0}
					<table class="table table-striped table-hover" id="ProjectListTab">
						<tr>
							<th>&nbsp;</th>
							<th>
								{if $order eq "nom"}
									{if $by eq "asc"}
										<a href="{$BASE}/groupe_list.php?page=1&order=nom&by=desc">{#groupe_liste_nom#} ({$groupes|@count})</a>&nbsp;<img src="{$BASE}/assets/img/pictos/asc_order.png" alt="" />
									{else}
										<a href="{$BASE}/groupe_list.php?page=1&order=nom&by=asc">{#groupe_liste_nom#} ({$groupes|@count})</a>&nbsp;<img src="{$BASE}/assets/img/pictos/desc_order.png" alt="" />
									{/if}
								{else}
									<a href="{$BASE}/groupe_list.php?page=1&order=nom&by={$by}">{#groupe_liste_nom#} ({$groupes|@count})</a>
								{/if}
							</th>
							{assign var=totalProjets value=0}
							{foreach name=groupes item=groupe from=$groupes}
								{assign var=totalProjets value=$totalProjets+$groupe.totalProjets}
							{/foreach}
							<th>{#groupe_liste_nbProjets#} ({$totalProjets})</th>
						</tr>
						{foreach name=groupes item=groupe from=$groupes}
							{assign var=couleurLigne value="#ffffff"}
							<tr>
								<td>
									<a href="{$BASE}/groupe_form.php?groupe_id={$groupe.groupe_id}"><i class="fa fa-pencil fa-lg fa-fw" aria-hidden="true"></i></a>
									<a href="{$BASE}/process/groupe_save.php?groupe_id={$groupe.groupe_id}&action=delete" onClick="javascript:return confirm('{#groupe_liste_confirmSuppr#|escape:"javascript"}')"><i class="fa fa-trash-o fa-lg fa-fw" aria-hidden="true"></i></a>
								</td>
								<td>{$groupe.nom|xss_protect}&nbsp;</td>
								<td>{$groupe.totalProjets}&nbsp;</td>
							</tr>
						{/foreach}
						{if $nbPages > 1}
							<tr>
								<td colspan="7" align="right" style="white-space:normal">
									{if $currentPage > 1}<a href="{$BASE}/groupe_list.php?page={$currentPage-1}">&lt;&lt; {#action_precedent#}</a>&nbsp;&nbsp;{/if}
									{section name=pagination loop=$nbPages}
										{if $smarty.section.pagination.iteration == $currentPage}<b>{else}<a href="{$BASE}/groupe_list.php?page={$smarty.section.pagination.iteration}">{/if}
										{$smarty.section.pagination.iteration}
										{if $smarty.section.pagination.iteration == $currentPage}</b>{else}</a>{/if}&nbsp;
									{/section}
									{if $currentPage < $nbPages}<a href="{$BASE}/groupe_list.php?page={$currentPage+1}">{#action_suivant#} &gt;&gt;</a>{/if}
								</td>
							</tr>
						{/if}
					</table>
				{else}
					{#info_noRecord#}
				{/if}
			</div>
		</div>
	</div>
</div>
{* CHARGEMENT SCROLL Y *}
<script>
	{literal}
	var yscroll = getCookie('yposProjets');
	window.onscroll = function() {document.cookie='yposProjets=' + window.pageYOffset;};
	addEvent(window, 'load', chargerYScrollPos);
	{/literal}
</script>
{include file="www_footer.tpl"}