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
			require "logic/register.php";
			require "template/register.php";
			break;
		case "logout":
			session_unset();
			session_destroy();
			header("Location: index.php");
			die();
			break;
	}
	
} else {

	$page = mysqli_fetch_assoc($mysql->query("SELECT * FROM `pages` WHERE `id` = ". $mysql->quote($_GET["p"]) .";"));
	
	if(empty($page)) {
		
		$page["title"] = "Nenalezeno";
		$page["content"] = '<div class="alert alert-danger"><strong>Taková stránka neexistuje!</strong></div>';
		
	} elseif ($page["access"] == 0 or $page["access"] <= $_SESSION["level"]) {
	
		switch ($page["type"]) {
		
			case 1:
				break;
			case 2:
				require "logic/talk.php";
				break;
		
			default:
				throw_error("Stránka nese neplatný typový identifikátor!");
		
		}
	
	} else {
		
		$page["title"] = "Zamítnuto";
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění pro zobrazení stránky!</strong></div>';
		
	}
	
	require "template/page.php";

}

?>