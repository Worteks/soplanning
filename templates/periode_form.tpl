{* Smarty *}
<form class="form-horizontal" method="POST" target="_blank" id="periodForm2">
	<input type="hidden" id="periode_id" name="periode_id" value="{$periode.periode_id}" />
	<input type="hidden" id="link_id" name="link_id" value="{$link_id}" />	
	<input type="hidden" id="saved" name="saved" value="{$periode.saved}" />
	<input type="hidden" id="updateoccurrences" name="updateoccurrences" value="true" />
	<div class="container-fluid">
	<div class="form-group row col-md-12">
		<label class="col-md-2 col-form-label">{#winPeriode_titre#} :</label>
		<div class="col-md-10">
			<input type="text" class="form-control" name="titre" id="titre" maxlength="2000" value="{$periode.titre|xss_protect}" onFocus="xajax_autocompleteTitreTache($('#projet_id').val());"   data-provide="typeahead" tabindex="21" />
		</div>
	</div>
	<div class="form-group row col-md-12">
			<label class="col-md-2 col-form-label">{#winPeriode_projet#} :</label>
			<div class="col-md-4">
				<select name="projet_id" id="projet_id" class="form-control {if !$smarty.session.isMobileOrTablet}select2{/if}" tabindex="1" style="width:100%">
					<option value="">- - - - - - - - - - -</option>
					{assign var="groupeCourant" value="-1"}
					{foreach from=$listeProjets item=projetTmp}
						{if $groupeCourant != $projetTmp.groupe_id}
							{assign var="groupeCourant" value=$projetTmp.groupe_id}
							{if $projetTmp.groupe_id == ""}
								{assign var="nomgroupe" value=#projet_liste_sansGroupes#}
							{else}
								{assign var="nomgroupe" value=$projetTmp.nom_groupe}
							{/if}
							<optgroup label="{$nomgroupe}"></optgroup>
						{/if}
						<option value="{$projetTmp.projet_id}" {if $periode.projet_id eq $projetTmp.projet_id}selected="selected"{/if} {if isset($projet_id_choisi) && $projet_id_choisi eq $projetTmp.projet_id}selected="selected"{/if}>{$projetTmp.nom|xss_protect} ({$projetTmp.projet_id}) {if $projetTmp.livraison neq ''} - S{$projetTmp.livraison}{/if}</option>
					{/foreach}
				</select>
			</div>
			<label class="col-md-2 col-form-label">{#winPeriode_user#} :</label>
			<div class="col-md-4">
				<select multiple="multiple" name="user_id2" id="user_id2" class="form-control {if $smarty.session.isMobileOrTablet!=1}select2{/if}" tabindex="2" style="width:100%">
					{assign var=groupeTemp value=""}
					{foreach from=$listeUsers item=userCourant name=loopUsers}
						{if $userCourant.user_groupe_id neq $groupeTemp}
							<optgroup label="{$userCourant.groupe_nom}">
						{/if}
						<option value="{$userCourant.user_id}" {if $userCourant.user_id eq $periode.user_id}selected="selected"{/if} 
						{if $userCourant.user_id|in_array:$listeUsersSelect}selected="selected"{/if}
						>{$userCourant.nom} - {$userCourant.user_id}</option>
						{if $userCourant.user_groupe_id neq $groupeTemp}
							</optgroup>
						{/if}
						{assign var=groupeTemp value=$userCourant.user_groupe_id}
					{/foreach}
				</select>
			</div>
		</div>
		{if isset($estFilleOuParente)}
		<div class="row col-md-12">
			<div class="col-md-8 offset-md-2">
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="checkbox" id="appliquerATous" checked="checked" value="1"><label class="form-check-label" for="appliquerATous">{#winPeriode_appliquerATous#}</label>
			</div>
			</div>
		</div>
		{/if}
		<div class='col-md-12'><hr /></div>
		<div class="form-group row col-md-12">
			<label class="col-md-2 col-form-label">{#winPeriode_debut#} :</label>
			<div class="col-md-4">
				{if $smarty.session.isMobileOrTablet==1}
					<input type="date" class="form-control" name="date_debut" id="date_debut" maxlength="10" value="{$periode.date_debut|forceISODateFormat}" tabindex="4" />
				{else}
					<input type="text" class="form-control datepicker" name="date_debut" id="date_debut" maxlength="10" value="{$periode.date_debut|sqldate2userdate}" tabindex="4" />
				{/if}
			</div>
		</div>
		<div class="form-group row col-md-12">
			<label class="col-md-2 col-form-label">{#winPeriode_fin#} :</label>
			<div class="col-md-10 col-form-label">
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="radioChoixFin" id="radioChoixFinDate" value="" {if $periode.duree_details eq ""}checked="checked"{/if} onChange="$('#divFinChoixDate').removeClass('d-none');$('#divFinChoixDuree').addClass('d-none');" tabindex="5">
				<label class="form-check-label" for="radioChoixFinDate">{#winPeriode_finChoixDate#}</label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="radio" name="radioChoixFin" id="radioChoixFinDuree" value="" {if $periode.duree_details neq ""}checked="checked"{/if} onChange="$('#divFinChoixDuree').removeClass('d-none');$('#divFinChoixDate').addClass('d-none');" tabindex="6">
				<label class="form-check-label" for="radioChoixFinDuree">{#winPeriode_finChoixDuree#}</label>
			</div>
			</div>
			<div class="offset-md-2 col-md-10 form-inline" id="divFinChoixDate">
				{if $smarty.session.isMobileOrTablet==1}
					<input type="date" class="form-control datepicker" name="date_fin" id="date_fin" maxlength="10" value="{$periode.date_fin|forceISODateFormat}" onFocus="remplirDateFinPeriode();videChampsFinTache(this.id);" onChange="videChampsFinTache(this.id);" tabindex="7" />
				{else}
					<input type="text" class="form-control datepicker" name="date_fin" id="date_fin" maxlength="10" value="{$periode.date_fin|sqldate2userdate}" onFocus="remplirDateFinPeriode();videChampsFinTache(this.id);" onChange="videChampsFinTache(this.id);" tabindex="7" />
				{/if}
				&nbsp;{#winPeriode_ouNBJours#} :&nbsp;
				<input type="number" class="form-control col-md-1" name="nb_jours" id="nb_jours" size="2"  onChange="videChampsFinTache(this.id);" tabindex="10" />
			{if $periode.periode_id neq 0 && $periode.date_fin neq ""}
				<label class="checkbox-inline" ><input type="checkbox" id="conserver_duree" name="conserver_duree" value="1" onClick="videChampsFinTache('');" tabindex="11" />{#winPeriode_conserverDuree#|sprintf:$nbJours}</label>
			{else}
				<input type="hidden" id="conserver_duree" value="" />
			{/if}
			</div>
		</div>
			<div class="row col-md-12 form-inline {if $periode.duree_details eq ''}d-none{/if}" id="divFinChoixDuree">
				<div class="offset-md-2 col-md-3">
					{#winPeriode_ouNBHeures#} <span title="{#winPeriode_FormatDuree#|xss_protect}" class="cursor-help tooltipster">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></span> :
					<input type="text" class="form-control" name="duree" id="duree" size="3" value="{if $periode.duree_details eq 'duree'}{$periode.duree|sqltime2usertime}{/if}" onFocus="if(this.value == '')this.value='{$smarty.const.CONFIG_DURATION_DAY|usertime2sqltime:"short"}';" onChange="videChampsFinTache(this.id);" tabindex="12"  maxlength="5" autocomplete="off" />
				</div>
				<div class="col-md-7">
					{#winPeriode_heureDebut#} <span title="{#winPeriode_FormatDuree#|xss_protect}" class="cursor-help tooltipster">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></span> :
					<input type="text" class="form-control" id="heure_debut" id="heure_debut" size="3"  value="{if isset($periode.duree_details_heure_debut)}{$periode.duree_details_heure_debut|sqltime2usertime}{/if}" onChange="heurefinSynchro(this.value,{$smarty.const.CONFIG_PLANNING_DUREE_CRENEAU_HORAIRE});" tabindex="13"  maxlength="5" autocomplete="off" />
					{#winPeriode_heureFin#} <span title="{#winPeriode_FormatDuree#|xss_protect}" class="cursor-help tooltipster">&nbsp;<i class="fa fa-question-circle" aria-hidden="true"></i></span> :
					<input type="text" class="form-control" id="heure_fin" size="3" value="{if isset($periode.duree_details_heure_fin)}{$periode.duree_details_heure_fin|sqltime2usertime}{/if}" onChange="videChampsFinTache(this.id);" tabindex="14" maxlength="5" autocomplete="off" />
				</div>
				<div class="offset-md-5 col-md-6 form-inline">				
					<div class="form-check form-check-inline">
						&nbsp;&nbsp;&nbsp;&nbsp;<input class="form-check-input" type="checkbox" name="matin" id="matin" onChange="videChampsFinTache(this.id);" {if $periode.duree_details eq 'AM'}checked="checked"{/if} tabindex="15">
						<label class="form-check-label" for="matin">{#winPeriode_matin#} ({$smarty.const.CONFIG_DURATION_AM}{#tab_h#})</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" name="apresmidi" id="apresmidi" onChange="videChampsFinTache(this.id);" {if $periode.duree_details eq 'PM'}checked="checked"{/if} tabindex="16">
						<label class="form-check-label" for="apresmidi">{#winPeriode_apresmidi#} ({$smarty.const.CONFIG_DURATION_PM}{#tab_h#})</label>
					</div>
				</div>
			</div>
		<div class='col-md-12'><hr /></div>
		<div class="form-group row col-md-12">
			{if !isset($estFilleOuParente)}
				<input type="hidden" id="appliquerATous" value="0">
				<label class="col-md-2 col-form-label">{#winPeriode_repeter#} :</label>
				<div class="col-md-4">
					<select name="repetition" id="repetition" onChange="{literal}
						if(this.value=='jour')
						{
							$('#divOptionsRepetitionJour').removeClass('d-none');
							$('#divExceptionRepetition').removeClass('d-none');
						}else{
							$('#divOptionsRepetitionJour').addClass('d-none');
							$('#divOptionsjourderepetition').addClass('d-none');
							$('#divOptionsRepetitionJS').addClass('d-none');
						}
						if(this.value=='semaine')
						{
							$('#divOptionsRepetitionJS').removeClass('d-none');
							$('#divOptionsRepetitionSemaine').removeClass('d-none');
							$('#divExceptionRepetition').removeClass('d-none');
						}else{
							$('#divOptionsRepetitionSemaine').addClass('d-none');
							$('#divOptionsjourderepetition').addClass('d-none');
							$('#divOptionsRepetitionJS').addClass('d-none');
						}
						if(this.value=='mois'){
							$('#divOptionsRepetitionMois').removeClass('d-none');
							$('#divOptionsjourderepetition').removeClass('d-none');
							$('#divExceptionRepetition').removeClass('d-none');
						}else{
							$('#divOptionsRepetitionMois').addClass('d-none');
						}
						if(this.value==''){
							$('#divOptionsRepetitionJour').addClass('d-none');
							$('#divOptionsRepetitionSemaine').addClass('d-none');
							$('#divOptionsRepetitionMois').addClass('d-none');
							$('#divExceptionRepetition').addClass('d-none');
							$('#divOptionsjourderepetition').addClass('d-none');
							$('#divOptionsRepetitionJS').addClass('d-none');
						}
						{/literal}" class="form-control" tabindex="17">
							<option value="">{#winPeriode_repeter_pasderepetition#}</option>
							<option value="jour">{#winPeriode_repeter_jour#}</option>
							<option value="semaine">{#winPeriode_repeter_semaine#}</option>
							<option value="mois">{#winPeriode_repeter_mois#}</option>
					</select>
				</div>
				<div class="col-md-6 form-row form-inline">
						<div id="divOptionsRepetitionJour" class="d-none form-group form-inline" tabindex="18">{#winPeriode_repeter_tousles#}&nbsp;
							<select name='nbRepetitionJour' id='nbRepetitionJour' class="form-control">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							<option value="13">13</option>
							<option value="14">14</option>
							<option value="15">15</option>
							<option value="16">16</option>
							<option value="17">17</option>
							<option value="18">18</option>
							<option value="19">19</option>
							<option value="20">20</option>
							<option value="21">21</option>
							<option value="22">22</option>
							<option value="23">23</option>
							<option value="24">24</option>
							<option value="25">25</option>
							<option value="26">26</option>
							<option value="27">27</option>
							<option value="28">28</option>
							<option value="29">29</option>
							<option value="30">30</option>
							</select>
							&nbsp;{#winPeriode_jour#}&nbsp;{#winPeriode_repeter_jusque#}&nbsp;
							{if $smarty.session.isMobileOrTablet==1}
								<input type="date" class="form-control" id="dateFinRepetitionJour" value="" size="11" maxlength="10" onFocus="remplirDateRepetition(this.id);" tabindex="18">
							{else}
								<input type="text" class="form-control datepicker" id="dateFinRepetitionJour" value="" size="11" maxlength="10" onFocus="remplirDateRepetition(this.id);" tabindex="18">
							{/if}
						</div>
						<div id="divOptionsRepetitionSemaine" class="d-none form-group form-inline" tabindex="19">
							{#winPeriode_repeter_tousles#}&nbsp;
							<select name='nbRepetitionSemaine' id='nbRepetitionSemaine' class="form-control">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							<option value="13">13</option>
							<option value="14">14</option>
							<option value="15">15</option>
							<option value="16">16</option>
							<option value="17">17</option>
							<option value="18">18</option>
							<option value="19">19</option>
							<option value="20">20</option>
							<option value="21">21</option>
							<option value="22">22</option>
							<option value="23">23</option>
							<option value="24">24</option>
							<option value="25">25</option>
							<option value="26">26</option>
							<option value="27">27</option>
							<option value="28">28</option>
							<option value="29">29</option>
							<option value="30">30</option>
							</select>
							&nbsp;{#winPeriode_semaine#}&nbsp;{#winPeriode_repeter_jusque#}&nbsp;
							{if $smarty.session.isMobileOrTablet==1}
								<input type="date" class="form-control" id="dateFinRepetitionSemaine" value="" size="11" maxlength="10" onFocus="remplirDateRepetition(this.id);" tabindex="18">
							{else}
								<input type="text" class="form-control datepicker" id="dateFinRepetitionSemaine" value="" size="11" maxlength="10" onFocus="remplirDateRepetition(this.id);" tabindex="18">
							{/if}
						</div>
						<div id="divOptionsRepetitionJS" class="d-none form-group form-inline">
							<label class="col-form-label">{#winPeriode_repeter_jourderepetition#} :&nbsp;&nbsp;&nbsp;&nbsp;</label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="jourSemaine" id="jourSemaine1" value="1" checked="checked">
								<label class="form-check-label" for="jourSemaine1">{#initial_day_1#}</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="jourSemaine" id="jourSemaine2" value="2">
								<label class="form-check-label" for="jourSemaine2">{#initial_day_2#}</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="jourSemaine" id="jourSemaine3" value="3">
								<label class="form-check-label" for="jourSemaine3">{#initial_day_3#}</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="jourSemaine" id="jourSemaine4" value="4">
								<label class="form-check-label" for="jourSemaine4">{#initial_day_4#}</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="jourSemaine" id="jourSemaine5" value="5">
								<label class="form-check-label" for="jourSemaine5">{#initial_day_5#}</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="jourSemaine" id="jourSemaine6" value="6">
								<label class="form-check-label" for="jourSemaine6">{#initial_day_6#}</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="jourSemaine" id="jourSemaine0" value="0">
								<label class="form-check-label" for="jourSemaine0">{#initial_day_0#}</label>
							</div>
						</div>
						<div id="divOptionsRepetitionMois" class="d-none form-group form-inline" tabindex="18">
							{#winPeriode_repeter_tousles#}&nbsp;
							<select name='nbRepetitionMois' id='nbRepetitionMois' class="form-control">
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							<option value="13">13</option>
							<option value="14">14</option>
							<option value="15">15</option>
							<option value="16">16</option>
							<option value="17">17</option>
							<option value="18">18</option>
							<option value="19">19</option>
							<option value="20">20</option>
							<option value="21">21</option>
							<option value="22">22</option>
							<option value="23">23</option>
							<option value="24">24</option>
							<option value="25">25</option>
							<option value="26">26</option>
							<option value="27">27</option>
							<option value="28">28</option>
							<option value="29">29</option>
							<option value="30">30</option>
							</select>
							&nbsp;{#winPeriode_mois#}&nbsp;{#winPeriode_repeter_jusque#}&nbsp;
							{if $smarty.session.isMobileOrTablet==1}
								<input type="date" class="form-control" id="dateFinRepetitionMois" value="" size="11" maxlength="10" onFocus="remplirDateRepetition(this.id);" tabindex="18">
							{else}
								<input type="text" class="form-control datepicker" id="dateFinRepetitionMois" value="" size="11" maxlength="10" onFocus="remplirDateRepetition(this.id);" tabindex="18">
							{/if}
							</div>
							<div id="divOptionsjourderepetition" class="d-none form-group form-inline">
							<label class="col-form-label">{#winPeriode_repeter_jourderepetition#} :&nbsp;&nbsp;&nbsp;</label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="radioChoixJourRepetition" id="radioChoixJourRepetition" value="0" checked="checked">
								<label class="form-check-label" for="radioChoixJourRepetition">{#winPeriode_repeter_jourderepetition_jourmois#}</label>
							</div>
							</div>	
							<div id="divExceptionRepetition" class="form-group form-inline d-none" tabindex="19">
							<label class="col-form-label">{#winPeriode_repeter_exception_siferie#} :&nbsp;&nbsp;&nbsp;</label>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="exceptionRepetition" id="exceptionRepetition1" value="1" checked="checked">
								<label class="form-check-label" for="exceptionRepetition1">{#winPeriode_repeter_exception_decaler#}</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="exceptionRepetition" id="exceptionRepetition2" value="2">
								<label class="form-check-label" for="exceptionRepetition2">{#winPeriode_repeter_exception_pasajout#}</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="exceptionRepetition" id="exceptionRepetition3" value="3">
								<label class="form-check-label" for="exceptionRepetition3">{#winPeriode_repeter_exception_ajout#}</label>
							</div>
						</div>
					</div>
			{else}
					<label class="col-md-2 col-form-label">{#winPeriode_repeter#} :</label>
					<div class="col-md-10 col-form-label">
						<b>{#winPeriode_recurrente#}{$prochaineOccurence|sqldate2userdate}</b>
					</div>
					<input type="hidden" name="repetition" id="repetition" value="" />
					<input type="hidden" name="dateFinRepetitionJour" id="dateFinRepetitionJour" value="" />
					<input type="hidden" name="dateFinRepetitionSemaine" id="dateFinRepetitionSemaine" value="" />
					<input type="hidden" name="dateFinRepetitionMois" id="dateFinRepetitionMois" value="" />
					<input type="hidden" name="nbRepetitionJour" id="nbRepetitionJour" value="" />
					<input type="hidden" name="nbRepetitionSemaine" id="nbRepetitionSemaine" value="" />
					<input type="hidden" name="nbRepetitionMois" id="nbRepetitionMois" value="" />
					<input type="hidden" name="jourSemaine" id="jourSemaine" value="" />
			{/if}
		</div>
		<div class='col-md-12'><hr /></div>
		<div class="form-group row col-md-12">
			<label class="col-md-2 col-form-label">{#winPeriode_statut#}:</label>
			<div class="col-md-4">
				<select name="statut_tache" id="statut_tache" class="form-control" tabindex="19">
				{foreach from=$listeStatus item=status}
					<option value="{$status.status_id}" {if (isset($periode.statut_tache) and $periode.statut_tache eq $status.status_id) or (!isset($periode.statut_tache) and $defaut_status eq $status.status_id)}selected="selected"{/if}>{$status.nom}</option>
				{/foreach}
				</select>
			</div>
			<label class="col-md-2 col-form-label">{#winPeriode_livrable#} :</label>
			<div class="col-md-4" >
				<select name="livrable" id="livrable" class="form-control" tabindex="20">
					<option value="oui" {if $periode.livrable eq "oui"}selected="selected"{/if}>{#oui#}</option>
					<option value="non" {if $periode.livrable eq "non"}selected="selected"{/if}>{#non#}</option>
				</select>
			</div>
		</div>
		<div class="form-group row col-md-12">
		{if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX == 1 }
			<label class="col-md-2 col-form-label">{#winPeriode_lieu#} :</label>
				<div class="col-md-4">
					<select name="lieu" id="lieu" class="form-control {if $smarty.session.isMobileOrTablet!=1}select2{/if}" tabindex="19" style="width:100%" >
						<option value=""></option>
						{foreach from=$listeLieux item=lieuTmp}
							<option value="{$lieuTmp.lieu_id}" {if $periode.lieu_id eq $lieuTmp.lieu_id} selected="selected" {/if}>{$lieuTmp.nom|xss_protect}</option>
						{/foreach}
					</select>
				</div>
		{/if}
		{if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES == 1 }
			<div class="col-md-2 col-form-label">{#winPeriode_ressource#} :</div>
				<div class="col-md-4">
					<select name="ressource" id="ressource" class="form-control {if $smarty.session.isMobileOrTablet!=1}select2{/if}" tabindex="20" style="width:100%" >
						<option value=""></option>
						{foreach from=$listeRessources item=ressourceTmp}
							<option value="{$ressourceTmp.ressource_id}" {if $periode.ressource_id eq $ressourceTmp.ressource_id} selected="selected" {/if}>{$ressourceTmp.nom|xss_protect}</option>
						{/foreach}
					</select>
				</div>
		{/if}
		{if $smarty.const.CONFIG_SOPLANNING_OPTION_LIEUX == 0 }
		<input type="hidden" name="lieu" id="lieu" value="">
		{/if}
		{if $smarty.const.CONFIG_SOPLANNING_OPTION_RESSOURCES == 0 }
		<input type="hidden" name="ressource" id="ressource" value="">
		{/if}
		</div>
		<div class='col-md-12'><hr /></div>

		<div class="form-group row col-md-12">
			<label class="col-md-2 col-form-label">{#winPeriode_lien#} :</label>
			<div class="col-md-4 form-inline align-items-start">
				<input {if $smarty.session.isMobileOrTablet==1}type="url"{else}type="text"{/if} class="form-control {if $periode.lien neq ""}col-md-10 input-withicon{else}col-md-12{/if}" name="lien" id="lien" maxlength="2000" value="" tabindex="22" />
				{if $periode.lien neq ""}
					<span title='{#winPeriode_gotoLien#|xss_protect}' onclick="window.open('{if ($periode.lien|strpos:"http" !== FALSE || $periode.lien|strpos:"ftp" !== FALSE) && $periode.lien|strpos:"\\" !== FALSE}http://{/if}'+document.getElementById('lien').value, '_blank')" target="_blank" class="btn btn-default tooltipster ml-1"><i class="fa fa-share-square-o" aria-hidden="true"></i></span>
				{/if}
			</div>
			<label class="col-md-2 col-form-label">{#winPeriode_fichier#} :</label>
			<div class="col-md-4 form-inline">
				<form enctype="multipart/form-data" id="fichier_form">
					<span id="file-select-button" class="col-form-label"><i class="fa fa-plus-square-o" aria-hidden="true"></i>&nbsp;
						{#upload_fichier_joindre#}<img id="divPatienter2" src="assets/img/pictos/loading16.gif" class="d-none ml-2" alt="" />
					</span>					
					<input name="fichier" id="fichier" type="file" style="float:left;width:220px;display:none;" />
					{foreach from=$fichiers item=fichier}
						<div>
							<a href="upload/files/{$link_id}/{$fichier}" target="_blank" class="ellipsis fileupload" id="fichier_periode" style="float:left;">{$fichier}</a>
							&nbsp;<i class="fa fa-trash fa-fw" aria-hidden="true" onclick="fileRemove('{$fichier}',this.closest('div'));" id="fileremovebutton" style="margin-top:4px;margin-left:4px;float:left;cursor:pointer;"></i>
						</div>
					{/foreach}
					<a id="lastfile"></a>
				</form>
				<input type="hidden" name="liste_fichiers" id="liste_fichiers" value="{$periode.fichiers}">
				<input type="hidden" name="suppression_upload" id="suppression_upload" value="{#upload_fichier_suppression#}">
				<input type="hidden" name="max_size_upload" id="max_size_upload" value="{$smarty.const.MAX_SIZE_UPLOAD}">				
				<input type="hidden" name="max_size_upload_error" id="max_size_upload_error" value="{#upload_fichier_erreur_taille#}">				
			</div>
		</div>
		<div class="form-group row col-md-12">
			<label class="col-md-2 col-form-label">{#winPeriode_commentaires#} :</label>
			<div class="col-md-10">
				<textarea class="form-control" rows="1" id="notes" name="notes" tabindex="23" style="min-height:50px;max-height:200px"></textarea>
			</div>
		</div>
		<div class="form-group row col-md-12">
			<label class="col-md-2 col-form-label">{#winPeriode_custom#} :</label>
			<div class="col-md-10">
				<input type="text" class="form-control float-left input-withicon" name="custom" id="custom" maxlength="255" value="{$periode.custom|xss_protect}" tabindex="23" />
				<div title='{#winPeriode_custom_aide#|xss_protect}' class="glyphicon glyphicon-question-sign cursor-help small tooltipster ml-2"></div>
			</div>
		</div>
		{if !isset($projet) || in_array("projects_manage_all", $user.tabDroits) || (in_array("tasks_modify_own_project", $user.tabDroits) && isset($projet) && $user.user_id eq $projet.createur_id) || in_array("tasks_modify_all", $user.tabDroits) || (in_array("tasks_modify_own_task", $user.tabDroits) && $periode.user_id eq $user.user_id)}
			{assign var=buttonSubmitTache value=1}
		{else}
			{assign var=buttonSubmitTache value=0}
		{/if}

		<div id="divSubmitPeriode" class="form-group row col-md-12 justify-content-end {if $buttonSubmitTache eq 0}d-none{/if}">
			{if $smarty.const.CONFIG_SMTP_HOST neq ''}
				<div class="form-check form-check-inline">
						<input class="form-check-input" type="checkbox" id="notif_email" checked="checked">
						<label class="form-check-label" for="notif_email" style="font-weight:normal" class="padding-right-25">{#winPeriode_notif_email#}</label>
				</div>
			{else}
				<input type="hidden" id="notif_email" value="false">
			{/if}

			<div class="btn-group" role="group">
				<button type="button" id="butSubmitPeriode" class="btn btn-primary" tabindex="24" onClick="$('#divPatienter').removeClass('d-none');this.disabled=true;users_ids=getSelectValue('user_id2');xajax_submitFormPeriode('{$periode.periode_id}', $('#projet_id').val(), users_ids, $('#date_debut').val(), $('#conserver_duree').is(':checked'), $('#date_fin').val(), $('#nb_jours').val(), $('#duree').val(), $('#heure_debut').val(), $('#heure_fin').val(), $('#matin').is(':checked'), $('#apresmidi').is(':checked'), $('#repetition option:selected').val(), $('#dateFinRepetitionJour').val(),$('#dateFinRepetitionSemaine').val(),$('#dateFinRepetitionMois').val(), $('#nbRepetitionJour option:selected').val(),$('#nbRepetitionSemaine option:selected').val(),$('#nbRepetitionMois option:selected').val(),getRadioValue('jourSemaine'),getRadioValue('exceptionRepetition'),$('#appliquerATous').is(':checked'), $('#statut_tache').val(),$('#lieu option:selected').val(), $('#ressource option:selected').val(), $('#livrable').val(), $('#titre').val(), $('#notes').val(), $('#lien').val(), $('#custom').val(), $('#liste_fichiers').val(), $('#link_id').val(), $('#notif_email').is(':checked'), $('#updateoccurrences').val());">{#winPeriode_valider#|xss_protect}</button>
				{if $periode.periode_id neq 0}
					<button type="button" class="btn btn-warning" onClick="if(confirm('{#winPeriode_confirmSuppr#|xss_protect}'))xajax_supprimerPeriode({$periode.periode_id}, false, $('#notif_email').is(':checked'));undefined;">{#winPeriode_supprimer#}</button>
				{/if}
				{if $periode.periode_id neq 0}
					<button class="btn dropdown-toggle btn-default" data-toggle="dropdown" onclick="javascript:multiselecthide();"><span class="d-none d-md-inline-block">&nbsp;&nbsp;{#periode_plus#}</span>&nbsp;<span class="caret"></span></button>
					<div class="dropdown-menu" style="">
						<a class="dropdown-item" href="javascript:if(confirm('{#winPeriode_dupliquer#|xss_protect} ?'))jQuery('#myBigModal').modal('toggle');xajax_ajoutPeriode('', '', {$periode.periode_id});undefined;"><i class="fa fa-fw fa-copy" aria-hidden="true"></i> {#winPeriode_dupliquer#|xss_protect}</a>
						{if $periode.date_fin neq ""}
							<a class="dropdown-item" href="javascript:xajax_periode_scinder_form({$periode.periode_id});undefined;"><i class="fa fa-fw fa-cut" aria-hidden="true"></i> {#periode_scinder#|xss_protect}</a>
						{/if}
						{if $audit_id neq ''}
							<a href="javascript:xajax_modifAudit('{$audit_id}');undefined;" class="dropdown-item" ><i class="fa fa-history fa-lg fa-fw" aria-hidden="true"></i>&nbsp;&nbsp;{#audit_restaurer#}</a>
						{/if}
						{if isset($estFilleOuParente)}
							<a class="dropdown-item" href="javascript:if(confirm('{#winPeriode_confirmSupprRepetition#|xss_protect}'))xajax_supprimerPeriode({$periode.periode_id}, true, $('#notif_email').is(':checked'));undefined;"><i class="fa fa-fw fa-remove" aria-hidden="true"></i> {#winPeriode_supprimer_repetition#|xss_protect}</a>
							<a class="dropdown-item" href="javascript:if(confirm('{#winPeriode_confirmSupprRepetition#|xss_protect}'))xajax_supprimerPeriode({$periode.periode_id}, 'avant', $('#notif_email').is(':checked'));undefined;"><i class="fa fa-fw fa-remove" aria-hidden="true"></i> {#winPeriode_supprimer_repetition_avant#|xss_protect}</a>
							<a class="dropdown-item" href="javascript:if(confirm('{#winPeriode_confirmSupprRepetition#|xss_protect}'))xajax_supprimerPeriode({$periode.periode_id}, 'apres', $('#notif_email').is(':checked'));undefined;"><i class="fa fa-fw fa-remove" aria-hidden="true"></i> {#winPeriode_supprimer_repetition_apres#|xss_protect}</a>
						{/if}
					</div>
				{/if}
			</div>
			<div id="divPatienter" class="d-none justify-content-end form-group" style="position:absolute;right:0;"><img src="assets/img/pictos/loading16.gif" alt="" /></div>
		</div>
</form>
<script>
	{literal}
	
	$('.tooltipster').tooltip({
		html: true,
		placement: 'auto',
		boundary: 'window'
	});

	function file_upload_click()
	{
		$('input[type=file]').click();
	}
	
	$('#file-select-button').click(function(){
		file_upload_click();
	});

	$('input[type=file]').change(function() { 
		fileUpload(); 
	});
	
	$("#myBigModal").on("hidden.bs.modal", function () {
	 var periode_id=$('#periode_id').val();
	 var fichiers=$('#fichiers').val();
	 var linkid=$('#link_id').val();	 
	 if (periode_id == 0 && fichiers!="" )
	 {
			var formData = new FormData();
			formData.append('linkid', linkid);
			formData.append('type', 'deletenew');
			formData.append('fichiers', fichiers);
			formData.append('periodeid', 0);
			$.ajax({
				url			: 'process/upload.php',
				cache       : false,
				contentType : false,
				processData : false,
				data        : formData,                         
				type        : 'post'
			});
	 }
	});

	{/literal}
</script>