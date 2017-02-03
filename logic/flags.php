<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

require "../config.php";
require "../core/database.php";
require "../core/functions.php";

$mysql = new DB();
if (!$mysql->connect(_SRV, _USR, _PW, _DB)) {
	throw_error("Připojení k databázi se nepodařilo.<br>Prosím zkontrolujte nastavení v config.php");
}

$settings = $mysql->query("SELECT * FROM `settings`");
while($setting = $settings->fetch_assoc()) {
	$sys[$setting["setting"]] = $setting["value"];
}

if (!validate_csrf($_POST["csrf"])) {
	die("0");
}

if ($_POST["data"] == "reject") {
	$post_flags = $mysql->query("SELECT `users`.`id` FROM `flags` INNER JOIN `users` ON `flags`.`user` = `users`.`id` WHERE `post` = ". $mysql->quote($_POST["post"]) .";");
	if ($post_flags->num_rows > 0) {
		while ($flag = $post_flags->fetch_assoc()) {
			$users[] = $flag["id"];
		}
	}
	$mysql->query("UPDATE `users` SET `flags_incorrect` = `flags_incorrect` + 1 WHERE `id` IN (". implode(',', array_map('intval', $users)) .");");
	$mysql->query("DELETE FROM `flags` WHERE `post` = ". $mysql->quote($_POST["post"]) .";");
	die("ok");
}

if (is_numeric($_POST["data"])) {
	$post_flags = $mysql->query("SELECT `users`.`id` FROM `flags` INNER JOIN `users` ON `flags`.`user` = `users`.`id` WHERE `post` = ". $mysql->quote($_POST["post"]) ." AND `reason` = ". $mysql->quote($_POST["data"]) .";");
	if ($post_flags->num_rows > 0) {
		while ($flag = $post_flags->fetch_assoc()) {
			$users[] = $flag["id"];
		}
	}
	$mysql->query("UPDATE `users` SET `flags_correct` = `flags_correct` + 1 WHERE `id` IN (". implode(',', array_map('intval', $users)) .");");
	$mysql->query("DELETE FROM `flags` WHERE `post` = ". $mysql->quote($_POST["post"]) ." AND `reason` = ". $mysql->quote($_POST["data"]) .";");
	die("ok");
}

die("err");

?>