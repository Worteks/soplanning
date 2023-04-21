{* Smarty *}
{include file="www_header.tpl"}

<div class="container">
	<div class="small-container">
		<br><br>
		<div align="center" style="font-weight:bold;font-size:17px;">
		{#options_2fa#}
		<br><br>
		</div>
		po:{$user.google_2fa}
		
		{if $user.google_2fa eq "ok"}
			{#google_2fa_code_help#}
			<br><br>
		{/if}

		<form action="#" method="post" class="form-horizontal box">
			{if $user.google_2fa eq "setup"}
				<div class="form-group row col-md-12">
					<label for="login" class="col-md-4 col-sm-4 control-label"><br>{#google_2fa_scan_qrcode#} :</label>
					<div class="col-md-8 col-sm-8">
						<img src="{$qrcode}"  />
					</div>
				</div>
			{/if}
			<div class="form-group row col-md-12">
				<label for="code" class="col-md-4 col-sm-4 control-label">{#google_2fa_scan_code#} :</label>
				<div class="col-md-2 col-sm-2">
					<input type="code" class="form-control" name="code" id="code" />
				</div>
			</div>
			<div class="form-group row col-md-12">
				<label for="code" class="col-md-4 col-sm-4 control-label"></label>
				<div class="col-md-4 col-sm-">
					<a href="javascript:xajax_google_2fa_check_code($('#code').val());undefined;" class="btn btn-primary">{#submit#}</a>
				</div >
			</div>
		</form>
			
	</div>
</div>
{include file="www_footer.tpl"}