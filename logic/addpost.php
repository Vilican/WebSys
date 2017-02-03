<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

require "../config.php";
require "../core/database.php";
require "../core/functions.php";

require '../lib/htmlpurifer/HTMLPurifier.auto.php';
$purifier = new HTMLPurifier();

$mysql = new DB();
if (!$mysql->connect(_SRV, _USR, _PW, _DB)) {
	throw_error("Připojení k databázi se nepodařilo.<br>Prosím zkontrolujte nastavení v config.php");
}

$settings = $mysql->query("SELECT * FROM `settings`");
while($setting = $settings->fetch_assoc()) {
	$sys[$setting["setting"]] = $setting["value"];
}

if (!isset($_SESSION["id"])) {
	
	if (strlen($_POST["name"]) == 0) {
		die("0");
	}
	
	if (strlen($_POST["name"]) > 24) {
		die("1");
	}
	
	if (strtolower($_SESSION['captcha']) != strtolower($_POST["captcha"])) {
		die("2");
	}
	
	$anon_author = $mysql->quote($_POST["name"]);
	$author = "NULL";
	
} elseif ($_SESSION["access_nocaptcha"] == 0) {
	
	if (strtolower($_SESSION['captcha']) != strtolower($_POST["captcha"])) {
		die("2");
	}
	
	$anon_author = "NULL";
	$author = $_SESSION["id"];
	
} else {
	
	$anon_author = "NULL";
	$author = $_SESSION["id"];
	
	if (!validate_csrf($_POST["csrf"])) {
		die("6");
	}
	
}

if (strlen($_POST["post"]) == 0) {
	die("3");
}

if (strlen($_POST["post"]) > 1024) {
	die("4");
}

if ($_POST["page"] == null) {
	die("5");
}


$location = $mysql->query("SELECT `param1` FROM `pages` WHERE `id` = ". $mysql->quote($_POST["page"]) .";");

if ($location->num_rows > 0) {

	$location = $location->fetch_assoc();
	
	if (!(($page["param1"] == 0 and $sys["anonymousposts"] == 1) or ($page["param1"] <= $_SESSION["level"] and $_SESSION["access_addpost"] > 0))) {
		die("5");
	}
	
} else {
	die("5");
}

$mysql->query("INSERT INTO `posts` (`location`, `author`, `anon_author`, `content`) VALUES (". $mysql->quote($_POST["page"]) .", ". $author .", ". $purifier->purify($anon_author) .", ". $purifier->purify($mysql->quote($_POST["post"])) .");");

die("9");

?>