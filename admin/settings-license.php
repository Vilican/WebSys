<?php

if (!isset($_SESSION["id"]) or $_SESSION["id"] != 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Nastavení systému - licence';

if (isset($_GET["update"])) {

	if (isset($_SERVER["HTTP_HOST"])) {
		$lic_domain = $_SERVER["HTTP_HOST"];
	} else {
		$lic_domain = $_SERVER["SERVER_NAME"];
	}

	if (ini_get('allow_url_fopen')) {
		$lic_lvl = file_get_contents('https://sufix.cz/ws-lic/?domain='. $lic_domain);
	} elseif (function_exists('curl_version')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://sufix.cz/ws-lic/?domain='. $lic_domain);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$lic_lvl = curl_exec($ch);
		curl_close($ch);
	} else {
		$lic_err = true;
	}

	if (!is_numeric($lic_lvl)) {
		$lic_err = true;
	}
	
	if (!$lic_err) {
		$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote($lic_lvl) ." WHERE `setting` = 'license';");
	}
	
	header("Location: admin.php?p=settings-license");
	die();
	
}

switch ($sys["license"]) {
	case 0:
		$page["content"] .= '<div class="alert alert-warning"><strong><p>Systém na této doméně používá tuto licenci:</p><p>a) pouze nekomerční použití<br>b) musí zobrazovat zpětný odkaz<br>c) změny kódu mimo designu nejsou povoleny</p></strong></div>';
		break;
	case 1:
		$page["content"] .= '<div class="alert alert-info"><strong><p>Systém na této doméně používá tuto licenci:</p><p>a) komerční použití je povoleno<br>b) musí zobrazovat zpětný odkaz<br>c) změny kódu mimo designu nejsou povoleny</p></strong></div>';
		break;
	case 2:
		$page["content"] .= '<div class="alert alert-success"><strong><p>Systém na této doméně používá tuto licenci:</p><p>a) komerční použití je povoleno<br>b) zpětný odkaz není požadován<br>c) změny v kódu jsou povoleny</p></strong></div>';
		break;
}

if ($sys["license"] < 2) {
	$page["content"] .= '<a href="" class="btn btn-danger">Koupit upgrade</a> <a href="admin.php?p=settings-license&update" class="btn btn-primary">Aktualizovat licenci</a>';
}

if ($sys["license"] == 2) {

	if (isset($_POST["submit"])) {
		$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(parse_from_checkbox($_POST["whitelabel"])) ." WHERE `setting` = 'whitelabel';");
		header("Location: admin.php?p=settings-license");
		die();
	}

	$page["content"] .= '<form method="post"><p><input type="checkbox" name="whitelabel"'. parse_to_checkbox($sys["whitelabel"]) .'>Odstrantit zpětné odkazy</a></p><button type="submit" class="btn btn-primary btn-sm" name="submit">Uložit</button></form>';
}



} ?>