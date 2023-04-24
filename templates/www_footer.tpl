		{* Smarty *}
 		<div class="navbar fixed-bottom navbar-light bg-white footer justify-content-center" id="footerbar">
			<a target="_blank" href="https://www.soplanning.org">www.soplanning.org</a>
			<span class="noprint">&nbsp;-&nbsp;</span>
			<a href="mailto:support@soplanning.org" class="noprint">{#soplanning_support#}</a>
			<span class="noprint">&nbsp;-&nbsp;</span>
			<a href="javascript:xajax_contact();undefined;" class="noprint">{#formContact_titre#}</a>
		</div>
		<div class="modal" tabindex="-1" role="dialog" id="myModal">
			<div class="modal-dialog modal-dialog-normal" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">...</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
					</div>
				</div>
			</div>
		</div>
		<div class="modal" id="alertModal" >
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
					</div>
				</div>
			</div>
		</div>
		<div class="modal" tabindex="-1" role="dialog" id="myBigModal">
			<div class="modal-dialog modalBig" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">...</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
					</div>
				</div>
			</div>
		</div>

		<script src="{$BASE}/assets/plugins/bootstrap3-typeahead/bootstrap3-typeahead.min.js"></script>
		<script src="{$BASE}/assets/plugins/jquery-ui-1.12.1.custom/i18n/datepicker-{$lang}.js"></script>
		<script src="{$BASE}/assets/plugins/bootstrap-4.6/js/bootstrap.bundle.min.js"></script>
		<script src="{$BASE}/assets/plugins/bootstrap-datepicker-1.9.0/js/bootstrap-datepicker.min.js"></script>
		<script src="{$BASE}/assets/plugins/bootstrap-datepicker-1.9.0/locales/bootstrap-datepicker.{if $lang eq "en"}en-GB{else}{$lang}{/if}.min.js" charset="UTF-8"></script>

		{$xajax}
		<script>
		{literal}
		$(".modal").draggable({
			handle: ".modal-header"
		});

		// datepicker activation
		$('.datepicker').datepicker({ 			
		calendarWeeks: true,
		language: "{/literal}{$lang}{literal}",
		format: "{/literal}{$smarty.const.CONFIG_DATE_DATEPICKER}{literal}",
		autoclose: true,
		todayHighlight: true,
		orientation: "bottom left"
		});

		// tooltip activation
		$('.tooltipster').tooltip({
			html: true,
			placement: 'auto',
			boundary: 'window'
		});
			

		{/literal}
		
		{if isset($user)}
			setTimeout("xajax_checkAvailableVersion('header');", 10000);
		{/if}
		{literal}
		var showFooter = false;
		$('#footerbar').mouseenter(function(){showFooter=true;});
		$('#footerbar').mouseleave(function(){showFooter=false;});

		$(function(){
			$(window).scroll(function() {	
			$('#footerbar').show();
			setTimeout(() => {  
					if (showFooter===false)
					{
						$('#footerbar').fadeOut(); 
					}
				}, 2500);

		});
		
		})

		{/literal}

		/*
		function registerServiceWorker() {
		  if ('serviceWorker' in navigator) {
			 navigator.serviceWorker.register('{$BASE}/serviceWorker.js') //
				.then(function(reg){
					console.log("service worker registered");
				}).catch(function(err) {
					console.log(err)
				});
		  }
		  else {
			console.log("Could not find serviceWorker in navigator");
		  }
		}
		registerServiceWorker();
		*/

		</script>
	</body>
</html>