<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

require(BASE .'/../vendor/phpqrcode/qrlib.php');

QRcode::png(CONFIG_SOPLANNING_URL);

?>