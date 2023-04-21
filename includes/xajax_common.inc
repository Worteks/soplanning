<?php
require_once (BASE . '/../includes/xajax.inc');
$xajax = new xajax(BASE . '/process/xajax_server.php');
$xajax->setCharEncoding('ISO-8859-1');
$xajax->decodeUTF8InputOn();
$xajax->errorHandlerOn();
$xajax->waitCursorOff();
//$xajax->statusMessagesOn();
//$xajax->debugOn();
$xajax->setLogFile(BASE . '/../xajax_errors.log');

$xajax->registerFunction('contact');
$xajax->registerFunction('ajoutProjet');
$xajax->registerFunction('modifProjet');
$xajax->registerFunction('submitFormProjet');
$xajax->registerFunction('supprimerProjet');
$xajax->registerFunction('checkProjetId');
$xajax->registerFunction('checkRessourceId');
$xajax->registerFunction('checkLieuId');
$xajax->registerFunction('checkCategorieId');
$xajax->registerFunction('ajoutPeriode');
$xajax->registerFunction('modifPeriode');
$xajax->registerFunction('submitFormPeriode');
$xajax->registerFunction('moveCasePeriode');
$xajax->registerFunction('checkAvailableVersion');
$xajax->registerFunction('choixPDF');
$xajax->registerFunction('modifUser');
$xajax->registerFunction('submitFormUser');
$xajax->registerFunction('supprimerUser');
$xajax->registerFunction('modifProfil');
$xajax->registerFunction('submitFormProfil');
$xajax->registerFunction('changerPwd');
$xajax->registerFunction('nouveauPwd');
$xajax->registerFunction('supprimerPeriode');
$xajax->registerFunction('modifFerie');
$xajax->registerFunction('submitFormFerie');
$xajax->registerFunction('supprimerFerie');
$xajax->registerFunction('choixIcal');
$xajax->registerFunction('modifUserGroupe');
$xajax->registerFunction('submitFormUserGroupe');
$xajax->registerFunction('supprimerUserGroupe');
$xajax->registerFunction('autocompleteTitreTache');
$xajax->registerFunction('submitFormContact');
$xajax->registerFunction('modifLieu');
$xajax->registerFunction('submitFormLieu');
$xajax->registerFunction('supprimerLieu');
$xajax->registerFunction('modifRessource');
$xajax->registerFunction('submitFormRessource');
$xajax->registerFunction('supprimerRessource');
$xajax->registerFunction('modifStatus');
$xajax->registerFunction('submitFormStatus');
$xajax->registerFunction('supprimerStatus');
$xajax->registerFunction('icalGenererLien');
$xajax->registerFunction('usersBulkRightsForm');
$xajax->registerFunction('usersBulkRightsSubmit');
$xajax->registerFunction('periode_change_projet');
$xajax->registerFunction('purgerAudit');
$xajax->registerFunction('modifAudit');
$xajax->registerFunction('restaureAudit');
$xajax->registerFunction('projet_decalage_form');
$xajax->registerFunction('projet_decalage_submit');

$xajax->registerFunction('periode_scinder_form');
$xajax->registerFunction('periode_scinder_submit');

$xajax->registerFunction('google_2fa_check_code');

$xajax->registerFunction('projet_copie_form');
$xajax->registerFunction('projet_copie_submit');

$xajax->registerFunction('selection_multi_tache_form');
$xajax->registerFunction('selection_multi_tache_suppr');

$xajax->registerFunction('tutoriel_masquer');

?>