<?php

function throw_error($error_message) {
	require "template/core_error.php";
	exit;
}

function menu(&$mysql) {
	$pages = $mysql->query("SELECT `id`, `title` FROM `pages` WHERE `ord` < 11 AND `visible` = '1';");
	if ($pages->num_rows > 0) {
		while($menu_page = $pages->fetch_assoc()) {
			if ($_GET["p"] == $menu_page["id"]) {
				$menu .= "<li><a href='index.php?p=".$menu_page["id"]."' class='act'>".$menu_page["title"]."</a></li>";
			} else {
				$menu .= "<li><a href='index.php?p=".$menu_page["id"]."'>".$menu_page["title"]."</a></li>";
			}
		}
	}
	return $menu;
}

function boxes(&$mysql) {
	$boxes = $mysql->query("SELECT `content`, `access` FROM `boxes` WHERE `visible` = 1 ORDER BY `ord` ASC;");
	if ($boxes->num_rows > 0) {
		while($box = $boxes->fetch_assoc()) {
			if ($box["access"] == 0 or $box["access"] <= $_SESSION["level"]) {
				$box["content"] = str_ireplace("%usermenu%", user_menu(), $box["content"]);
				$box_column .= '<div class="well">'. $box["content"] ."</div>";
			}
		}
	}
	return $box_column;
}

function admin_menu() {

	if ($_SESSION["access_admin_content"] > 0) {
	if ($_GET["p"] == "content") {
		$menu .= "<li><a href='admin.php?p=content' class='act'>Správa obsahu</a></li>";
	} else {
		$menu .= "<li><a href='admin.php?p=content'>Správa obsahu</a></li>";
	}
	}
	
	if ($_SESSION["access_admin_content"] > 0) {
	if ($_GET["p"] == "files") {
		$menu .= "<li><a href='admin.php?p=files' class='act'>Správa souborů</a></li>";
	} else {
		$menu .= "<li><a href='admin.php?p=files'>Správa souborů</a></li>";
	}
	}
	
	if ($_SESSION["access_admin_users"] > 0) {
	if ($_GET["p"] == "users") {
		$menu .= "<li><a href='admin.php?p=users' class='act'>Správa uživatelů</a></li>";
	} else {
		$menu .= "<li><a href='admin.php?p=users'>Správa uživatelů</a></li>";
	}
	}
	
	if ($_SESSION["access_admin_roles"] > 0) {
	if ($_GET["p"] == "groups") {
		$menu .= "<li><a href='admin.php?p=groups' class='act'>Správa skupin</a></li>";
	} else {
		$menu .= "<li><a href='admin.php?p=groups'>Správa skupin</a></li>";
	}
	}
	
	if ($_SESSION["access_admin_settings"] > 0) {
	if ($_GET["p"] == "sys") {
		$menu .= "<li><a href='admin.php?p=sys' class='act'>Nastavení systému</a></li>";
	} else {
		$menu .= "<li><a href='admin.php?p=sys'>Nastavení systému</a></li>";
	}
	}
	
	$menu .= "<li><a href='index.php'>Návrat na stránky</a></li>";
	
	return $menu;
}

function user_menu() {
	
	if (isset($_SESSION["id"])) {
		$menu = "<p>Přihlášen: ". $_SESSION["username"] ."</p><p><a href='index.php?p=logout'>Odhlásit se</a><br><a href='index.php?p=settings'>Nastavení</a></p>";
		if ($_SESSION["access_admin"]) {
			$menu .= "<p><a href='admin.php'>Administrace</a></p>";
		}
	} else {
		$menu = "<a href='index.php?p=login'>Přihlásit se</a><br><a href='index.php?p=reg'>Registrovat</a>";
	}
	
	return $menu;
}

function santise($input) {
	return filter_var($input, FILTER_SANITIZE_STRING);
}

function generate_csrf() {
	$string = md5(openssl_random_pseudo_bytes(12));
	$_SESSION["csrf"] = $string;
	return $string;
}

function validate_csrf($token) {
	if ($token == $_SESSION["csrf"]) {
		unset($_SESSION["csrf"]);
		return true;
	} else {
		return false;
	}
}

function restore_value($original, $posted) {
	if ($posted !== null) {
		return $posted;
	} else {
		return $original;
	}
}

function parse_to_checkbox($state) {
	if ($state == 1) {
		return ' checked="checked"';
	} else {
		return null;
	}
}

function parse_from_checkbox($state) {
	if ($state == "on") {
		return 1;
	} else {
		return 0;
	}
}

?>