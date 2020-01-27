{* Smarty *}

{include file="www_header.tpl"}

<div class="container">
	{if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT == 1 && in_array("audit_restore", $user.tabDroits) }
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					<a href="{$BASE}/options.php" class="btn btn-default" ><i class="fa fa-cogs fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuOptions#}</a>
				</div>
				<div class="btn-group">
					<a href="javascript:xajax_purgerAudit();undefined;"	onclick="javascript: return confirm('{#audit_purge_question#|xss_protect}')" class="btn btn-default" ><i class="fa fa-trash fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#audit_purge#}</a>
				</div>
			</div>
		</div>
	</div>
	{/if}
	<form action="audit.php" method="POST" id="filtreAudit">
		<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
			<label class="label-form">{#audit_filtreDate#} : </label>
			{if $smarty.const.CONFIG_SOPLANNING_OPTION_AUDIT == 1 && in_array("audit_restore", $user.tabDroits) }			
			<div class="btn-group ml-4">
						<input type="hidden" name="filtreUserAudit" value="1" />
						<select name="filtreUserAudit" multiple="multiple" id="filtreUserAudit" class="d-none multiselect">
							{if $users|@count eq 0}
								<option>&nbsp;{#formFiltreProjetAucunProjet#}</option>
							{else}
								<optgroup id="e0" label="{#cocheUserSansGroupe#}">
								{assign var=groupeTemp value=""}
								{foreach from=$users item=userCourant}
									{if $userCourant.user_groupe_id neq $groupeTemp}
										</optgroup><optgroup id="e{$userCourant.user_groupe_id}" label="{$userCourant.groupe_nom}">
									{/if}
								<option value="{$userCourant.user_id}" {if in_array($userCourant.user_id, $filtreUserAudit)}selected="selected"{/if}>{$userCourant.nom|xss_protect} ({$userCourant.user_id|xss_protect})</option> 								
								{assign var=groupeTemp value=$userCourant.user_groupe_id}
								{/foreach}
							{/if}
							</optgroup></select>
				</div>					
			{/if}
			
					<div class="btn-group ml-1" id="dropdownTaskProjectFilter">
						<input type="hidden" name="filtreGroupeProjetAudit" value="1" />
						<select name="filtreGroupeProjetAudit" multiple="multiple" id="filtreGroupeProjetAudit" class="d-none multiselect">
							{if $listeProjets|@count eq 0}
								<option>&nbsp;{#formFiltreProjetAucunProjet#}</option>
							{else}
								<optgroup id="g0" label="{#projet_liste_sansGroupes#}">
								{assign var=groupeTemp value=""}
								{foreach from=$listeProjets item=projetCourant name=loopProjets}
									{if $projetCourant.groupe_id neq $groupeTemp}
										</optgroup><optgroup id="g{$projetCourant.groupe_id}" label="{$projetCourant.groupe_nom}">
									{/if}
								<option value="{$projetCourant.projet_id}" {if in_array($projetCourant.projet_id, $filtreGroupeProjetAudit)}selected="selected"{/if}>{$projetCourant.nom|xss_protect} ({$projetCourant.projet_id|xss_protect})</option> 								
								{assign var=groupeTemp value=$projetCourant.groupe_id}
								{/foreach}
							{/if}
							</optgroup></select>
					</div>
					</div>
		</div>	
	</div>	
	</form>
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				{if $audit|@count > 0}
					<table class="table table-striped table-hover" id="traceTab">
						<tr>
							<th class="w100">&nbsp;</th>
							<th class="w100">
								<b>{#audit_date#}</b>
							</th>
							<th>
								<b>{#audit_qui#}</b>
							</th>
							<th>
								<b>{#audit_type#}</b>
							</th>
							<th class="wrap d-none d-lg-table-cell">
								<b>{#audit_date_tache#}</b>
							</th>
							<th class="wrap d-none d-lg-table-cell">
								<b>{#audit_auteur_tache#}</b>
							</th>
							<th class="wrap d-none d-lg-table-cell">
								<b>{#audit_info#}</b>
							</th>
							</tr>
						{foreach name=audits item=audits from=$audit}
							<tr>
								<td class="w100 nowrap text-center">
								{if ($audits.type=="MT" or $audits.type=="DT" or $audits.type=="MP" or $audits.type=="DP" or $audits.type=="MG" or $audits.type=="DG" or $audits.type=="MU" or $audits.type=="DU" or $audits.type=="ME" or $audits.type=="DE" or $audits.type=="ML" or $audits.type=="DL" or $audits.type=="MR" or $audits.type=="DR" or $audits.type=="MS" or $audits.type=="DS") and $audits.nbmodifs > 0}
									<a href="javascript:xajax_modifAudit('{$audits.audit_id}');undefined;"><i class="fa fa-history fa-lg fa-fw" aria-hidden="true"></i></a>
								{/if}
								</td>
								<td>
									{$audits.date_modif}&nbsp;
								</td>
								<td>
									{$audits.modif_nom}&nbsp;
								</td>
								<td class="wrap">
									{if $audits.type=="MT"}{#action_modif_tache#} {$audits.periode_id} : {$audits.informations}{/if}
									{if $audits.type=="AT"}{#action_ajout_tache#} {$audits.periode_id} : {$audits.informations}{/if}
									{if $audits.type=="DT"}{#action_suppression_tache#} {$audits.periode_id} : {$audits.informations}{/if}
									{if $audits.type=="AP"}{#action_ajout_projet#} "{$audits.informations}"{/if}									
									{if $audits.type=="MP"}{#action_modif_projet#} "{$audits.informations}"{/if}
									{if $audits.type=="DP"}{#action_suppression_projet#} "{$audits.informations}"{/if}
									{if $audits.type=="AG"}{#action_ajout_groupe#} "{$audits.informations}"{/if}									
									{if $audits.type=="MG"}{#action_modif_groupe#} "{$audits.informations}"{/if}
									{if $audits.type=="DG"}{#action_suppression_groupe#} "{$audits.informations}"{/if}
									{if $audits.type=="AU"}{#action_ajout_utilisateur#} "{$audits.informations}"{/if}
									{if $audits.type=="MU"}{#action_modif_utilisateur#} "{$audits.informations}"{/if}
									{if $audits.type=="DU"}{#action_suppression_utilisateur#} "{$audits.informations}"{/if}
									{if $audits.type=="AE"}{#action_ajout_equipe#} "{$audits.informations}"{/if}									
									{if $audits.type=="ME"}{#action_modif_equipe#} "{$audits.informations}"{/if}
									{if $audits.type=="DE"}{#action_suppression_equipe#} "{$audits.informations}"{/if}									
									{if $audits.type=="AL"}{#action_ajout_lieu#} "{$audits.informations}"{/if}
									{if $audits.type=="ML"}{#action_modif_lieu#} "{$audits.informations}"{/if}
									{if $audits.type=="DL"}{#action_suppression_lieu#} "{$audits.informations}"{/if}
									{if $audits.type=="AR"}{#action_ajout_ressource#} "{$audits.informations}"{/if}
									{if $audits.type=="MR"}{#action_modif_ressource#} "{$audits.informations}"{/if}
									{if $audits.type=="DR"}{#action_suppression_ressource#} "{$audits.informations}"{/if}
									{if $audits.type=="AS"}{#action_ajout_statut#} "{$audits.informations}"{/if}
									{if $audits.type=="MS"}{#action_modif_statut#} "{$audits.informations}"{/if}
									{if $audits.type=="DS"}{#action_suppression_statut#} "{$audits.informations}"{/if}
									{if $audits.type=="C"}{#action_connexion#}{/if}
									{if $audits.type=="D"}{#action_deconnexion#}{/if}
									&nbsp;
								</td>
								<td class="wrap d-none d-lg-table-cell">
								{$audits.date_debut}
								</td>
								<td class="wrap d-none d-lg-table-cell">
								{$audits.periode_user_nom}
								</td>								
								<td class="wrap d-none d-lg-table-cell">
									{if $audits.type=="MT"}{#nb_modifications#} : {$audits.nbmodifs}{/if}
									{if $audits.type=="MP"}{#nb_modifications#} : {$audits.nbmodifs}{/if}
									{if $audits.type=="MU"}{#nb_modifications#} : {$audits.nbmodifs}{/if}
									{if $audits.type=="ML"}{#nb_modifications#} : {$audits.nbmodifs}{/if}
									{if $audits.type=="MR"}{#nb_modifications#} : {$audits.nbmodifs}{/if}
									{if $audits.type=="MG"}{#nb_modifications#} : {$audits.nbmodifs}{/if}
									{if $audits.type=="ME"}{#nb_modifications#} : {$audits.nbmodifs}{/if}
									{if $audits.type=="MS"}{#nb_modifications#} : {$audits.nbmodifs}{/if}
									
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
<script>
{literal}
$("#filtreUserAudit").multiselect({
	selectAll:false,
	noUpdatePlaceholderText:true,
	nameSuffix: 'user',
	desactivateUrl: 'audit.php?desactiverFiltreUserAudit=1',
	placeholder: '{/literal}<i class="fa fa-user fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;{#formChoixUser#}</span>{literal}',
	texts: {
       selectAll    : '{/literal}{#formFiltreProjetCocherTous#}{literal}',
       unselectAll    : '{/literal}{#formFiltreProjetDecocherTous#}{literal}',
	   disableFilter : '{/literal}{#formFiltreProjetDesactiver#}{literal}',
	   validateFilter : '{/literal}{#submit#}{literal}',
	   search : '{/literal}{#search#}{literal}'
	},
});
$("#filtreUserAudit").show();
$("#filtreGroupeProjetAudit").multiselect({
	selectAll:false,
	noUpdatePlaceholderText:true,
	nameSuffix: 'projet',
	desactivateUrl: 'audit.php?desactiverFiltreGroupeProjetAudit=1',
	placeholder: '{/literal}<i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;{#taches_filtreProjets#}</span>{literal}',
	texts: {
       selectAll    : '{/literal}{#formFiltreProjetCocherTous#}{literal}',
       unselectAll    : '{/literal}{#formFiltreProjetDecocherTous#}{literal}',
	   disableFilter : '{/literal}{#formFiltreProjetDesactiver#}{literal}',
	   validateFilter : '{/literal}{#submit#}{literal}',
	   search : '{/literal}{#search#}{literal}'
	},
});
$("#filtreGroupeProjetAudit").show();
{/literal}
</script>
{include file="www_footer.tpl"}