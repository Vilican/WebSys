<?php

require "database.php";
require "functions.php";
require "config.php";

$mysql = new DB();
if (!$mysql->connect(_SRV, _USR, _PW, _DB)) {
	throw_error("Připojení k databázi se nepodařilo.<br>Prosím zkontrolujte nastavení v config.php");
}

$settings = $mysql->query("SELECT * FROM `settings`");
while($setting = $settings->fetch_assoc()) {
	$sys[$setting["setting"]] = $setting["value"];
}

$page_types = array(1 => 'stránka', 2 => 'diskuze', 3 => 'fórum', 4 => 'galerie');

if (isset($_SESSION["id"])) {
	$mysql->update_activity($_SESSION["id"]);
}

if ($sys["license"] == 2 and $sys["whitelabel"] == 1) {
	$unbranded = true;
}