{include file="www_header.tpl"}
<link href="assets/css/simplePage.css" rel="stylesheet">
<br /><br />
<div class="container">
	<h3 class="text-center">
		{if $smarty.const.CONFIG_SOPLANNING_LOGO != ''}
				<img src="./upload/logo/{$smarty.const.CONFIG_SOPLANNING_LOGO}" alt='logo' style='height:40px;' id="logo" /><br />
		{/if}
		{if $smarty.const.CONFIG_SOPLANNING_TITLE neq "SOPlanning"}
			<span class="soplanning_index_title1">{$smarty.const.CONFIG_SOPLANNING_TITLE|xss_protect}</span>
		{else}
			<span class="soplanning_index_title2">Simple Online Planning</span>
		{/if}
		{if isset($infoVersion)}
			<small>v{$infoVersion}</small>
		{/if}
	</h3>
	<div class="small-container">
		{if isset($smartyData.message)}
			{assign var=messageFinal value=$smartyData.message|formatMessage}
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				{$messageFinal}
			</div>
		{/if}
		<form action="process/login.php" method="post" class="form-horizontal box">
			<div class="form-group row col-md-12">
				<label for="login" class="col-md-4 col-sm-4 control-label">{#login_login#} :</label>
				<div class="col-md-8 col-sm-8">
					{$userTmp.login}
				</div>
			</div>
			<div class="form-group row col-md-12">
				<label for="password" class="col-md-4 col-sm-4 control-label">{#rappelPwdNouveauPassword#} :</label>
				<div class="col-md-8 col-sm-8">
					<input type="password" size="20" name="password" class="form-control" id="password">
				</div>
			</div>
			<div class="form-group row col-md-12">
				<label class="col-md-4 col-sm-4 control-label"></label>
				<div class="col-md-8 col-sm-8">
					<a class="btn btn-primary" href="javascript:xajax_nouveauPwd(document.getElementById('password').value);undefined;">{#changePwd#}</a>
				</div>
			</div>
		</form>
	</div>
</div>
{include file="www_footer.tpl"}