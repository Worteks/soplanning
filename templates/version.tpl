{foreach from=$infos item=info key=titre}
	{assign var=nomTexteTmp value="version_"|cat:$titre}
	<b>{$smarty.config.$nomTexteTmp}</b> : {$info}
	<br />
{/foreach}

<div class="text-right">
	<a href="javascript:$('#infosVersion').hide();jQuery('#myModal').modal('hide');desactiverRappelVersion();undefined">{#version_nePlusRappeler#}</a>
</div>

