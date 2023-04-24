{* Smarty *}
{include file="www_header.tpl"}

<div class="container">
	{if !in_array("tasks_readonly", $user.tabDroits)}
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">				
				<div class="btn-group">
					<a href="backup.php" class="btn btn-default"><i class="fa fa-upload fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuExport#}</a>
				</div>				
			</div>
		</div>
	</div>
	{/if}
	<form action="process/restore.php" method="POST">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				<fieldset>
					<legend>
						{#import_titre#}
					</legend>
					<form enctype="multipart/form-data" id="fichier_form">
					<div class="form-group row col-md-12">
					<label class="col-md-3 col-form-label">{#import_type_fichier#} :</label>
					<div class="col-md-8 col-form-label">
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="type_restauration" id="restauration_sauvegarde" value="sauvegarde" checked="checked" onclick="$('#type_fichier_div').hide();$('#fichier').val('');$('#fichier').attr('accept','application/zip,.zip');$('#optionsdiv').hide();">
							<label class="form-check-label" for="restauration_sauvegarde">{#import_type_fichier_sauvegarde#}</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="type_restauration" id="restauration_import" value="import" onclick="$('#type_fichier_div').show();$('#uploaddiv').hide();$('#fichier').val('');$('#fichier').attr('accept','text/csv,.csv');$('#optionsdiv').hide();">
							<label class="form-check-label" for="restauration_import">{#import_type_fichier_seul#}</label>
						</div>
					</div>
					</div>
					<div id="type_fichier_div" class="form-group row col-md-12" style="display:none">
					<label class="col-md-3 col-form-label">{#import_type_fichier_csv#} :</label>
					<div class="col-md-3 col-form-label">
						<select name='type_fichier_import' id='type_fichier_import' class="form-control">
							<option value="user">{#import_type_fichier_user#}</option>
							<option value="user_groupe">{#import_type_fichier_user_groupe#}</option>
							<option value="projet">{#import_type_fichier_projet#}</option>
							<option value="projet_groupe">{#import_type_fichier_projet_groupe#}</option>
							<option value="periode">{#import_type_fichier_tache#}</option>
							<option value="lieu">{#import_type_fichier_lieu#}</option>
							<option value="ressource">{#import_type_fichier_ressource#}</option>
							<option value="feries">{#import_type_fichier_feries#}</option>
							<option value="status">{#import_type_fichier_status#}</option>
							<option value="config">{#import_type_fichier_config#}</option>
						</select>
					</div>
					</div>
					<div class="form-group row col-md-12">
					<label class="col-md-3 col-form-label">{#import_fichier#} :</label>
							<div class="align-self-center col-form-label ml-3">
							<input name="fichier" id="fichier" type="file" accept=".zip" />
							</div>
							<div title="{#import_fichier_aide#}" class="align-self-center cursor-help tooltipster">
								&nbsp;&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i>
							</div>
					</div>

					<div id="uploaddiv" class="form-group row col-md-12" style="display:none">
						<label class="col-md-3 col-form-label">{#import_choix_donnees#} :</label>
					<div class="col-8">
						<div id="config-div" class="col-md-12 form-check col-form-label" style="display:none">
							<input class="form-check-input" type="checkbox" name="export_configuration" id="export_configuration" value="1" checked="checked">
							<label class="form-check-label" for="export_configuration">
							{#export_choix_configuration#} : <b><span id='config-nb'></span></b> {#import_element#}
							</label>
						</div>	
						<div id="projets-div" class="col-md-12 form-check col-form-label" style="display:none">
							<input class="form-check-input" type="checkbox" name="export_projets" id="export_projets" value="1" checked="checked">
							<label class="form-check-label" for="export_projets">
							{#export_choix_projets#} : <b><span id='projets-nb'></span></b> {#import_element#}
							</label>
						</div>
						<div id="taches-div" class="col-md-12 form-check col-form-label" style="display:none">
							<input class="form-check-input" type="checkbox" name="export_taches" id="export_taches" value="1" checked="checked">
							<label class="form-check-label" for="export_taches">
							{#export_choix_taches#} : <b><span id='taches-nb'></span></b> {#import_element#}
							</label>
						</div>
						<div id="user-div" class="col-md-12 form-check col-form-label" style="display:none">
							<input class="form-check-input" type="checkbox" name="export_users" id="export_users" value="1" checked="checked">
							<label class="form-check-label" for="export_users">
							{#export_choix_users#} : <b><span id='user-nb'></span></b> {#import_element#}
							</label>
						</div>
						<div id="lieux-div" class="col-md-12 form-check col-form-label" style="display:none">
							<input class="form-check-input" type="checkbox" name="export_lieux" id="export_lieux" value="1" checked="checked">
							<label class="form-check-label" for="export_lieux">
							{#export_choix_lieux#} : <b><span id='lieux-nb'></span></b> {#import_element#}
							</label>
						</div>
						<div id="ressources-div" class="col-md-12 form-check col-form-label" style="display:none">
							<input class="form-check-input" type="checkbox" name="export_ressources" id="export_ressources" value="1" checked="checked">
							<label class="form-check-label" for="export_ressources">
							{#export_choix_ressources#} : <b><span id='ressources-nb'></span></b> {#import_element#}
							</label>
						</div>
						</div>
					</div>

					<div id="optionsdiv" class="form-group row col-md-12" style="display:none">
						<label class="col-md-3 col-form-label">{#import_options#} :</label>
					<div class="col-8">					
						<div class="ml-6 col-md-12 form-check col-form-label">
							<input class="form-check-input" type="checkbox" name="import_options_ecrasement" id="import_options_ecrasement" value="1" checked="checked">
							<label class="form-check-label" for="import_options_ecrasement">
							{#import_options_ecrasement#}
							</label>
						</div>

						<div id="configoptions-div" class="ml-6 col-md-12 form-check col-form-label"  style="display:none">
							<input class="form-check-input" type="checkbox" name="import_options_configuration" id="import_options_configuration" value="1" checked="checked">
							<label class="form-check-label" for="import_options_configuration">
							{#import_options_parametrage#}
							</label>
						</div>
					</div>
					</div>

					<div class="form-group row col-md-12 align-items-center">
						<div class="col-md-4"></div>
						<div class="col-6">
							<br />
							<input type="submit" class="btn btn-primary" id="bouton-restore" disabled value="{#restaurer#}"/>
							<input type="hidden" name="max_size_upload" id="max_size_upload" value="{$smarty.const.MAX_SIZE_UPLOAD}">
						</div>
					</div>	
				</form>	
			</div>
		</div>
	</div>	
<script>
	{literal}
	document.getElementById('fichier').onchange = function(){
         fileUploadBackup();
 	};
	{/literal}
</script>
{include file="www_footer.tpl"}