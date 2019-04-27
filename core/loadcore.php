<?php

require "config.php";
require "database.php";

if (file_exists("setup/")) {
	throw_error("Adresář setup pro instalaci stále existuje. Smažte ho.");
}

$settings = $mysql->query("SELECT * FROM `settings`");
while ($setting = $settings->fetch_assoc()) {
	$sys[$setting["setting"]] = $setting["value"];
}

if ((empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] !== "on") && $sys["stricthttps"] == 1) {
	header("Location: https://". $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	exit;
}

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if ($sys["stricthttps"]) { ini_set('session.cookie_secure', 1); }
session_start();

if ($sys["restrictorigin"] == 1) {
	header("Referrer-Policy: no-referrer");
}

header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

require "functions.php";

$page_types = array(1 => 'stránka', 2 => 'diskuze', 3 => 'fórum', 4 => 'galerie', 5 => 'kategorie');

if (isset($_SESSION["id"])) {
    if ($_SESSION["timestamp"] < (time()-1200)) {
        session_unset();
        session_destroy();
        header("Location: index.php?p=login");
        exit;
    }
    $_SESSION["timestamp"] = time();
	$mysql->update_activity($_SESSION["id"]);
	$userparams = $mysql->query("SELECT * FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `id`= ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();
	unset($user["hash"]);
	unset($user["lastact"]);
	unset($user["blocked"]);
	unset($user["deleted"]);
	session_regenerate_id();
	foreach ($userparams as $userparamname => $userparamvalue) {
		$_SESSION[$userparamname] = $userparamvalue;
	}
}

if ($sys["license"] >= 1 and $sys["whitelabel"] == 1) {
	$unbranded = true;
}