<?php

if (!$_SESSION["access_admin_content"] > 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Správa obsahu - vytvoření stránky';

if ($_SESSION["access_admin_content_edit_all"] > 0 or $_SESSION["access_admin_content_edit"] > 0) {
			
	if (isset($_POST["submit"])) {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
		} elseif (strlen($_POST["id"]) > 16) {
			$message = '<div class="alert alert-danger"><strong>Identifikátor je moc dlouhý; maximum je 16 znaků!</strong></div>';
		} elseif (strlen($_POST["id"]) == 0) {
			$message = '<div class="alert alert-danger"><strong>Identifikátor je prázdný!</strong></div>';
		} elseif (strlen($_POST["title"]) == 0) {
			$message = '<div class="alert alert-danger"><strong>Titulek je prázdný!</strong></div>';
		} elseif (strlen($_POST["title"]) > 24) {
			$message = '<div class="alert alert-danger"><strong>Titulek je moc dlouhý; maximum je 24 znaků!</strong></div>';
		} elseif (strlen($_POST["ord"]) == 0) {
			$message = '<div class="alert alert-danger"><strong>Pořadí je prázdné!</strong></div>';
		} elseif (strlen($_POST["access"]) == 0) {
			$message = '<div class="alert alert-danger"><strong>Přístup je prázdné!</strong></div>';
		} elseif (!is_numeric($_POST["ord"]) or $_POST["ord"] < 0) {
			$message = '<div class="alert alert-danger"><strong>Pořadí musí být kladné číslo!</strong></div>';
		} elseif (!is_numeric($_POST["access"]) or $_POST["access"] < 0) {
			$message = '<div class="alert alert-danger"><strong>Přístup musí být kladné číslo!</strong></div>';
		} elseif (strlen($_POST["ord"]) > 4) {
			$message = '<div class="alert alert-danger"><strong>Pořadí je moc dlouhé; maximum jsou 4 znaky!</strong></div>';
		} elseif (strlen($_POST["access"]) > 4) {
			$message = '<div class="alert alert-danger"><strong>Přístup je moc dlouhý; maximum jsou 4 znaky!</strong></div>';
		} elseif (!ctype_alnum($_POST["id"])) {
			$message = '<div class="alert alert-danger"><strong>Identifikátor nesmí obsahovat speciální znaky!</strong></div>';
		} else {
			
			$pg_newid = $mysql->query("SELECT `id` FROM `pages` WHERE `id` = ". $mysql->quote($_POST["id"]) .";");
			if ($pg_newid->num_rows > 0) {
				$message = '<div class="alert alert-danger"><strong>Identifikátor nesmí být duplicitní!</strong></div>';
			} else {
				$mysql->query("INSERT INTO `pages` (`id`, `title`, `content`, `description`, `type`, `ord`, `author`, `visible`, `access`) VALUES (". $mysql->quote($_POST["id"]) .", ". $purifier->purify($mysql->quote($_POST["title"])) .", ". $mysql->quote($_POST["content"]) .", ". $purifier->purify($mysql->quote($_POST["description"])) .", 1, ". $mysql->quote($_POST["ord"]) .", ". $mysql->quote($_SESSION["id"]) .", ". $mysql->quote(parse_from_checkbox($_POST["visibility"])) .", ". $mysql->quote($_POST["access"]) .");");
				$mysql->query("INSERT INTO `phistory` (`page`, `content`, `author`) VALUES (". $mysql->quote($_POST["id"]) .", ". $mysql->quote($_POST["content"]) .", ". $mysql->quote($_SESSION["id"]) .");");
				header("Location: admin.php?p=content-edit-page&id=". $_POST["id"]);
				die();
			}
		}
	}
			
	$ckeditor = true;
	$page["content"] .= $message . '
<form method="post">
<table style="border-spacing: 10px">
<tr><td>ID:</td><td><input type="text" name="id" class="form-control" value="'. $_POST["id"] .'"></td></tr>
<tr><td>Titulek:</td><td><input type="text" name="title" class="form-control" value="'. $_POST["title"] .'"></td></tr>
<tr><td>Popis:</td><td><textarea name="description" class="form-control">'. $_POST["description"] .'</textarea></td></tr>
<tr><td>Obsah:</td><td><textarea name="content" class="form-control">'. $_POST["content"] .'</textarea></td></tr>
<tr><td>Pořadí:</td><td><input type="text" name="ord" class="form-control" value="'. $_POST["ord"] .'"></td></tr>
<tr><td>Přístup:</td><td><input type="text" name="access" class="form-control" value="'. $_POST["access"] .'"></td></tr>
<tr><td>Viditelnost:</td><td><input type="checkbox" name="visibility" class="form-control"'. parse_to_checkbox(parse_from_checkbox($_POST["visibility"])) .'></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Vytvořit" class="btn btn-default"></td></tr>
</table></form>';
			
} else {
	$page["content"] .= '<div class="alert alert-danger"><strong>Vytvoření: chybí oprávnění!</strong></div>';
}

} ?>