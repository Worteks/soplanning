{* Smarty *}
{include file="www_header.tpl" inc='select2'}
<div class="container">
	<form action="stats_roi_projects.php" method="POST" id="formROI">
	<input type="hidden" name="go" value="0">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				<div class="titrePage text-center">
					{#droits_stats_roi_projects#}
				</div>
				<br>
				<div class="form-group row col-md-12">
					<label class="col-md-2 col-form-label">
						{#projet_liste_filtreProjets#}:
					</label>
					<div class="col-md-10">
						{foreach from=$listeStatus item=status}
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name="statut[]" id="{$status.status_id}" value="{$status.status_id}" onclick="javascript:$('#formROI').submit();" {if in_array($status.status_id, $statutsChoisis)}checked="checked"{/if}>
								<label class="form-check-label" for="{$status.status_id}">{$status.nom}</label>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
	</div>
	</form>

	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
			<table class="table table-striped table-hover">
					<tr>
						<th>{#winProjet_nomProjet#}</th>
						<th>{#projet_budget_montant#}</th>
						<th>{#projet_budget_consomme#}</th>
						<th>{#projet_budget_restant#}</th>
						<th>{#projet_budget_pourcent#}</th>
						<th>{#projet_budget_temps#}</th>
						<th>{#projet_temps_consomme#}</th>
						<th>{#projet_temps_restant#}</th>
						<th>{#projet_temps_pourcent#}</th>
					</tr>
					{assign var=groupeCourant value=""}
					{foreach from=$projets item=projet}
						{if $projet.groupe_id neq $groupeCourant}
							<tr>
							<td colspan="9" class="project-group-head">{$projet.nom_groupe|xss_protect}</td>
						{/if}
						<tr>
							<td onClick="xajax_projet_couts_form('{$projet.projet_id}');" style="cursor:pointer">
								<span class="pastille-projet" style="background-color:#{$projet.couleur};color:{"#"|cat:$projet.couleur|buttonFontColor}">{$projet.projet_id}</span>
								{$projet.nom|xss_protect}
							</td>
							<td class="projectTabColTotaux colProj1">
								{if $projet.budget_montant > 0}
									{$projet.budget_montant|formaterNombreDecimal} €
								{else}
									-----
								{/if}
							</td>
							<td class="projectTabColTotaux colProj1">
								{$projet.montant_consomme|formaterNombreDecimal} €
							</td>
							<td class="projectTabColTotaux colProj1">
								{if $projet.budget_montant > 0}
									{$projet.montant_restant|formaterNombreDecimal} €
								{else}
									-----
								{/if}
							</td>
							<td class="projectTabColTotaux colProj1">
								{if $projet.budget_montant > 0}
									{assign var=montant_pourcent value=$projet.montant_restant*100/$projet.budget_montant}
									<span style="color:{if $montant_pourcent < 0}#ff0000{/if}">
										{$montant_pourcent|formaterNombreDecimal} %
									</span>
								{else}
									-----
								{/if}
							</td>
							<td class="projectTabColTotaux colProj2">
								{if $projet.budget_temps > 0}
									{$projet.budget_temps|formaterNombreDecimal} h
								{else}
									-----
								{/if}
							</td>
							<td class="projectTabColTotaux colProj2">
								{$projet.temps_consomme|formaterNombreDecimal} h
							</td>
							<td class="projectTabColTotaux colProj2">
								{if $projet.budget_temps > 0}
									{$projet.temps_restant|formaterNombreDecimal} h
								{else}
									-----
								{/if}
							</td>
							<td class="projectTabColTotaux colProj2">
								{if $projet.budget_temps > 0}
									{assign var=temps_pourcent value=$projet.temps_restant*100/$projet.budget_temps}
									<span style="color:{if $temps_pourcent < 0}#ff0000{/if}">
										{$temps_pourcent|formaterNombreDecimal} %
									</span>
								{else}
									-----
								{/if}
							</td>
						</tr>
						{assign var=groupeCourant value=$projet.groupe_id}
					{/foreach}
				</table>
			</div>
		</div>
	</div>
</div>


{include file="www_footer.tpl"}