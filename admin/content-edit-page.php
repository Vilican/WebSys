<?php

do {

	if (!$_SESSION["access_admin_content"] > 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - editace stránky';
	
	if (empty($_GET["id"])) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Editace: taková stránka neexistuje!</strong></div>';
		break;
	}
	
	$pg = $mysql->query("SELECT * FROM `pages` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($pg->num_rows == 0) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Editace: taková stránka neexistuje!</strong></div>';
		break;
	}
	
	$pg = $pg->fetch_assoc();

	if (!$_SESSION["access_admin_content_edit_all"] > 0 and !($_SESSION["access_admin_content_edit"] > 0 and $pg["author"] == $_SESSION["id"])) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Editace: chybí oprávnění!</strong></div>';
		break;
	}
	
	require "admin/require/page-extras.php";
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		if (!validate_length($_POST["id"], 1, 16)) {
			$message .= 'Identifikátor musí obsahovat 1 až 16 znaků!<br>';
			$err = true;
		}
		
		if (!validate_length($_POST["title"], 3, 24)) {
			$message .= 'Titulek musí obsahovat 3 až 24 znaků!<br>';
			$err = true;
		}
		
		if (!validate_length($_POST["ord"], 1, 4) or !is_numeric($_POST["ord"]) or $_POST["ord"] < 0) {
			$message .= 'Pořadí musí obsahovat 1 až 4 kladná čísla!<br>';
			$err = true;
		}
		
		if (!validate_length($_POST["access"], 1, 4) or !is_numeric($_POST["access"]) or $_POST["access"] < 0) {
			$message .= 'Přístup musí obsahovat 1 až 4 kladná čísla!<br>';
			$err = true;
		}
		
		if (!ctype_alnum($_POST["id"])) {
			$message .= 'Identifikátor nesmí obsahovat speciální znaky!<br>';
			$err = true;
		}
		
		$pg_newid = $mysql->query("SELECT `id` FROM `pages` WHERE `id` = ". $mysql->quote($_POST["id"]) .";");
		
		if ($pg_newid->num_rows > 0 and $pg["id"] != $_POST["id"]) {
			$message .= 'Identifikátor nesmí být duplicitní!<br>';
			$err = true;
		}
		
		if ($pg["id"] == $sys["homepage"] and $pg["id"] != $_POST["id"]) {
			$message .= 'Identifikátor hlavní stránky nelze změnit!<br>';
			$err = true;
		}
		
		$val = validate_page_fields($pg["type"]);
		
		if (!empty($val)) {
			$message .= $val;
			$err = true;
		}
		
		if ($err) {
			$message = '<div class="alert alert-danger"><p><strong>Při ukládání došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
			break;
		}

		$mysql->query("UPDATE `pages` SET `id` = ". $mysql->quote($_POST["id"]) .", `title` = ". $purifier->purify($mysql->quote($_POST["title"])) .",	`content` = ". $mysql->quote($_POST["content"]) .", `description` = ". $purifier->purify($mysql->quote($_POST["description"])) .", `ord` = ". $mysql->quote($_POST["ord"]) .", `visible` = ". $mysql->quote(parse_from_checkbox($_POST["visibility"])) .", `access` = ". $mysql->quote($_POST["access"]) . mysql_page_fields($pg["type"], $mysql) ." WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");
		$mysql->query("INSERT INTO `phistory` (`page`, `content`, `author`) VALUES (". $mysql->quote($_POST["id"]) .", ". $mysql->quote($_POST["content"]) .", ". $mysql->quote($_SESSION["id"]) .");");
		$message = '<div class="alert alert-success"><strong>Stránka upravena</strong></div>';
		$pg = $mysql->query("SELECT * FROM `pages` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";")->fetch_assoc();
		
	} while (0);
	
	$ckeditor = true;
	$page["content"] .= $message .'
<form method="post"><table style="border-spacing: 10px">
<tr><td>ID:</td><td><input type="text" name="id" class="form-control" value="'. restore_value($pg["id"], $purifier->purify($_POST["id"])) .'"></td></tr>
<tr><td>Titulek:</td><td><input type="text" name="title" class="form-control" value="'. restore_value($pg["title"], $purifier->purify($_POST["title"])) .'"></td></tr>
<tr><td>Popis:</td><td><textarea name="description" class="form-control">'. restore_value($pg["description"], $purifier->purify($_POST["description"])) .'</textarea></td></tr>
<tr><td>Obsah:</td><td><textarea name="content" class="form-control">'. restore_value($pg["content"], $_POST["content"]) .'</textarea></td></tr>
<tr><td>Pořadí:</td><td><input type="text" name="ord" class="form-control" value="'. restore_value($pg["ord"], $purifier->purify($_POST["ord"])) .'"></td></tr>
<tr><td>Přístup čtení:</td><td><input type="text" name="access" class="form-control" value="'. restore_value($pg["access"], $purifier->purify($_POST["access"])) .'"></td></tr>
'. show_page_fields($pg["type"], $pg, $purifier) .'
<tr><td>Viditelnost:</td><td><input type="checkbox" name="visibility" class="form-control"'. parse_to_checkbox($pg["visible"]) .'></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Upravit" class="btn btn-default"></td></tr>
</table></form>';

} while (0);