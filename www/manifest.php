<?php
require_once('./base.inc');
require_once(BASE . '/../config.inc');

$urlSuggeree = getUrlInfo();
$pathCourant =  $urlSuggeree['root'] . $urlSuggeree['currentDir'];

$icon = BASE . '/upload/logo/icon.png';
echo '
{
	"name": "' . CONFIG_SOPLANNING_TITLE . '",
	"short_name": "' . substr(CONFIG_SOPLANNING_TITLE, 0, 10) . '",
	"icons": [
	{
	    "src": "' . BASE . '/android-chrome-192x192.png",
	    "sizes": "192x192",
	    "type": "image/png"
	},
	{
	    "src": "' . BASE . '/android-chrome-512x512.png",
	    "sizes": "512x512",
	    "type": "image/png"
	},
	{
	    "src": "' . BASE . '/favicon-32x32.png",
	    "sizes": "32x32",
	    "type": "image/png"
	},
	{
	    "src": "' . (is_file($icon) ? $icon : '/soplanning-carre.png') . '",
	    "sizes": "48x48",
	    "type": "image/png"
	}
	],
	"theme_color": "#9fcd3a",
	"background_color": "#ffffff",
	"display": "standalone",
	"scope": "' . (CONFIG_SOPLANNING_URL != '' ? CONFIG_SOPLANNING_URL : $pathCourant) . '",
	"start_url": "' . (CONFIG_SOPLANNING_URL != '' ? CONFIG_SOPLANNING_URL : $pathCourant) . '"
}
';