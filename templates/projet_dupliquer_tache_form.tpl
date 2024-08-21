{* Smarty *}
<form method="post" action="" target="_blank" onsubmit="return false;">
    <div class="form-group row col-md-12 align-items-center">
        <label class="col-md-4 col-form-label">{#tab_projet#} :</label>
        <div class="col-md-7">
            {$projet.nom} ({$projet.projet_id})
        </div>
    </div>
    <div class="form-group row col-md-12 align-items-center">
        <label class="col-md-4 col-form-label">{#projet_dupliquer_tache_a_partir_de#}</label>
        <div class="col-md-6">
            {if $smarty.session.isMobileOrTablet==1}
                <input type="date" class="form-control" name="date_debut_copie" id="date_debut_copie" value="" style="display:inline;block" autocomplete="off" />
            {else}
                <input type="text" class="form-control datepicker" name="date_debut_copie" id="date_debut_copie" value="" style="display:inline-block" autocomplete="off" />
            {/if}
        </div>
    </div>
    <div class="form-group row col-md-12 align-items-center">
        <label class="col-md-4 col-form-label">{#projet_dupliquer_tache_jusqua#}</label>
        <div class="col-md-6">
            {if $smarty.session.isMobileOrTablet==1}
                <input type="date" class="form-control" name="date_fin_copie" id="date_fin_copie" value="" style="display:inline;block" autocomplete="off" />
            {else}
                <input type="text" class="form-control datepicker" name="date_fin_copie" id="date_fin_copie" value="" style="display:inline-block" autocomplete="off" />
            {/if}
        </div>
    </div>
    <div class="form-group row col-md-12 align-items-center">
        <label class="col-md-4 col-form-label">{#projet_dupliquer_tache_demarrer#} :</label>
        <div class="col-md-8 radio-inline">
            {if $smarty.session.isMobileOrTablet==1}
                <input type="date" class="form-control" name="date_demarrage" id="date_demarrage" value="" style="display:inline;block" autocomplete="off" />
            {else}
                <input type="text" class="form-control datepicker" name="date_demarrage" id="date_demarrage" value="" style="display:inline-block" autocomplete="off" />
            {/if}
        </div>
    </div>
    <div class="form-group row col-md-12 align-items-center">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <br />
            <input id="butSubmitCopie" type="button" class="btn btn-primary" value="{#enregistrer#|escape:"html"}" onclick="$('#divPatienter').removeClass('d-none');/*this.disabled=true*/; xajax_projet_dupliquer_tache_submit('{$projet.projet_id}', $('#date_debut_copie').val(), $('#date_fin_copie').val(), $('#date_demarrage').val());"/>
            <div id="divPatienter" class="d-none" style="margin-left:20px;display:inline-block"><img src="assets/img/pictos/loading16.gif" alt="" /></div>
        </div>
	</div>
</form>

<script>
    {literal}
    $('.tooltipster').tooltip({
        html: true,
        placement: 'auto',
        boundary: 'window'
    });
    {/literal}
</script>

