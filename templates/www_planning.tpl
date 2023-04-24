{* Smarty *}
{include file="www_header.tpl"}
{include file="www_planning_filtre.tpl"}
	{* le planning *}
		<div class="position-relative" id="thirdLayer">
			{if $fleches eq 1}
				<div id="left-scroll">					
					<span class="fa-stack">
						<i class="fa fa-chevron-left fa-2x" id="left-button" aria-hidden="true"></i>				
					</span>
				</div>
			{/if}
			<div id="divConteneurPlanning" style="width:99vw;">
				{$htmlTableau}
			</div>
			{if $fleches eq 1}
				<div id="right-scroll">						
					<span class="fa-stack">
						<i class="fa fa-chevron-right fa-2x" id="right-button" aria-hidden="true"></i>				
					</span>
				</div>
			{/if}
			<br><br><br>
		 </div> 
	{if isset($htmlRecap) and $htmlRecap neq ""}
	<div class="vw-100 noprint" id="divRecap">
		<div >
			<div id="divPlanningRecap" class="soplanning-box pt-0" >
				{$htmlRecap}
			</div>
		</div>
	</div>
	{literal}
	<script>
	$(window).scroll(function(){
    $('#divRecap').css({
        'left': $(this).scrollLeft() + 0 
		});
	});
	</script>
	{/literal}
	{/if}
