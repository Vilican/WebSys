<?php

do {
	
	if (!has_access("admin_content") or !has_access("admin_content_edit_all") or !has_access("admin_content_edit")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - vytvoření stránky';
	
	if (empty($page_types[$_GET["type"]])) {
		$page["content"] = '<div class="alert alert-danger"><strong>Vytváření: neplatný typový identifikátor!</strong></div>';
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
		
		if ($pg_newid->num_rows > 0) {
			$message .= 'Identifikátor nesmí být duplicitní!<br>';
			$err = true;
		}
		
		$val = validate_page_fields($_GET["type"]);
		
		if (!empty($val)) {
			$message .= $val;
			$err = true;
		}
		
		if ($err) {
			$message = '<div class="alert alert-danger"><p><strong>Při ukládání došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
			break;
		}
		
		$insert_fields = mysql_page_fields_new($_GET["type"]);

		$mysql->query("INSERT INTO `pages` (`id`, `title`, `content`, `description`, `type`, `ord`, `author`, `visible`, `access`". $insert_fields[0] .") VALUES (". $mysql->quote($_POST["id"]) .", ". $mysql->quote(santise($_POST["title"])) .", ". $mysql->quote($_POST["content"]) .", ". $mysql->quote(santise($_POST["description"])) .", ". $mysql->quote($_GET["type"]) .", ". $mysql->quote($_POST["ord"]) .", ". $mysql->quote($_SESSION["id"]) .", ". $mysql->quote(parse_from_checkbox($_POST["visibility"])) .", ". $mysql->quote($_POST["access"]) . $insert_fields[1] .");");
		$mysql->query("INSERT INTO `phistory` (`page`, `content`, `author`) VALUES (". $mysql->quote($_POST["id"]) .", ". $mysql->quote($_POST["content"]) .", ". $mysql->quote($_SESSION["id"]) .");");
		header("Location: admin.php?p=content-edit-page&id=". $_POST["id"]);
		die();
		
	} while(0);
	
	$ckeditor = true;
	$page["content"] .= $message .'
<form method="post"><table style="border-spacing: 10px">
<tr><td>ID:</td><td><input type="text" name="id" class="form-control" value="'. santise($_POST["id"]) .'"></td></tr>
<tr><td>Titulek:</td><td><input type="text" name="title" class="form-control" value="'. santise($_POST["title"]) .'"></td></tr>
<tr><td>Popis:</td><td><textarea name="description" class="form-control">'. santise($_POST["description"]) .'</textarea></td></tr>
<tr><td>Obsah:</td><td><textarea name="content" class="form-control">'. $_POST["content"] .'</textarea></td></tr>
<tr><td>Pořadí:</td><td><input type="text" name="ord" class="form-control" value="'. santise($_POST["ord"]) .'"></td></tr>
<tr><td>Přístup čtení:</td><td><input type="text" name="access" class="form-control" value="'. santise($_POST["access"]) .'"></td></tr>
'. show_page_fields_new($_GET["type"]) .'
<tr><td>Viditelnost:</td><td><input type="checkbox" name="visibility" class="form-control"></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Vytvořit" class="btn btn-default"></td></tr>
</table></form>';
	
} while(0);