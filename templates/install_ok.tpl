{include file="www_header.tpl"}

<link rel="stylesheet" href="{$BASE}/assets/css/simplePage.css" />	

		<div class="container">
			<h3 class="text-center">
				<span class="soplanning_install_title">Simple Online Planning</span>
			</h3>
			<div class="small-container">
				<div class="alert alert-success">
					{#install_installResultOk#}
				</div>
				<ul class="list-inline flag text-right">
					<li class="list-inline-item"><a tabindex="1" href="?language=pl" class="tooltipEvent" data-title="Polish"><img src="{$BASE}/assets/img/flag/pl.png" alt="Polish" title="Polish"/></a></li>
					<li class="list-inline-item"><a tabindex="2" href="?language=pt" class="tooltipEvent" data-title="Portuguese"><img src="{$BASE}/assets/img/flag/pt.png" alt="Portuguese" title="Portuguese"/></a></li>
					<li class="list-inline-item"><a tabindex="3" href="?language=es" class="tooltipEvent" data-title="Spanish"><img src="{$BASE}/assets/img/flag/es.png" alt="Spanish" title="Spanish" /></a></li>
					<li class="list-inline-item"><a tabindex="4" href="?language=de" class="tooltipEvent" data-title="German"><img src="{$BASE}/assets/img/flag/de.png" alt="German" title="German"/></a></li>
					<li class="list-inline-item"><a tabindex="5" href="?language=da" class="tooltipEvent" data-title="Danish"><img src="{$BASE}/assets/img/flag/da.png" alt="Danish" title="Danish"/></a></li>
					<li class="list-inline-item"><a tabindex="6" href="?language=hu" class="tooltipEvent" data-title="Hungarian"><img src="{$BASE}/assets/img/flag/hu.png" alt="Hungarian" title="Hungarian"/></a></li>
					<li class="list-inline-item"><a tabindex="7" href="?language=nl" class="tooltipEvent" data-title="Dutch"><img src="{$BASE}/assets/img/flag/nl.png" alt="Dutch" title="Dutch"/></a></li>
					<li class="list-inline-item"><a tabindex="8" href="?language=it" class="tooltipEvent" data-title="Italian"><img src="{$BASE}/assets/img/flag/it.png" alt="Italian" title="Italian"/></a></li>
					<li class="list-inline-item"><a tabindex="9" href="?language=fr" class="tooltipEvent" data-title="French"><img src="{$BASE}/assets/img/flag/fr.png" alt="French" title="French"/></a></li>
					<li class="list-inline-item"><a tabindex="10" href="?language=en" class="tooltipEvent" data-title="English"><img src="{$BASE}/assets/img/flag/en.png" alt="English" title="English"/></a></li>
				</ul>
				<p class="text-right text-info"><small><a href="mailto:support@soplanning.org">{#proposerTrad#}</a></small></p>
				<p class="text-right text-info"><small>{#install_intro#}</small></p>
			</div>
		</div>
{include file="www_footer.tpl"}