<div id="divChoixDragNDrop" onMouseOut="masquerSousMenuDelai('divChoixDragNDrop');" onMouseOver="AnnuleMasquerSousMenu('divChoixDragNDrop');" onfocus="AnnuleMasquerSousMenu('divChoixDragNDrop')">
	<a href="javascript:windowPatienter();xajax_moveCasePeriode(idCaseEnCoursDeplacement, idCaseDestination, false, 'seule');undefined;">{#planning_deplacer#}<div title="{#action_aide_deplacer_seule#}" class="align-self-center cursor-help tooltipster" style="display:block;float:right;margin-left:0px;"><i class="fa fa-question-circle" aria-hidden="true"></i></div></a>
	<a href="javascript:windowPatienter();xajax_moveCasePeriode(idCaseEnCoursDeplacement, idCaseDestination, false, 'toutes');undefined;">{#planning_deplacer_toutestaches#}<div title="{#action_aide_deplacer_toutestaches#}" class="align-self-center cursor-help tooltipster" style="display:block;float:right;margin-left:0px;"><i class="fa fa-question-circle" aria-hidden="true"></i></div></a>
	<a href="javascript:windowPatienter();xajax_moveCasePeriode(idCaseEnCoursDeplacement, idCaseDestination, true);undefined;">{#planning_copier#}</a>
	<a href="javascript:masquerSousMenu('divChoixDragNDrop');document.location.reload();">{#planning_annuler#}</a>
</div>
<script>
{literal}
Reloader.init({/literal}{$smarty.const.CONFIG_REFRESH_TIMER}{literal});
{/literal}
{* when coming from an email *}
{if isset($direct_periode_id)}
	addEvent(window, 'load', function(){literal}{{/literal}xajax_modifPeriode({$direct_periode_id}){literal}}{/literal});
{/if}

{* textes pour erreur dans fichier JS *}
var js_choisirProjet = '{#js_choisirProjet#|xss_protect}';
var js_choisirUtilisateur = '{#js_choisirUtilisateur#|xss_protect}';
var js_choisirDateDebut = '{#js_choisirDateDebut#|xss_protect}';
var js_saisirFormatDate = '{#js_saisirFormatDate#|xss_protect}';
var js_dateFinInferieure = '{#js_dateFinInferieure#|xss_protect}';
var js_deposerCaseSurDate = '{#js_deposerCaseSurDate#|xss_protect}';
var js_deplacementOk = '{#js_deplacementOk#|xss_protect}';
var js_patienter = '{#js_patienter#|xss_protect}';
var idDrag;
var dragElementParent;
var oldDragBorder;
var displayMode = {$modeAffichage|@json_encode};
var dateDebut = {$dateDebut|@json_encode};
var dateFin = {$dateFin|@json_encode};
{literal}
	// Gestion du filtre Projet
		$("#filtreGroupeProjet").multiselect({
			selectAll:false,
			noUpdatePlaceholderText:true,
			search   : true,
			nameSuffix: 'projet',
			desactivateUrl: 'process/planning.php?desactiverFiltreGroupeProjet=1',
			placeholder: '{/literal}<i class="fa fa-book fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;{#taches_filtreProjets#}</span>{literal}',
			texts: {
				selectAll    : '{/literal}{#formFiltreProjetCocherTous#}{literal}',
				unselectAll    : '{/literal}{#formFiltreProjetDecocherTous#}{literal}',
				disableFilter : '{/literal}{#formFiltreProjetDesactiver#}{literal}',
				validateFilter : '{/literal}{#submit#}{literal}',
				search : '{/literal}{#search#}{literal}'
			},
		});
		$("#filtreGroupeProjet").show();
	// Gestion du filtre User
		$("#filtreUser").multiselect({
			selectAll:false,
			noUpdatePlaceholderText:true,
			search   : true,
			nameSuffix: 'user',
			desactivateUrl: 'process/planning.php?desactiverFiltreUser=1',
			placeholder: '{/literal}<i class="fa fa-user fa-lg fa-fw" aria-hidden="true"></i><span class="d-none d-md-inline-block">&nbsp;{#formChoixUser#}</span>{literal}',
			texts: {
				selectAll    : '{/literal}{#formFiltreUserCocherTous#}{literal}',
				unselectAll    : '{/literal}{#formFiltreUserDecocherTous#}{literal}',
				disableFilter : '{/literal}{#formFiltreUserDesactiver#}{literal}',
				validateFilter : '{/literal}{#submit#}{literal}',
				search : '{/literal}{#search#}{literal}'
			},
		});
		$("#filtreUser").show();
	// Ajout des boutons de scroll de planning
	var e = $("#divConteneurPlanning").get(0);
	if (e.scrollWidth > e.clientWidth)
	{
		{/literal}
		{if $fleches eq 1}
		{literal}
			$('#left-scroll').show();
			$('#right-scroll').show();
			$('#right-button').click(function() {
				window.scrollBy({
				    top: 0,
					left: 800,
					behavior : "smooth"
				});
			});
			$('#left-button').click(function() {
				window.scrollBy({
				    top: 0,
					left: -800,
					behavior : "smooth"
				});
			});
		{/literal}
		{/if}
		{literal}
	}
		{/literal}
		{if $baseligne == "heures"}
			{literal}
				$('#divConteneurPlanning').attr('style','overflow:visible');
			{/literal}
		{/if}		
	{literal}
	
	var tabCellsSelected = new Array();

	// Affichage du formulaire période si clic sur case vide
	$('#tabContenuPlanning td.week,#tabContenuPlanning td.weekend,#tabContenuPlanning .cellProject,#tabContenuPlanning .cellProjectBiseau1, #tabContenuPlanning .cellProjectBiseau2').click(function(ev){
		ev.preventDefault();
		if (ev.ctrlKey) {
			if ($(this).hasClass("cellProject") || $(this).hasClass("cellProjectBiseau1")  || $(this).hasClass("cellProjectBiseau2")) {
				checkPos = tabCellsSelected.indexOf(this.id);
				if (checkPos >= 0) {
					tabCellsSelected.splice(checkPos, 1);
					$(this).removeClass("bordureSelectionne");
				} else {
					tabCellsSelected.push(this.id);
					$(this).addClass("bordureSelectionne");
				}
			}
		} else {
			if (!$(this).hasClass("read-only")){
				if ($(this).hasClass("cellProject") || $(this).hasClass("cellProjectBiseau1")  || $(this).hasClass("cellProjectBiseau2")) {
					cellClic(this.id,0);
				} else {
					{/literal}{if isset($droitAjoutPeriode) and $droitAjoutPeriode== true}{literal}
					cellClic(this.id,1);
					{/literal}{/if}{literal}

				}
				return false;
			}
		}
	});
	
		
	function resizeDivConteneur()
	{

	}

	// Gestion du cookie de positionnement
	function writeCookie(displayMode){
		if (displayMode == 'mois'){
			document.cookie='yposMoisWin=' + window.pageYOffset;
			document.cookie='xposMoisWin=' + window.pageXOffset;
		}else if (displayMode == 'jour'){
			document.cookie='yposJoursWin=' + window.pageYOffset;
			document.cookie='xposJoursWin=' + window.pageXOffset;
		}
	}
	{/literal}
	// Mémorisation scrolling
	{if isset($smarty.cookies.dateDebut)}
		var cookieDateDebut = '{$smarty.cookies.dateDebut}';
	{else}
		var cookieDateDebut = 0;
	{/if}
	{if isset($smarty.cookies.dateFin)}
		var cookieDateFin = '{$smarty.cookies.dateFin}';
	{else}
		var cookieDateFin = 0;
	{/if}
	{literal}
	if (dateDebut != cookieDateDebut || dateFin != cookieDateFin)  
	{
		document.cookie='dateDebut=' + dateDebut ;
		document.cookie='dateFin=' + dateFin ;
		document.cookie='xposMoisWin=0';
		document.cookie='xposJoursWin=0';
		document.cookie='yposMoisWin=0';
		document.cookie='yposJoursWin=0';
	}
	// Récuperation
	if (displayMode == 'mois')
	{
		{/literal}
		{if isset($smarty.cookies.xposMoisWin)}
			var xscrollWin = {$smarty.cookies.xposMoisWin};
		{else}
			var xscrollWin = 0;
		{/if}
		{if isset($smarty.cookies.yposMoisWin)}
			var yscrollWin = {$smarty.cookies.yposMoisWin};
		{else}
			var yscrollWin = 0;
		{/if}
		{literal}
	}else if (displayMode == 'jour'){
		{/literal}
		{if isset($smarty.cookies.xposJoursWin)}
			var xscrollWin = {$smarty.cookies.xposJoursWin};
		{else}
			var xscrollWin = 0;
		{/if}
		{if isset($smarty.cookies.yposJoursWin)}
			var yscrollWin = {$smarty.cookies.yposJoursWin};
		{else}
			var yscrollWin = 0;
		{/if}
		{literal}
	}
	window.scroll(xscrollWin,yscrollWin);
	window.onscroll = function() {writeCookie(displayMode)};
	{/literal}
	{literal}
		resizeDivConteneur();
	{/literal}


	// Onload
	jQuery(function() {
		{if $smarty.session.isMobileOrTablet==0}
			{literal}
			// hack pour empecher fermeture du layer au click sur les boutons du calendrier1
			$("#ui-datepicker-div").click( function(event) {
				event.stopPropagation();
			});
			jQuery('#dropdownDateSelector .dropdown-menu').on({
			"click":function(e){
					e.stopPropagation();
				}
			});

			$(document).on('keyup', function(e) {
				if (e.which == 17 && tabCellsSelected.length > 0){
					xajax_selection_multi_tache_form(tabCellsSelected);
				}
			});

			{/literal}
		{/if}
	});

</script>

{include file="tutoriel.tpl"}

{include file="www_footer.tpl"}