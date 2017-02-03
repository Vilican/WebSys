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

if ($_POST["page"] == null or $_POST["post_id"] == null) {
	die("5");
}

$post = $mysql->query("SELECT `posts`.*, `users`.`id`, `users`.`username`, `roles`.`color`, `roles`.`level` FROM `posts` LEFT JOIN `users` ON `posts`.`author` = `users`.`id` LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `location` = ". $mysql->quote($_POST["page"]) ." AND `deleted` = 0 AND `post_id` = ". $mysql->quote($_POST["post_id"]));
if ($post->num_rows > 0) {
	$post = $post->fetch_assoc();
	if (!(($_SESSION["access_posts_edit"] > 0 and $_SESSION["level"] > $post["level"]) or $page["author"] == $_SESSION["id"] or (isset($_SESSION["id"]) and $_SESSION["id"] == 0) or ($post["id"] == $_SESSION["id"] and strtotime($post["time"]) + $sys["authoredittime"] + 60 > time()))) {
		die("8");
	}
} else {
	die("7");
}

if ($_SESSION["access_nocaptcha"] == 0) {
	
	if (strtolower($_SESSION['captcha']) != strtolower($_POST["captcha"])) {
		die("2");
	}
	
} else {
	
	if (!validate_csrf($_POST["csrf"])) {
		die("6");
	}
	
}

if ($post["username"] == null) {
	
	if (strlen($_POST["name"]) == 0) {
		die("0");
	}
	
	if (strlen($_POST["name"]) > 24) {
		die("1");
	}
	
}

if (strlen($_POST["post"]) == 0) {
	die("3");
}

if (strlen($_POST["post"]) > 1024) {
	die("4");
}

if ($post["username"] != null) {

	$mysql->query("UPDATE `posts` SET `content` = ". $purifier->purify($mysql->quote($_POST["post"])) ." WHERE `location` = ". $mysql->quote($_POST["page"]) ." AND `deleted` = 0 AND `post_id` = ". $mysql->quote($_POST["post_id"]));
	
} else {
	
	$mysql->query("UPDATE `posts` SET `content` = ". $purifier->purify($mysql->quote($_POST["post"])) .", `anon_author` = ". $purifier->purify($mysql->quote($_POST["name"])) ." WHERE `location` = ". $mysql->quote($_POST["page"]) ." AND `deleted` = 0 AND `post_id` = ". $mysql->quote($_POST["post_id"]));
	
}

die("9");

?>