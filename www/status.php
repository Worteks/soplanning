<?php

require('./base.inc');
require(BASE . '/../config.inc');

$smarty = new MySmarty();

require BASE . '/../includes/header.inc';

$status = new GCollection('Status');
$status->db_load(array(), array('priorite' => 'ASC'));
$smarty->assign('status', $status->getSmartyData());
$smarty->assign('xajax', $xajax->getJavascript("", "assets/js/xajax.js"));
$smarty->display('www_status.tpl');
?>