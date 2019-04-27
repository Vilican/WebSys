<?php

require "core/loadcore.php";

if (!isset($_SESSION["level"])) {
    header("Location: index.php?p=login");
    exit;
}

if (!has_access("admin")) {
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
			break;
		}
		$page["content"] = '<div class="alert alert-danger"><strong>Takový modul neexistuje!</strong></div>';
		break;
}

require "template/admin.php";