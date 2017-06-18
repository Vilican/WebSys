<?php

require "config.php";
require "database.php";

$settings = $mysql->query("SELECT * FROM `settings`");
while($setting = $settings->fetch_assoc()) {
	$sys[$setting["setting"]] = $setting["value"];
}

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if ($sys["stricthttps"]) { ini_set('session.cookie_secure', 1); }
session_start();

require "functions.php";

$page_types = array(1 => 'stránka', 2 => 'diskuze', 3 => 'fórum', 4 => 'galerie');

if (isset($_SESSION["id"])) {
	$mysql->update_activity($_SESSION["id"]);
	$user = $mysql->query("SELECT * FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `id`= ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();
	unset($user["hash"]);
	unset($user["lastact"]);
	unset($user["blocked"]);
	unset($user["deleted"]);
	session_regenerate_id(true);				
	$_SESSION = $user;
}

if ($sys["license"] == 2 and $sys["whitelabel"] == 1) {
	$unbranded = true;
}