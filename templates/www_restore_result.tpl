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
				<div class="btn-group">
					<a href="restore.php" class="btn btn-default"><i class="fa fa-download fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#menuImport#}</a>
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
						{#import_resultat_titre#}
					</legend>
				{#import_fichier#} : {$fichier}<br />
				<table id="table_restore_result" class="mt-2 ml-5">
				{foreach from=$restore_elements key=cle item=fichier_restore}
					<tr><td colspan="4">{#import_type_enregistrement#} : <b>{$cle}</b></td></tr>
					{foreach from=$fichier_restore item=element}
					<tr><td>{if $element.type=="add" and isset($element.auto_increment)}[new]{else}{$element.id}{/if}</td><td>
					{if $element.type=="add"}{#import_type_add#}{/if}{if $element.type=="update"}{#import_type_update#}{/if}{if $element.type=="ignore"}{#import_type_ignore#}{/if}
					</td><td class="text-center"><span class="restore-{$element.status}">{$element.status}</span></td>
					<td>{if isset($element.error)}{$nberror=0}{#upload_erreur_champs#}{foreach from=$element.error item=erreur}{if $nberror>0}&nbsp;-&nbsp;{/if}{$erreur}{$nberror=$nberror+1}{/foreach}{/if}</td></tr>
					{/foreach}
				{/foreach}
				</table>
			</div>
		</div>
	</div>	
{include file="www_footer.tpl"}