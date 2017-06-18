<?php

if (!defined("_PW")) {
	die();
}

if (!empty($page["content"])) {
	$page["content"] .= '<hr>';
}

$galery = true;
$target = "upload/". $page["id"] ."/";
$files = array_diff(scandir($target), array('.', '..'));

foreach ($files as $value) {
	$page["content"] .= '<a class="fancybox" rel="galerie" href="'. $target . $value .'"><img data-src="'. $target . $value .'" class="gal-mini"></a>
';
}