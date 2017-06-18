<?php

function menu() {
	global $mysql;
	$pages = $mysql->query("SELECT `id`, `title` FROM `pages` WHERE `ord` < 11 AND `visible` = '1' ORDER BY `ord` ASC;");
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

function boxes() {
	global $mysql;
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

function has_access($place) {
	if (!isset($_SESSION["id"])) {
		return false;
	}
	if ($_SESSION["id"] == 0) {
		return true;
	}
	if ($_SESSION["access_". $place] > 0 and $_SESSION["emailvalid"] == 1) {
		return true;
	}
	return false;
}

function admin_menu() {

	if (has_access("admin_content")) {
		if ($_GET["p"] == "content") {
			$menu .= "<li><a href='admin.php?p=content' class='act'>Správa obsahu</a></li>";
		} else {
			$menu .= "<li><a href='admin.php?p=content'>Správa obsahu</a></li>";
		}
	}
	
	if (has_access("admin_users")) {
		if ($_GET["p"] == "users") {
			$menu .= "<li><a href='admin.php?p=users' class='act'>Správa uživatelů</a></li>";
		} else {
			$menu .= "<li><a href='admin.php?p=users'>Správa uživatelů</a></li>";
		}
	}
	
	if (has_access("admin_roles")) {
		if ($_GET["p"] == "groups") {
			$menu .= "<li><a href='admin.php?p=groups' class='act'>Správa skupin</a></li>";
		} else {
			$menu .= "<li><a href='admin.php?p=groups'>Správa skupin</a></li>";
		}
	}
	
	if (has_access("admin_settings")) {
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
	global $sys;
	if (isset($_SESSION["id"])) {
		$menu = "<p>Přihlášen: ". $_SESSION["username"] ."</p><p><a href='index.php?p=logout'>Odhlásit se</a><br><a href='index.php?p=settings'>Nastavení</a></p>";
		if (has_access("admin")) {
			$menu .= "<p><a href='admin.php'>Administrace</a></p>";
		}
	} else {
		$menu = "<a href='index.php?p=login'>Přihlásit se</a>";
		if ($sys["regallowed"]) {
			$menu .= "<br><a href='index.php?p=reg'>Registrovat</a>";
		}
	}
	
	return $menu;
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
	}
	return false;
}

function restore_value($original, $posted) {
	if (!empty($posted)) {
		return $posted;
	}
	return $original;
}

function parse_to_checkbox($state) {
	if ($state == 1) {
		return ' checked="checked"';
	}
	return null;
}

function parse_from_checkbox($state) {
	if ($state == "on") {
		return 1;
	}
	return 0;
}

function validate_str($str, $allowed = null) {
	if (ctype_alnum(str_replace($allowed, '', $str))) {
		return true;
	}
	return false;
}

function validate_length($str, $min, $max) {
	if (strlen($str) >= $min and strlen($str) <= $max) {
		return true;
	}
	return false;
}

function is_date( $str ) {
    try {
        $dt = new DateTime( trim($str) );
    }
    catch( Exception $e ) {
        return false;
    }
    $month = $dt->format('m');
    $day = $dt->format('d');
    $year = $dt->format('Y');
    if( checkdate($month, $day, $year) ) {
        return true;
    }
    else {
        return false;
    }
}

function check_type($str, $type) {
	
	if (empty($str)) {
		return true;
	}
	
	switch ($type) {
		
		case "numeric":
			if (!is_numeric($str)) {
				return false;
			}
			break;

		case "alphanum":
			if (!ctype_alnum($str)) {
				return false;
			}
			break;
			
		case "alpha":
			if (!ctype_alpha($str)) {
				return false;
			}
			break;
			
		case "text":
			break;
		
		case "date":
			if (!is_date($str)) {
				return false;
			}
			break;
			
		case "username":
			if(!ctype_alnum(str_replace(array("-", "_", "á", "č", "ď", "é", "ě", "í", "ň", "ó", "ř", "š", "ť", "ů", "ú", "ý", "ž"), '', $str))) {
				return false;
			}
		
	}
	return true;
}

function santise($str) {
	return filter_var($str, FILTER_SANITIZE_STRING);
}

function id_to_user($identificator) {
	
	global $mysql;
	
	$user = $mysql->query("SELECT `users`.`id`, `users`.`username`, `roles`.`color` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `users`.`id` = ". $mysql->quote($identificator) ." OR `users`.`username` = ". $mysql->quote($identificator) .";");
	
	if ($user->num_rows > 0) {
		
		$user = $user->fetch_assoc();
		return '<a href="index.php?p=profile&id='. $user["id"] .'" style="color:'. $user["color"] .'">'. $user["username"] .'</a>';
		
	}
	
	return false;
	
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        case 'g':
        $val *= 1024;
        case 'm':
        $val *= 1024;
        case 'k':
        $val *= 1024;
    }
    return $val;
}

function max_file_upload() {
    $max_upload = return_bytes(ini_get('upload_max_filesize'));
    $max_post = return_bytes(ini_get('post_max_size'));
    $memory_limit = return_bytes(ini_get('memory_limit'));
    return min($max_upload, $max_post, $memory_limit);
}

function bb_to_html($text) {
	$find = array(
		'~\[b\](.*?)\[/b\]~s',
		'~\[i\](.*?)\[/i\]~s',
		'~\[u\](.*?)\[/u\]~s',
		'~\[url\]((?:ftp|https?)://.*?)\[/url\]~s',
		'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s'
	);
	$replace = array(
		'<b>$1</b>',
		'<i>$1</i>',
		'<span class="underline">$1</span>',
		'<a href="$1" rel="nofollow">$1</a>',
		'<img src="$1" class="user-image img-responsive" alt="">'
	);
	return preg_replace($find,$replace,$text);
}