<?php

if (isset($_POST["save"])) do {
	
	if (!isset($_SESSION["id"]) or $_SESSION["id"] != 0) {
		break;
	}
	
	if (!validate_csrf($_POST["csrf"])) {
		$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF! To může znamenat pokus o útok.</strong></div>';
		break;
	}
	
	if (!check_type($_POST["title"], "nospecial") or empty($_POST["title"])) {
		$message = '<div class="alert alert-danger"><strong>Název neuložen - nesmí obsahovat speciální znaky a musí být vyplněn!</strong></div>';
	} else {
		$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote($_POST["title"]) ." WHERE `setting` = 'title';");
	}
	
	if (!check_type($_POST["author"], "nospecial")) {
		$message .= '<div class="alert alert-danger"><strong>Autor neuložen - nesmí obsahovat speciální znaky!</strong></div>';
	} else {
		$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote($_POST["author"]) ." WHERE `setting` = 'author';");
	}
	
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(parse_from_checkbox($_POST["anonymousposts"])) ." WHERE `setting` = 'anonymousposts';");
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(parse_from_checkbox($_POST["flags"])) ." WHERE `setting` = 'flags';");
	
	if (!check_type($_POST["paging"], "positivewholenum") or empty($_POST["paging"])) {
		$message .= '<div class="alert alert-danger"><strong>Stránkování neuloženo - smí obsahovat pouze kladná celá čísla!</strong></div>';
	} else {
		$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote($_POST["paging"]) ." WHERE `setting` = 'paging';");
	}
	
	if (!check_type($_POST["authoredittime"], "positivewholenum")) {
		$message .= '<div class="alert alert-danger"><strong>Doba správy příspěvku neuložena - smí obsahovat pouze kladná celá čísla!</strong></div>';
	} else {
		$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote((int)$_POST["authoredittime"]) ." WHERE `setting` = 'authoredittime';");
	}
	
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(parse_from_checkbox($_POST["regallowed"])) ." WHERE `setting` = 'regallowed';");
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(parse_from_checkbox($_POST["lostpass"])) ." WHERE `setting` = 'lostpass';");
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(parse_from_checkbox($_POST["stricthttps"])) ." WHERE `setting` = 'stricthttps';");
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(parse_from_checkbox($_POST["restrictorigin"])) ." WHERE `setting` = 'restrictorigin';");
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(parse_from_checkbox($_POST["twofactor_gauth"])) ." WHERE `setting` = 'twofactor_gauth';");
	
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(filter_var($_POST["yubi_url"], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) ." WHERE `setting` = 'yubi_url';");
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote($_POST["yubi_id"]) ." WHERE `setting` = 'yubi_id';");
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote($_POST["yubi_key"]) ." WHERE `setting` = 'yubi_key';");
	
	if (parse_from_checkbox($_POST["twofactor_yubi"])) {
		if (empty($_POST["yubi_url"]) or empty($_POST["yubi_id"]) or empty($_POST["yubi_key"])) {
			$message .= '<div class="alert alert-danger"><strong>YubiKey autentizaci nelze povolit! Nejsou řádně vyplněné údaje o serveru!</strong></div>';
			$mysql->query("UPDATE `settings` SET `value` = 0 WHERE `setting` = 'twofactor_yubi';");
		} else {
			$mysql->query("UPDATE `settings` SET `value` = 1 WHERE `setting` = 'twofactor_yubi';");
		}
	} else {
		$mysql->query("UPDATE `settings` SET `value` = 0 WHERE `setting` = 'twofactor_yubi';");
	}
	
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["bodybackground"] ."' WHERE `setting` = 'bodybackground';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["bodytxtcolor"] ."' WHERE `setting` = 'bodytxtcolor';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["headercolortop"] ."' WHERE `setting` = 'headercolortop';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["headercolorbottom"] ."' WHERE `setting` = 'headercolorbottom';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["navcolor"] ."' WHERE `setting` = 'navcolor';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["navtextcolor"] ."' WHERE `setting` = 'navtextcolor';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["navactivecolor"] ."' WHERE `setting` = 'navactivecolor';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["titlecolor"] ."' WHERE `setting` = 'titlecolor';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["wellcolor"] ."' WHERE `setting` = 'wellcolor';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["wellborder"] ."' WHERE `setting` = 'wellborder';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["hrcolor"] ."' WHERE `setting` = 'hrcolor';");
	$mysql->query("UPDATE `settings` SET `value` = '". $_POST["submenucaretcolor"] ."' WHERE `setting` = 'submenucaretcolor';");
    $mysql->query("UPDATE `settings` SET `value` = '". $_POST["linkcolor"] ."' WHERE `setting` = 'linkcolor';");
	
	$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote(parse_from_checkbox($_POST["whitelabel"])) ." WHERE `setting` = 'whitelabel';");
	
	if (!empty($_POST["licreload"])) {
		
		if (isset($_SERVER["HTTP_HOST"])) {
			$lic_domain = $_SERVER["HTTP_HOST"];
		} else {
			$lic_domain = $_SERVER["SERVER_NAME"];
		}
		
		if (ini_get('allow_url_fopen')) {
			$lic_lvl = file_get_contents('https://www.sufix.cz/ws-lic/?domain='. $lic_domain);
		} elseif (function_exists('curl_version')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://www.sufix.cz/ws-lic/?domain='. $lic_domain);
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
		
	}
	
} while(0);