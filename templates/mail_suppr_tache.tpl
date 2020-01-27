
{assign var=titre_email value="mail_supprTache_sujet"}
{include file="mail_header.tpl"}

{#mail_supprTache_corps#}

<br/><br/>
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

{include file="mail_footer.tpl"}
