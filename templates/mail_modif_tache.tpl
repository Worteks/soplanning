
{assign var=titre_email value="mail_modifTache_sujet"}
{include file="mail_header.tpl"}

{#mail_modifTache_corps#}
<br/>
<br/>

{if $periode.titre neq ""}
	{#winPeriode_titre#} : {$periode.titre}
	<br/>
{/if} 
{#winPeriode_projet#} : {$projet.nom} ({$projet.projet_id})
<br/>
{#winPeriode_debut#} : {$periode.date_debut|sqldate2userdate} 
<br/>
{if $periode.date_fin neq ""}
	{#winPeriode_fin#} : {$periode.date_fin|sqldate2userdate}{else}{#mail_tacheDuree#} : {$periode.duree|sqltime2usertime}
	<br/>
{/if} 
{if $heure_debut neq ""}
	{#mail_heure_debut#} : {$heure_debut|sqltime2usertime}
	<br/>
{/if} 
{if $heure_fin neq ""}
	{#mail_heure_fin#} : {$heure_fin|sqltime2usertime}
	<br/>
{/if} 
{if $periode.lieu_id neq ""}
	{#winPeriode_lieu#} : {$periode.lieu_id}
	<br/>
{/if} 
{if $periode.ressource_id neq ""}
	{#winPeriode_ressource#} : {$periode.ressource_id}
	<br/>
{/if} 
{if $periode.notes neq ""}
	{#winPeriode_commentaires#} : {$periode.notes}
	<br/>
{/if}
{if $periode.lien neq ""}
	{#winPeriode_lien#} : {$periode.lien}
	<br/>
{/if}

{if isset($lienTache)}
	<br><br>
	<a href="{$lienTache}">{#modifier_tache#}</a>
{/if}

<br><br> 
--------------------------------------------------------------------------------------------- 
<br>
{if $oldPeriode.titre neq ""}
	{#winPeriode_titre#} : {$oldPeriode.titre}
	<br/>
{/if} 
{#winPeriode_projet#} : {$projet.nom} ({$projet.projet_id})
<br/>
{#winPeriode_debut#} : {$oldPeriode.date_debut|sqldate2userdate} 
<br/>
{if $oldPeriode.date_fin neq ""}
	{#winPeriode_fin#} : {$oldPeriode.date_fin|sqldate2userdate}{else}{#mail_tacheDuree#} : {$oldPeriode.duree|sqltime2usertime}
	<br/>
{/if} 
{if $heure_debut_old neq ""}
	{#mail_heure_debut#} : {$heure_debut_old|sqltime2usertime}
	<br/>
{/if} 
{if $heure_fin_old neq ""}
	{#mail_heure_fin#} : {$heure_fin_old|sqltime2usertime}
	<br/>
{/if} 
{if $oldPeriode.lieu_id neq ""}
	{#winPeriode_lieu#} : {$oldPeriode.lieu_id}
	<br/>
{/if} 
{if $oldPeriode.ressource_id neq ""}
	{#winPeriode_ressource#} : {$oldPeriode.ressource_id}
	<br/>
{/if} 
{if $oldPeriode.notes neq ""}
	{#winPeriode_commentaires#} : {$oldPeriode.notes}
	<br/>
{/if}
{if $oldPeriode.lien neq ""}
	{#winPeriode_lien#} : {$oldPeriode.lien}
	<br/>
{/if}


{include file="mail_footer.tpl"}
