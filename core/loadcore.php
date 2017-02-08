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

$flag_reasons = array(1 => "Tento příspěvek je nevhodný", 2 => "Tento příspěvek je mimo téma", 3 => "Tento příspěvek je zastaralý");
$page_types = array(1 => 'stránka', 2 => 'diskuze', 3 => 'fórum');

if (isset($_SESSION["id"])) {
	$mysql->update_activity($_SESSION["id"]);
}

if ($sys["license"] == 2 and $sys["whitelabel"] == 1) {
	$unbranded = true;
}