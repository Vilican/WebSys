<?php

if ($_GET["t"] != "kofl") {
	die();
}

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

$image = @imagecreatetruecolor(160, 45) or die("Error: Cannot Initialize new GD image stream!");
$background = imagecolorallocate($image, 0xE6, 0xE6, 0xE6);
imagefill($image, 0, 0, $background);
$linecolor = imagecolorallocate($image, 0x00, 0x99, 0xCC);
$linecolor2 = imagecolorallocate($image, 0x99, 0x66, 0x33);
$textcolor1 = imagecolorallocate($image, 0x00, 0x66, 0x00);
$textcolor2 = imagecolorallocate($image, 0x00, 0x00, 0xCC);
$textcolor3 = imagecolorallocate($image, 0xCC, 0x00, 0x00);
  
for($i=0; $i < 6; $i++) {
	imagesetthickness($image, mt_rand(1,3));
	imageline($image, mt_rand(0,120), 0, mt_rand(0,120), 45, (mt_rand() % 2) ? $linecolor : $linecolor2);
}

$fonts = array();
$fonts[] = "../template/fonts/UbuntuMono-B.ttf";
$fonts[] = "../template/fonts/Ubuntu-B.ttf";

$chars = '';  
$characters = 'ABCDEFGHIJKLMNPQRSTUVWXYZ';

if (!isset($_SESSION["id"])) {
	$step = 30;
} else {
	$step = 50;
}

for($x = 10; $x <= 130; $x += $step) {
	$num = mt_rand(1,3000);
	if($num % 3 == 0) { $textcolor = $textcolor1; }
	if($num % 3 == 1) { $textcolor = $textcolor2; }
	if($num % 3 == 2) { $textcolor = $textcolor3; }
	$chars .= ($char = $characters[mt_rand(0, strlen($characters) - 1)]);
	imagettftext($image, mt_rand(18,20), mt_rand(-25,25), $x, mt_rand(25, 35), $textcolor, $fonts[array_rand($fonts)], $char);
}

$_SESSION['captcha'] = strtolower($chars);

header('Content-type: image/png');
imagepng($image);
imagedestroy($image);