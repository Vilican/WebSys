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

$page["content"] .= '<div class="popup-gallery">';

foreach ($files as $value) {
	$page["content"] .= '<a href="'. $target . $value .'"><img src="'. $target . $value .'" class="gal-mini"></a>
';
}

$page["content"] .= '</div>';