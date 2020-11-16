		{* Smarty *}
 		<div class="navbar fixed-bottom navbar-light bg-white footer justify-content-center" id="footerbar">
			<a target="_blank" href="https://www.soplanning.org">www.soplanning.org</a>
			<span class="noprint">&nbsp;-&nbsp;</span>
			<a href="mailto:support@soplanning.org" class="noprint">{#soplanning_support#}</a>
			<span class="noprint">&nbsp;-&nbsp;</span>
			<a href="javascript:xajax_contact();undefined;" class="noprint">{#formContact_titre#}</a>
		</div>
		<div id="divFormSupport" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h3>{#formContact_titre#}</h3>
					</div>
					<div class="modal-body">
						<input type="text" id="rappel_pwd" placeholder="{#rappelPwdVotreEmail#}" class="form-control" />
					</div>
					<div class="modal-footer">
						<button class="btn btn-primary" id="changePwd">{#submit#}</button>
					</div>
				</div>
			</div>
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
		<script src="{$BASE}/assets/plugins/bootstrap-4.5.2/js/bootstrap.bundle.min.js"></script>
		{$xajax}
		<script>
		{literal}
		$(".modal").draggable({
			handle: ".modal-header"
		});
		
		// datepicker activation
		$(".datepicker").datepicker({ 
			showWeek: true, 
			dateFormat: "{/literal}{$smarty.const.CONFIG_DATE_DATEPICKER}{literal}"
		});		
		// tooltip activation
		$('.tooltipster').tooltip({
			html: true,
			placement: 'auto',
			boundary: 'window'
		});

		// timepicker defaults
		jQuery(function($){
			$.timepicker.regional['en'] = {
						hourText: '{/literal}{#heures#}{literal}',
						minuteText: '{/literal}{#minutes#}{literal}'}
			$.timepicker.setDefaults($.timepicker.regional['en']);
			$.timepicker.setDefaults({showPeriod:false, showPeriodLabels:false});
		});
		{/literal}
		
		{if isset($user)}
			setTimeout("xajax_checkAvailableVersion('header');", 10000);
		{/if}
		{literal}
		var showFooter = false;
		$('#footerbar').mouseenter(function(){showFooter=true;});
		$('#footerbar').mouseleave(function(){showFooter=false;});
		{/literal}
		$(function(){
			$(window).scroll(function() {
			$('.datepicker').datepicker('hide');			
			$('#footerbar').show();
			setTimeout(() => {  
					if (showFooter===false)
					{
						$('#footerbar').fadeOut(); 
					}
				}, 2500);
		});
		
		$('#myBigModal').on("scroll", function() {
			$('.datepicker').datepicker('hide');
			$('#ui-timepicker-div').hide(300);
		});
		
		})
		
		</script>
	</body>
</html>