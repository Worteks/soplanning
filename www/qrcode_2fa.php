<?php

require('./base.inc');
require(BASE .'/../config.inc');
require(BASE .'/../includes/header.inc');

require(BASE .'/../vendor/phpqrcode/qrlib.php');

$chaine = 'otpauth://totp/' . $user->user_id . '@' . str_replace(' ', '-', substr(CONFIG_SOPLANNING_TITLE, 0, 30)) . '?secret=' . $user->cle;

require_once BASE .'/../vendor/sonata-project/google-authenticator/src/FixedBitNotation.php';
require_once BASE .'/../vendor/sonata-project/google-authenticator/src/GoogleAuthenticator.php';
require_once BASE .'/../vendor/sonata-project/google-authenticator/src/GoogleQrUrl.php';

use Google\Authenticator\GoogleAuthenticator;

$g = new \Google\Authenticator\GoogleAuthenticator();
$chaine = $g->getQRCode($user->user_id, cleanStr(CONFIG_SOPLANNING_TITLE), $user->cle);
//echo $chaine;die;
QRcode::png($chaine);

?>