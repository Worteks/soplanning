<!DOCTYPE HTML>
<html lang="fr">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>{if isset($titre_email)}{$smarty.config.titre_email}{/if}</title>
<style type="text/css">
	{literal}
	a { border: none; }
	img { border: none; }
	p { margin: 0; line-height: 1.3em; }
	#footer-msg a { color: #F3A836; }
	h1,h2,h3,h4,h5,h6 {font-size:100%;margin:0;}
	{/literal}
</style>
</head>
<body style="margin: 0; padding: 0; background-color: #eeeeee" bgcolor="#eeeeee">
				
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
<tr>
	<td align="center" style="padding: 37px 0; background-color: #eeeeee;" bgcolor="#eeeeee">
		<div style="padding: 0 0 10px">
			<table cellpadding="0" cellspacing="0" border="0" style="margin: 0; border: 1px solid #dddddd; color: #444444; font-family: arial; font-size: 12px; border-color: #dddddd; background-color: #EEEEEE; " width="90%">
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0" border="0" style="margin: 0; border-collapse: collapse;" width="100%">
					<tr>
						<td style="color: #444444; font-family: arial; font-size: 12px; border-color: #dddddd; background-color: #EEEEEE;  padding: 5px 0;" align="left">
							<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;color: #444444; font-family: arial; font-size: 12px; border-color: #dddddd; background-color: #EEEEEE; ">
							<tr>
								<td width="10px"></td>
								<td width="90%" style="vertical-align: top; padding: 5px 0; ">
									<table cellpadding="0" cellspacing="0" style="border-collapse:collapse; text-align: center; width: 100% ">
									<tr>
										<td style="border-collapse:collapse;color: #444444; font-family: arial; font-size: 18px; font-weight:bold" >
											{if $smarty.const.CONFIG_SOPLANNING_URL != ''}
												{if $smarty.const.CONFIG_SOPLANNING_LOGO != ''}
													<a class="navbar-brand navbar-brand-logo" href="{$CONFIG_SOPLANNING_URL}"><img src="./upload/logo/{$smarty.const.CONFIG_SOPLANNING_LOGO}" alt='logo' class="logo" />
												{else}
													<a class="navbar-brand" href="{$smarty.const.CONFIG_SOPLANNING_URL}">
												{/if}
													{$smarty.const.CONFIG_SOPLANNING_TITLE}
												</a>
											{else}
												{$smarty.const.CONFIG_SOPLANNING_TITLE}
											{/if}
										</td>
									</tr>
									</table>
								</td>
							</tr>
							</table>
							<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;color: #444444; font-family: arial; font-size: 12px; border-color: #dddddd; background-color: #EEEEEE; ">
							<tr>
								<td width="10px"></td>
								<td width="90%" style="vertical-align: top; padding: 5px 0; ">
									<table cellpadding="0" cellspacing="0" style="border-collapse:collapse;width:100%;color: #444444; font-family: arial; font-size: 12px; border-color: #dddddd; background-color: #EEEEEE; " width="100%">
									<tr>
										<td style="padding:5px 0 5px 5px;line-height:normal;color: #444444; font-family: arial; font-size: 12px; border-color: #dddddd; background-color: #EEEEEE; ">
