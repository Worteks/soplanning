{* Smarty *}
{include file="www_header.tpl"}
<div class="container">
	<div class="form-group row">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					<a href="{$BASE}/projets.php" class="btn btn-default" ><img src="assets/img/pictos/projets.png" alt=""> {#menuListeProjets#}</a>
					<a href="{$BASE}/groupe_list.php" class="btn btn-default"><img src="assets/img/pictos/groupes.png" alt=""> {#menuListeGroupes#}</a>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				{if isset($error_fields)}
					<div class="alert alert-danger">
						<h5>{#groupe_erreurChamps#} :</h5>
						<ul>
							{foreach from=$error_fields item=field}
								<li>{$field}</li>
							{/foreach}
						</ul>
					</div>
				{/if}
				<form action="{$BASE}/process/groupe_save.php" method="POST" class="form-horizontal">
					<input type="hidden" name="saved" value="{$groupe.saved}" />
					<input type="hidden" name="groupe_id" value="{$groupe.groupe_id}" />
					<div class="input-group">
						<label class="col-md-2 col-form-label" for="nom">{#groupe_nom#} :</label>
						<div class="col-md-8">
							<div class="input-group">
								<input name="nom" id="nom" type="text" class="form-control" value="{$groupe.nom|xss_protect}" maxlength="100" />
								<div class="input-group-append">
									<input type="submit" value="{#groupe_valider#|escape:"html"}" class="btn btn-default" />
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
{include file="www_footer.tpl"}