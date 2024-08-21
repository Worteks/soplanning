{* Smarty *}

{include file="www_header.tpl"}

<div class="container">
	<div class="row">
		<div class="span12">
			<div class="soplanning-box" style="font-size:17px;width:800px">
				<table width="100%">
				<tr>
					<td><b>{#faq_roi#|strtoupper}</b></td>
					<td align="right">
						<a href="index.php" class="btn btn-sm btn-default">{#aide_retour#}</a>
					</td>
				</tr>
				</table>
				<b></b>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="span12">
			<div class="soplanning-box mt-2">
				{#roi_contenu#}
				<br><br>
				<center>
					<a href="index.php" class="btn btn-sm btn-default">{#aide_retour#}</a>
				</center>
			</div>
		</div>
	</div>
</div>

{include file="www_footer.tpl"}