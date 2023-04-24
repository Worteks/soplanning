
{if isset($afficher_tuto)}
	{if $smarty.session.isMobileOrTablet==0}

		<div class="modal fade" tabindex="-1" role="dialog" id="modal-tutoriel">
			<div class="modal-dialog modal-tutoriel" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">{#tutoriel_titre#}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div id="tutoriel-contenu-1">
							{#tutoriel_contenu_1#}
							<br><br>
							<div align="center">
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-1').hide();$('#tutoriel-contenu-2').show();undefined;">{#tutoriel_demarrer#}</a>
							</div>
						</div>
						<div id="tutoriel-contenu-2" style="display:none">
							{#tutoriel_contenu_2#}
							<br><br>
							<div align="center" width="630" style="border:1px solid #000000;">
								<video width="100%" controls autoplay muted loop>
									<source src="assets/videos/creation_tache_{if $lang eq "fr"}fr{else}en{/if}.mp4" type="video/mp4">
									Your browser does not support the video tag.
								</video>
							</div>
							<br>
							<div align="center">
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-1').show();$('#tutoriel-contenu-2').hide();undefined;">{#tutoriel_precedent#}</a>
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-3').show();$('#tutoriel-contenu-2').hide();undefined;">{#tutoriel_suivant#}</a>
							</div>
							<br>
						</div>
						<div id="tutoriel-contenu-3" style="display:none">
							{#tutoriel_contenu_3#}
							<br><br>
							<div align="center" width="630" style="border:1px solid #000000;">
								<video width="100%" controls autoplay muted loop>
									<source src="assets/videos/glisser_copier_{if $lang eq "fr"}fr{else}en{/if}.mp4" type="video/mp4">
									Your browser does not support the video tag.
								</video>
							</div>
							<br>
							<div align="center">
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-2').show();$('#tutoriel-contenu-3').hide();undefined;">{#tutoriel_precedent#}</a>
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-4').show();$('#tutoriel-contenu-3').hide();undefined;">{#tutoriel_suivant#}</a>
							</div>
							<br>
						</div>

						<div id="tutoriel-contenu-4" style="display:none">
							{#tutoriel_contenu_4#}
							<br><br>
							<div align="center" width="630" style="border:1px solid #000000;">
								<video width="100%" controls autoplay muted loop>
									<source src="assets/videos/selection_multiple_{if $lang eq "fr"}fr{else}en{/if}.mp4" type="video/mp4">
									Your browser does not support the video tag.
								</video>
							</div>
							<br>
							<div align="center">
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-3').show();$('#tutoriel-contenu-4').hide();undefined;">{#tutoriel_precedent#}</a>
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-5').show();$('#tutoriel-contenu-4').hide();undefined;">{#tutoriel_suivant#}</a>
							</div>
							<br>
						</div>

						<div id="tutoriel-contenu-5" style="display:none">
							{#tutoriel_contenu_5#}
							<br><br>
							<div align="center" width="630" style="border:1px solid #000000;">
								<video width="100%" controls autoplay muted loop>
									<source src="assets/videos/creation_tache_{if $lang eq "fr"}fr{else}en{/if}.mp4" type="video/mp4">
									Your browser does not support the video tag.
								</video>
							</div>
							<br>
							<div align="center">
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-4').show();$('#tutoriel-contenu-5').hide();undefined;">{#tutoriel_precedent#}</a>
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-6').show();$('#tutoriel-contenu-5').hide();undefined;">{#tutoriel_suivant#}</a>
							</div>
							<br>
						</div>

						<div id="tutoriel-contenu-6" style="display:none">
							{#tutoriel_contenu_6#}
							<br><br>
							<div align="center" width="630" style="border:1px solid #000000;">
								<video width="100%" controls autoplay muted loop>
									<source src="assets/videos/decalage_projet_{if $lang eq "fr"}fr{else}en{/if}.mp4" type="video/mp4">
									Your browser does not support the video tag.
								</video>
							</div>
							<br>
							<div align="center">
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-5').show();$('#tutoriel-contenu-6').hide();undefined;">{#tutoriel_precedent#}</a>
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-7').show();$('#tutoriel-contenu-6').hide();undefined;">{#tutoriel_suivant#}</a>
							</div>
							<br>
						</div>

						<div id="tutoriel-contenu-7" style="display:none">
							{#tutoriel_contenu_7#}
							<br><br>
							<div align="center">
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-6').show();$('#tutoriel-contenu-7').hide();undefined;">{#tutoriel_precedent#}</a>
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-8').show();$('#tutoriel-contenu-7').hide();undefined;">{#tutoriel_suivant#}</a>
							</div>
							<br>
						</div>

						<div id="tutoriel-contenu-8" style="display:none">
							{#tutoriel_contenu_8#}
							<br><br>
							<div align="center">
								<a class="btn btn-default" href="javascript:$('#tutoriel-contenu-7').show();$('#tutoriel-contenu-8').hide();undefined;">{#tutoriel_precedent#}</a>
							</div>
							<br>
						</div>

						<br><br>
						<div align="center">
							<a class="btn btn-default" href="javascript:if(confirm('{$smarty.config.tutoriel_ne_plus_afficher_confirm|escape:"javascript"}')){literal}{{/literal}$('#modal-tutoriel').modal('hide');xajax_tutoriel_masquer();{literal}}{/literal}undefined;">{#tutoriel_ne_plus_afficher#}</a>
						</div>

					</div>
				</div>
			</div>
		</div>

		<script>
			// Onload
			jQuery(function() {
				{if isset($afficher_tuto)}
				{literal}
					$("#modal-tutoriel").modal({backdrop: 'static',  keyboard: false});
					// fade in  : https://codepen.io/bootpen/pen/jbbaRa
					$('#modal-tutoriel').on('shown.bs.modal', function (e) {
					  $(".modal-backdrop").css({ opacity: 0.15 });
					})
					$('#modal-tutoriel').on('hidden.bs.modal', function (e) {
					  $(".modal-backdrop").css({ opacity: 0.5 });
					})
				{/literal}
				{/if}
			});
		</script>
	{/if}

{/if}