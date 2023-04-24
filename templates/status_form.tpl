{* Smarty *}
<form method="POST" action="" target="_blank">
	<input type="hidden" name="old_status_id" id="old_status_id" value="{$status.status_id}" />
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#status_identifiant#} :</label>
		<div class="col-md-4">
			{if $status.status_id neq ''}
			<input name="status_id" id="status_id" type="text" readonly class="form-control-plaintext" value="{$status.status_id}">
			{else}
			<input name="status_id" id="status_id" type="text" maxlength="10" class="form-control" value="{$status.status_id}"  onChange="xajax_checkStatusId(this.value, '{$status.status_id}');" /> 
			</div>
			<div class="col-md-3">
			<span id="divStatutCheckStatusId"></span>
			{#winPeriode_status_identifiantCarMax#}
			{/if}
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#status_nom#} :</label>
		<div class="col-md-5">
			<input name="nom" id="nom" class="form-control" type="text" maxlength="50" value="{$status.nom}" />
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#status_commentaire#} :</label>
		<div class="col-md-7">
			<textarea name="commentaire" id="commentaire" class="form-control" maxlength="255">{$status.commentaire}</textarea>
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#options_statusAffichage_affichage#} :</label>
		<div class="col-md-6">
			<select name='affichage' id='affichage' class="form-control">
				<option value="tp" {if $status.affichage == 'tp'}selected="selected"{/if}>{#options_statusAffichage_tachesprojets#}</option>
				<option value="p" {if $status.affichage == 'p'}selected="selected"{/if}>{#options_statusAffichage_projets#}</option>
				<option value="t" {if $status.affichage == 't'}selected="selected"{/if}>{#options_statusAffichage_taches#}</option>
			</select>
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#status_affichage_liste#} :</label>
		<div class="col-md-3">
			<select name="affichage_liste" id="affichage_liste" class="form-control">
				<option value="0" {if $status.affichage_liste eq 0}selected="selected"{/if}>{#non#}</option>
				<option value="1" {if $status.affichage_liste eq 1}selected="selected"{/if}>{#oui#}</option>
			</select>
		</div>
			<div title="{#options_aide_status_affichage_liste#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#status_pardefaut#} :</label>
		<div class="col-md-3">
			<select name="defaut" id="defaut" class="form-control">
				<option value="0" {if $status.defaut eq 0}selected="selected"{/if}>{#non#}</option>
				<option value="1" {if $status.defaut eq 1}selected="selected"{/if}>{#oui#}</option>
			</select>
		</div>
			<div title="{#options_aide_status_defaut#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#status_couleur#} :</label>
		<div class="col-md-3">
		{if $smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE neq ""}
				{if $smarty.session.isMobileOrTablet==1}
					<input class="form-control color-input" name="couleur" id="couleur" maxlength="6" type="color" list="colors" value="#{if $status.couleur eq ''}{$couleurExStatus}{else}{$status.couleur}{/if}" />
						<datalist id="colors">
							{foreach from=","|explode:$smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE item=couleurTmp}
								<option>{$couleurTmp}</option>
							{/foreach}
						</datalist>
				{else}
				<select name="couleur2" id="couleur2" class="form-control" style="background-color:#{$status.couleur};color:{'#'|cat:$status.couleur|buttonFontColor}">
				    {if $status.couleur neq ""}<option value="{$status.couleur}" style="background-color:#{$status.couleur};color:{'#'|cat:$status.couleur|buttonFontColor}" selected="selected">{$status.couleur}</option>{else}<option value="">{#status_couleurchoix#}</option>{/if}
					{foreach from=","|explode:$smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE item=couleurTmp}
						<option value="{$couleurTmp|replace:'#':''}" style="background-color:{$couleurTmp};color:{$couleurTmp|buttonFontColor}" {if $couleurTmp eq "#"|cat:$status.couleur}selected="selected"{/if}>{$couleurTmp|replace:'#':''}</option>
					{/foreach}
				</select>
				{/if}
			{else}
                {if $smarty.session.couleurExStatus neq ""}
                    {assign var=couleurExStatus value=$smarty.session.couleurExStatus}
                {else}
                    {assign var=couleurExStatus value="ffffff"}
                {/if}
				<input name="couleur" id="couleur" maxlength="6" data-huebee {if $smarty.session.isMobileOrTablet==1}type="color"{else}type="text"{/if} value="#{if $status.couleur eq ''}{$couleurExStatus}{else}{$status.couleur}{/if}" />
			{/if}
		</div>
			<div title="{#options_aide_couleur_statut#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
	</div>	
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#status_format_texte#} : </label>
		<div class="col-md-5">
			<div class="input-group">
			<button type="button" id="button_bold" class="btn {if $status.gras eq 1}btn-info{else}btn-default{/if}" aria-label="Bold" onclick="boutonStyleStatut(this.id);">
				<i class="fa fa-bold" aria-hidden="true"></i>
			</button>
			<button type="button" id="button_italic" class="btn {if $status.italique eq 1}btn-info{else}btn-default{/if}" aria-label="Italic" onclick="boutonStyleStatut(this.id);">
				<i class="fa fa-italic" aria-hidden="true"></i>
			</button>
			<button type="button" id="button_underline" class="btn {if $status.souligne eq 1}btn-info{else}btn-default{/if}" aria-label="Underline" onclick="boutonStyleStatut(this.id);">
				<i class="fa fa-underline" aria-hidden="true"></i>
			</button>
			<button type="button" id="button_strikethrough" class="btn {if $status.barre eq 1}btn-info{else}btn-default{/if}" aria-label="Strikethrough" onclick="boutonStyleStatut(this.id);">
				<i class="fa fa-strikethrough" aria-hidden="true"></i>
			</button>
			</div>		
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#status_pourcentage#} :</label>
		<div class="col-md-3">
			<select name='pourcentage' id='pourcentage' class="form-control">
				<option value="0" {if $status.pourcentage == 0}selected="selected"{/if}>0</option>
				<option value="10" {if $status.pourcentage == 10}selected="selected"{/if}>10</option>
				<option value="20" {if $status.pourcentage == 20}selected="selected"{/if}>20</option>
				<option value="30" {if $status.pourcentage == 30}selected="selected"{/if}>30</option>
				<option value="40" {if $status.pourcentage == 40}selected="selected"{/if}>40</option>
				<option value="50" {if $status.pourcentage == 50}selected="selected"{/if}>50</option>
				<option value="60" {if $status.pourcentage == 60}selected="selected"{/if}>60</option>
				<option value="70" {if $status.pourcentage == 70}selected="selected"{/if}>70</option>
				<option value="80" {if $status.pourcentage == 80}selected="selected"{/if}>80</option>
				<option value="90" {if $status.pourcentage == 90}selected="selected"{/if}>90</option>
				<option value="100" {if $status.pourcentage == 100}selected="selected"{/if}>100</option>
			</select>
		</div>	
			<div title="{#options_aide_pourcentage_statut#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5 col-form-label">{#status_priorite#} :</label>
		<div class="col-md-2">
			<select name='priorite' id='priorite' class="form-control">
				<option value="1" {if $status.priorite == 1}selected="selected"{/if}>1</option>
				<option value="2" {if $status.priorite == 2}selected="selected"{/if}>2</option>
				<option value="3" {if $status.priorite == 3}selected="selected"{/if}>3</option>
				<option value="4" {if $status.priorite == 4}selected="selected"{/if}>4</option>
				<option value="5" {if $status.priorite == 5}selected="selected"{/if}>5</option>
				<option value="6" {if $status.priorite == 6}selected="selected"{/if}>6</option>
				<option value="7" {if $status.priorite == 7}selected="selected"{/if}>7</option>
				<option value="8" {if $status.priorite == 8}selected="selected"{/if}>8</option>
				<option value="9" {if $status.priorite == 9}selected="selected"{/if}>9</option>
				<option value="10" {if $status.priorite == 10}selected="selected"{/if}>10</option>
			</select>
		</div>	
			<div title="{#options_aide_priorite_statut#}" class="cursor-help tooltipster"><i class="fa fa-question-circle" aria-hidden="true"></i></div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-5"></label>
		<div class="col-md-5">
			<br />
			<input type="button" value="{#enregistrer#|escape:"html"}" class="btn btn-primary" onClick="xajax_submitFormStatus('{$status.status_id}', $('#status_id').val(), $('#nom').val(), $('#commentaire').val(), $('#affichage option:selected').val(), $('#button_strikethrough').hasClass('btn-info'),$('#button_bold').hasClass('btn-info'),$('#button_italic').hasClass('btn-info'),$('#button_underline').hasClass('btn-info'), $('#defaut option:selected').val(), $('#affichage_liste option:selected').val(), $('#pourcentage option:selected').val(), {if $smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE neq ""}$('#couleur2 option:selected').val(){else}$('#couleur').val(){/if}, $('#priorite option:selected').val())" />
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

function boutonStyleStatut(clic) {
	$('#' + clic).toggleClass('btn-info');
	$('#' + clic).toggleClass('btn-default');
}
</script>