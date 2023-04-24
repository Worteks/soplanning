{* Smarty *}
{include file="www_header.tpl"}

<div class="container">
	{if !in_array("tasks_readonly", $user.tabDroits)}
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box">
				<div class="btn-group">
					<a href="restore.php" class="btn btn-default"><i class="fa fa-download fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuImport#}</a>
				</div>								
			</div>
		</div>
	</div>
	{/if}
	<form action="process/backup.php" method="POST" id="filtreTaches">
	<div class="row">
		<div class="col-md-12">
			<div class="soplanning-box mt-2">
				<fieldset>
					<legend>
						{#export_titre#}
					</legend>
						{#export_libelle#}
						<div class="form-group row col-md-12" >
						<label class="col-md-2 col-form-label">{#export_choix_donnees#} :</label>
						<div class="col-6">
						<div class="ml-3 col-md-12 form-check col-form-label">
							<input class="form-check-input" type="checkbox" name="export_configuration" id="export_configuration" value="1" checked="checked">
							<label class="form-check-label" for="export_configuration">
							{#export_choix_configuration#}
							</label>
						</div>	
						<div class="ml-3 col-md-12 form-check col-form-label">
							<input class="form-check-input" type="checkbox" name="export_projets" id="export_projets" value="1" checked="checked">
							<label class="form-check-label" for="export_projets">
							{#export_choix_projets#}
							</label>
						</div>
						<div class="ml-3 col-md-12 form-check col-form-label">
							<input class="form-check-input" type="checkbox" name="export_taches" id="export_taches" value="1" checked="checked">
							<label class="form-check-label" for="export_taches">
							{#export_choix_taches#}
							</label>
						</div>
						<div class="ml-3 col-md-12 form-check col-form-label">
							<input class="form-check-input" type="checkbox" name="export_users" id="export_users" value="1" checked="checked">
							<label class="form-check-label" for="export_users">
							{#export_choix_users#}
							</label>
						</div>
						{if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX == 1 }
						<div class="ml-3 col-md-12 form-check col-form-label">
							<input class="form-check-input" type="checkbox" name="export_lieux" id="export_lieux" value="1" checked="checked">
							<label class="form-check-label" for="export_lieux">
							{#export_choix_lieux#}
							</label>
						</div>
						{/if}
						{if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES == 1 }
						<div class="ml-3 col-md-12 form-check col-form-label">
							<input class="form-check-input" type="checkbox" name="export_ressources" id="export_ressources" value="1" checked="checked">
							<label class="form-check-label" for="export_ressources">
							{#export_choix_ressources#}
							</label>
						</div>
						{/if}
						</div>
					</div>
	<div class="form-group row col-md-12">
						<label class="col-md-2 col-form-label">{#export_nom_sauvegarde#} :</label>
						<div class="col-6">
							<div class="col-md-7">
								<input type="text" class="form-control" name="export_nom_sauvegarde" id="export_nom_sauvegarde" value="soplanning_{$smarty.now|date_format:"%Y%m%d-%H%M%S"}">
							</div>
						</div>
					</div>
					<div class="form-group row col-md-12 align-items-center">
						<div class="col-md-4"></div>
							<div class="col-6">
								<br />
								<input type="submit" class="btn btn-primary" value="{#sauvegarder#}"/>
							</div>
					</div>		
			</div>
		</div>
	</div>	

{include file="www_footer.tpl"}