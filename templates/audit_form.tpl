{* Smarty *}
<form class="form-horizontal" method="POST" action="" target="_blank">
	<input type="hidden" name="old_audit_id" id="old_audit_id" value="{$audit.audit_id}" />
	<div class="form-group row col-md-12">
		<label for="audit_nom" class="col-md-3 col-form-label">{#audit_qui#} :</label>
		<div class="col-md-5">
		<p class="form-control-static">{$audit.user_modif}</p>
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label for="audit_date" class="col-md-3 col-form-label">{#audit_date#} :</label>
		<div class="col-md-5">
		<p class="form-control-static">{$audit.date_modif}</p>
		</div>
	</div>
	<div class="form-group row col-md-12">
		<label for="audit_type" class="col-md-3 col-form-label">{#audit_type#} :</label>
		<div class="col-md-7">
			<p class="form-control-static">
			{if $audit.type=="MT"}{#action_modif_tache#} "{$audit.periode_id}" : {$audit.informations}{/if}
			{if $audit.type=="AT"}{#action_ajout_tache#} "{$audit.periode_id}" : {$audit.informations}{/if}
			{if $audit.type=="DT"}{#action_suppression_tache#} "{$audit.periode_id}"{/if}
			{if $audit.type=="AP"}{#action_ajout_projet#}"{$audit.informations}"{/if}		
			{if $audit.type=="MP"}{#action_modif_projet#} "{$audit.informations}"{/if}
			{if $audit.type=="DP"}{#action_suppression_projet#} "{$audit.informations}"{/if}
			{if $audit.type=="AG"}{#action_ajout_groupe#}"{$audit.informations}"{/if}
			{if $audit.type=="MG"}{#action_modif_groupe#} "{$audit.informations}"{/if}
			{if $audit.type=="DG"}{#action_suppression_groupe#} "{$audit.informations}"{/if}
			{if $audit.type=="AU"}{#action_ajout_utilisateur#} "{$audit.informations}"{/if}
			{if $audit.type=="MU"}{#action_modif_utilisateur#} "{$audit.informations}"{/if}
			{if $audit.type=="DU"}{#action_suppression_utilisateur#} "{$audit.informations}"{/if}
			{if $audit.type=="AE"}{#action_ajout_equipe#}"{$audit.informations}"{/if}
			{if $audit.type=="ME"}{#action_modif_equipe#} "{$audit.informations}"{/if}
			{if $audit.type=="DE"}{#action_suppression_equipe#}"{$audit.informations}"{/if}				
			{if $audit.type=="AL"}{#action_ajout_lieu#} "{$audit.informations}"{/if}
			{if $audit.type=="ML"}{#action_modif_lieu#} "{$audit.informations}"{/if}
			{if $audit.type=="DL"}{#action_suppression_lieu#} "{$audit.informations}"{/if}
			{if $audit.type=="AR"}{#action_ajout_ressource#} "{$audit.informations}"{/if}
			{if $audit.type=="MR"}{#action_modif_ressource#} "{$audit.informations}"{/if}
			{if $audit.type=="DR"}{#action_suppression_ressource#} "{$audit.informations}"{/if}
			{if $audit.type=="AS"}{#action_ajout_statut#} "{$audit.informations}"{/if}
			{if $audit.type=="MS"}{#action_modif_statut#} "{$audit.informations}"{/if}
			{if $audit.type=="DS"}{#action_suppression_statut#} "{$audit.informations}"{/if}
			{if $audit.type=="C"}{#action_connexion#}{/if}
			{if $audit.type=="D"}{#action_deconnexion#}{/if}
			</p>
		</div>
	</div>	
	
	<div class="form-group row col-md-12">
		<label for="audit_commentaire" class="col-md-8 col-form-label">{#audit_modifications#} :</label>
		<div class="col-md-7">
			<table id="tab_audit_valeurs">
				<tr><th style="width:20%">{#audit_champ#}</th><th style="width:40%;min-width:150px;">{#audit_avant#}</th><th style="width:40%;min-width:150px;">{#audit_apres#}</th></tr>
				 {foreach from=$valeurs item=label key=key}
					<tr><td><b>{if isset($traductions.$key)}{$traductions.$key}{else}{$key}{/if}</b></td><td>{$label.old}</td><td >{$label.new}</td></tr>
				 {/foreach}
			</table>
		</div>
	</div>
	{if (in_array("audit_restore", $user.tabDroits) || in_array("audit_restore_own", $user.tabDroits))}
	<div class="form-group row col-md-12">
		<div class="col-md-4"></div>
		<div class="col-md-5">
			<br />
			<a href="javascript:xajax_restaureAudit('{$audit.audit_id}');undefined;"	onclick="javascript: return confirm('{#audit_restaurer_question#|xss_protect}')" class="btn btn-primary" ><i class="fa fa-history fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#audit_restaurer_modifications#}</a>
		</div>
	</div>
	{/if}
</form>