{* Smarty *}

{include file="www_header.tpl"}

<div class="container">
	<div class="row">
		<div class="span12">
			<div class="soplanning-box" style="font-size:17px">
				{#index_contenu#}
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span12">
			<div class="soplanning-box mt-2">
				<table width="100%">
				<tr>
					<td style="font-size:18px">
						<a href="utilisateurs.php"><img src="{$BASE}/assets/img/pictos/users.png"> {#users_titre#}</a>
						<br><br>
						<a href="equipes.php"><img src="{$BASE}/assets/img/pictos/user_groupes.png"> {#equipes_titre#}</a>
						<br><br>
						<a href="projets.php"><img src="{$BASE}/assets/img/pictos/projets.png"> {#projets_titre#}</a>
						<br><br>
						<a href="groupes.php"><img src="{$BASE}/assets/img/pictos/groupes.png"> {#groupes_titre#}</a>
						<br><br>
					</td>
					<td style="font-size:18px">
						<a href="planning.php"><img src="{$BASE}/assets/img/pictos/logo.png"> {#planning_titre#}</a>
						<br><br>
						<a href="ressources.php"><img src="{$BASE}/assets/img/pictos/plug.png"> {#ressources_titre#}</a>
						<br><br>
						<a href="lieux.php"><img src="{$BASE}/assets/img/pictos/location.png"> {#lieux_titre#}</a>
						<br><br>
						<a href="faq.php"><img src="{$BASE}/assets/img/pictos/faq.png"> {#faq_titre#}</a>
					</td>
				</tr>
				</table>

				<br>
			</div>
		</div>
	</div>
</div>

{include file="www_footer.tpl"}