<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

require "core/loadcore.php";

if (!isset($_SESSION["id"]) or !$_SESSION["access_admin"]) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do administrace!</strong></div>';
	require "template/admin.php";
	die();
}

switch ($_GET["p"]) {
	
	case null:
		$page["title"] = 'Administrace';
		$page["content"] = 'Vítejte v administraci.';
		break;
	default:
		if (file_exists('admin/'. $_GET["p"] .'.php') and ctype_alpha(str_replace("-", null, $_GET["p"]))) {
			require 'admin/'. $_GET["p"] .'.php';
		} else {
			$page["content"] = '<div class="alert alert-danger"><strong>Takový modul neexistuje!</strong></div>';
		}
		break;
}

require "template/admin.php";

?>