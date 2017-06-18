<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

require "core/loadcore.php";

if ($_GET["p"] == null) {
	$_GET["p"] = $sys["homepage"];
}

$sys_pages = array("login", "logout", "reg", "lostpass");

if (in_array($_GET["p"], $sys_pages)) {
	
	switch ($_GET["p"]) {
		case "login":
			require "logic/login.php";
			require "template/login.php";
			break;
		case "reg":
			if ($sys["regallowed"]) {
				require "logic/register.php";
				require "template/register.php";
				break;
			}
			$page["title"] = "Zamítnuto";
			$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění pro zobrazení stránky!</strong></div>';
			require "template/page.php";
			break;
		case "logout":
			session_unset();
			session_destroy();
			header("Location: index.php");
			exit;
		case "lostpass":
			require "logic/lostpass.php";
			require "template/lostpass.php";
			break;
	}
	
} else {

	$page = $mysql->query("SELECT * FROM `pages` WHERE `id` = ". $mysql->quote($_GET["p"]) .";")->fetch_assoc();
	
	if(empty($page)) {
		
		$page["title"] = "Nenalezeno";
		$page["content"] = '<div class="alert alert-danger"><strong>Taková stránka neexistuje!</strong></div>';
		
	} elseif ($page["access"] == 0 or $page["access"] <= $_SESSION["level"]) {
		
		$page_type_map = array(2 => "logic/talk.php", 3 => "logic/forum.php", 4 => "logic/galery.php");
		
		if (!empty($page_type_map[$page["type"]])) {
			require $page_type_map[$page["type"]];
		}
		
	} else {
		
		$page["title"] = "Zamítnuto";
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění pro zobrazení stránky!</strong></div>';
		
	}
	
	require "template/page.php";

}