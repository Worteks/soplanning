{* Smarty *}
<form method="post" action="" target="_blank" onsubmit="return false;">
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#feries_date#} :</label>
		<div class="col-md-7">
			{if $smarty.session.isMobileOrTablet==1}
				<input type="date" class="form-control" id="date_ferie" maxlength="10" value="{$ferie.date_ferie|forceISODateFormat}" />		
			{else}
				<input type="text" class="form-control datepicker" id="date_ferie" maxlength="10" value="{$ferie.date_ferie|sqldate2userdate}" />		
			{/if}
			
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#feries_libelle#} :</label>
		<div class="col-md-6">
			<input id="libelle" maxlength="50" type="text" value="{$ferie.libelle}" class="form-control" />
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<label class="col-md-4 col-form-label">{#feries_couleurfond#} :</label>
		<div class="col-md-6">
			<div class="form-check">
				<input class="form-check-input" type="radio" name="couleur_fond" id="couleur_fond_defaut" value="defaut" onChange="{literal}if(this.checked){document.getElementById('divSpecificColor').style.display='none';}{/literal}" {if $ferie.couleur eq ''}checked="checked"{/if}>
				<label class="form-check-label" for="couleur_fond_defaut">{#feries_couleurtheme#}</label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="couleur_fond" id="couleur_fond_perso" value="perso" onChange="{literal}if(this.checked){document.getElementById('divSpecificColor').style.display='inline-block';}{/literal}"{if $ferie.couleur neq ''}checked="checked"{/if}>
				<label class="form-check-label" for="couleur_fond_perso">{#feries_couleurperso#}</label>
			</div>
			<div id="divSpecificColor" style="display:{if $ferie.couleur eq ''}none{else}inline-block{/if}" class="col-form-label">
					{if $smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE neq ""}
						{if $smarty.session.isMobileOrTablet==1}
							<input name="couleur_user" id="couleur_ferie" maxlength="6" type="color" list="colors" value="#{if $ferie.couleur eq ''}{$couleurExFerie}{else}{$ferie.couleur}{/if}" />
							<datalist id="colors">
								{foreach from=","|explode:$smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE item=couleurTmp}
								<option>{$couleurTmp|xss_protect}</option>
								{/foreach}
							</datalist>
						{else}
							<select name="couleur2" id="couleur2" style="background-color:#{$ferie.couleur};color:{"#"|cat:$ferie.couleur|buttonFontColor}" class="form-control" >
							{if $ferie.couleur eq ""}<option value="">{#winProjet_couleurchoix#}</option>{/if}
							{foreach from=","|explode:$smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE item=couleurTmp}
								<option value="{$couleurTmp|replace:'#':''}" style="background-color:{$couleurTmp};color:{$couleurTmp|buttonFontColor}" {if $couleurTmp eq "#"|cat:$ferie.couleur}selected="selected"{/if}>{$couleurTmp|replace:'#':''}</option>
							{/foreach}
						</select>
						{/if}
					{else}
						<input id="couleur" name="couleur" maxlength="6" {if $smarty.session.isMobileOrTablet==1}type="color"{else}type="text"{/if} value="#{$ferie.couleur|xss_protect}"/>
					{/if}
			</div>
		</div>
	</div>
	<div class="form-group row col-md-12 align-items-center">
		<div class="col-md-3"></div>
		<div class="col-md-6">
			<br />
			<input type="button" class="btn btn-primary" value="{#enregistrer#|escape:"html"}" onclick="xajax_submitFormFerie(document.getElementById('date_ferie').value, document.getElementById('libelle').value, {if $smarty.const.CONFIG_PROJECT_COLORS_POSSIBLE neq ""}$('#couleur2 option:selected').val(){else}$('#couleur').val(){/if});"/>
		</div>
</form>