{* Smarty *}
<form class="form-horizontal">
	<input type="hidden" id="form_contact_version" value="{$infoVersion}" />
	<div class="form-group row form-inline">
		<label for="form_contact_email" class="col-md-3 col-form-label">{#formContact_email#} :</label>
		<div class="col-md-7">
			<input type="text" class="form-control" id="form_contact_email" value="{if isset($user) && $user.email neq ''}{$user.email}{/if}" />
		</div>
		</div>
		<div class="form-group row form-inline">
			<label for="form_contact_commentaire" class="col-md-3 col-form-label">{#formContact_commentaire#} :</label>
			<div class="col-md-7">
				<textarea rows="2" class="form-control" id="form_contact_commentaire"></textarea>
			</div>
		</div>
		<div class="form-group row form-inline">
				<div class="offset-md-3 col-md-7">
					<label class="checkbox-inline">
						<input type="checkbox" id="form_contact_abo" value="1" checked="checked" />&nbsp;{#formContact_newsletter#}
					</label>
				</div>
		</div>
		<div class="form-group">
				<div class="offset-md-3 col-md-6">
					<input type="button" class="btn btn-primary" onClick="if(confirm('{#confirm#|escape:"javascript"}'))xajax_submitFormContact(document.getElementById('form_contact_version').value, document.getElementById('form_contact_email').value, document.getElementById('form_contact_commentaire').value, document.getElementById('form_contact_abo').checked);" value="{#formContact_envoyer#}" />
				</div>
			</div>
		</div>
	</div>	
		
	</form>