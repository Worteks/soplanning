
{assign var=titre_email value="mail_modifTache_sujet"}
{include file="mail_header.tpl"}

{#mail_modifTache_corps#}
<br/>
<br/>

{if $periode.titre neq ""}
	<b>{#winPeriode_titre#}</b> : {$periode.titre}
	<br/>
{/if} 
<b>{#winPeriode_projet#}</b> : {$projet.nom} ({$projet.projet_id})
<br/>
<b>{#winPeriode_debut#}</b> : {$periode.date_debut|sqldate2userdate} 
<br/>
{if $periode.date_fin neq ""}
	<b>{#winPeriode_fin#}</b> : {$periode.date_fin|sqldate2userdate}{else}<b>{#mail_tacheDuree#}</b> : {$periode.duree|sqltime2usertime}
	<br/>
{/if} 
{if isset($heure_debut) && $heure_debut neq ""}
	<b>{#mail_heure_debut#}</b> : {$heure_debut|sqltime2usertime}
	<br/>
{/if} 
{if isset($heure_fin) && $heure_fin neq ""}
	<b>{#mail_heure_fin#}</b> : {$heure_fin|sqltime2usertime}
	<br/>
{/if}
{if $periode.lieu_id neq ""}
	<b>{#winPeriode_lieu#}</b> : {$lieu.nom} ({$periode.lieu_id})
	<br/>
{/if} 
{if $periode.ressource_id neq ""}
	<b>{#winPeriode_ressource#}</b> : {$ressource.nom} ({$periode.ressource_id})
	<br/>
{/if} 
{if $periode.ressource_id neq ""}
	<b>{#winPeriode_ressource#}</b> : {$periode.ressource_id}
	<br/>
{/if} 
{if $periode.notes neq ""}
	<b>{#winPeriode_commentaires#}</b> : {$periode.notes}
	<br/>
{/if}
{if $periode.lien neq ""}
	<b>{#winPeriode_lien#}</b> : {$periode.lien}
	<br/>
{/if}
{if $periode.fichiers neq ""}
	<br/>
	<b>{#winPeriode_fichier#}</b> :<ul>
		{foreach from=$fichiers item=fichier}
			<li><a href="{$base}/upload/files/{$periode.link_id}/{$fichier}" target="_blank" class="ellipsis fileupload">{$fichier}</a></li>
		{/foreach}
		</ul>
{/if}

{if isset($lienTache)}
	<br><br>
	<a href="{$lienTache}">{#modifier_tache#}</a>
{/if}

<br><br> 
--------------------------------------------------------------------------------------------- 
<br>
{if $oldPeriode.titre neq ""}
	<b>{#winPeriode_titre#}</b> : {$oldPeriode.titre}
	<br/>
{/if} 
<b>{#winPeriode_projet#}</b> : {$projet.nom} ({$projet.projet_id})
<br/>
<b>{#winPeriode_debut#}</b> : {$oldPeriode.date_debut|sqldate2userdate} 
<br/>
{if $oldPeriode.date_fin neq ""}
	<b>{#winPeriode_fin#}</b> : {$oldPeriode.date_fin|sqldate2userdate}{else}<b>{#mail_tacheDuree#}</b> : {$oldPeriode.duree|sqltime2usertime}
	<br/>
{/if} 
{if $heure_debut_old neq ""}
	<b>{#mail_heure_debut#}</b> : {$heure_debut_old|sqltime2usertime}
	<br/>
{/if} 
{if $heure_fin_old neq ""}
	<b>{#mail_heure_fin#}</b> : {$heure_fin_old|sqltime2usertime}
	<br/>
{/if}
{if $oldPeriode.statut_tache neq ""}
	<b>{#winPeriode_statut#}</b> : {$oldStatus}
	<br/>
{/if} 
{if $oldPeriode.lieu_id neq ""}
	<b>{#winPeriode_lieu#}</b> : {$oldLieu.nom} ({$oldPeriode.lieu_id})
	<br/>
{/if} 
{if $oldPeriode.ressource_id neq ""}
	<b>{#winPeriode_ressource#}</b> : {$oldRessource.nom} ({$oldPeriode.ressource_id})
	<br/>
{/if} 
{if $oldPeriode.notes neq ""}
	<b>{#winPeriode_commentaires#}</b> : {$oldPeriode.notes}
	<br/>
{/if}
{if $oldPeriode.lien neq ""}
	<b>{#winPeriode_lien#}</b> : {$oldPeriode.lien}
	<br/>
{/if}
{if $periode.fichiers neq ""}
	<br/>
	<b>{#winPeriode_fichier#}</b> :<ul>
		{foreach from=$fichiers item=fichier}
			<li><a href="{$base}/upload/files/{$periode.link_id}/{$fichier}" target="_blank" class="ellipsis fileupload">{$fichier}</a></li>
		{/foreach}
		</ul>
{/if}

{include file="mail_footer.tpl"}
