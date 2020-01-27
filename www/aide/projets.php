<?php

require_once('./base.inc');
require_once(BASE . '/../config.inc');

$smarty = new MySmarty();

require BASE . '/../includes/header.inc';

$smarty->assign('xajax', $xajax->getJavascript("", BASE . "/assets/js/xajax.js"));

$smarty->configLoad('aide/' . $lang . '.txt');

$smarty->display('aide/projets.tpl');

?>