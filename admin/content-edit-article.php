<?php

do {

	if (!has_access("admin_content")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - editace článku';
	
	if (empty($_GET["id"])) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Editace: takový článek neexistuje!</strong></div>';
		break;
	}
	
	$art = $mysql->query("SELECT * FROM `articles` WHERE `articles`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($art->num_rows == 0) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Editace: takový článek neexistuje!</strong></div>';
		break;
	}
	
	$art = $art->fetch_assoc();

	if (!has_access("admin_content_articles_edit_all") and !(has_access("admin_content_articles_edit") and $art["author"] == $_SESSION["id"])) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Editace: chybí oprávnění!</strong></div>';
		break;
	}
	
	if ($art["approved"] == 1 and !has_access("admin_content_articles_edit_approved")) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Editace: chybí oprávnění!</strong></div>';
		break;
	}
	
	if ($sys["license"] < 1) {
		$lic = '<br><span class="text-danger">Licence nedovoluje vkládání komerčního obsahu.</span>';
	}
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		if (!validate_length($_POST["title"], 5, 48)) {
			$message .= 'Titulek musí obsahovat 5 až 48 znaků!<br>';
			$err = true;
		}
		
		if ($err) {
			$message = '<div class="alert alert-danger"><p><strong>Při ukládání došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
			break;
		}

		$mysql->query("UPDATE `articles` SET `title` = ". $mysql->quote(santise($_POST["title"])) .", `text` = ". $mysql->quote($_POST["content"]) .", `description` = ". $mysql->quote(santise($_POST["description"])) .", `location` = ". $mysql->quote(santise($_POST["location"])) ." WHERE `articles`.`id` = ". $mysql->quote($_GET["id"]) .";");
		$message = '<div class="alert alert-success"><strong>Stránka upravena</strong></div>';
		$art = $mysql->query("SELECT * FROM `articles` WHERE `articles`.`id` = ". $mysql->quote($_GET["id"]) .";")->fetch_assoc();
		
	} while (0);
	
	$cats = $mysql->query("SELECT `pages`.`id`, `pages`.`title` FROM `pages` WHERE `pages`.`type` = 5;");
	if ($cats->num_rows > 0) {
		while($cat = $cats->fetch_assoc()) {
			if ($cat["id"] == $art["location"]) {
				$selected = ' selected="selected"';
			} else {
				$selected = null;
			}
			$categories .= '<option value="'. $cat["id"] .'"'. $selected .'>'. $cat["title"] .'</option>';
		}
	}
	
	$ckeditor = true;
	$page["content"] .= $message .'
<form method="post"><table style="border-spacing: 10px">
<tr><td>Kategorie:</td><td><select name="location" class="form-control">'. $categories .'</select></td></tr>
<tr><td>Titulek:</td><td><input type="text" name="title" class="form-control" value="'. restore_value($art["title"], santise($_POST["title"])) .'"></td></tr>
<tr><td>Popis:</td><td><textarea name="description" class="form-control">'. restore_value($art["description"], santise($_POST["description"])) .'</textarea></td></tr>
<tr><td>Obsah:</td><td><textarea name="content" class="form-control">'. restore_value($art["text"], $_POST["content"]) .'</textarea>'. $lic .'</td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Upravit" class="btn btn-default"></td></tr>
</table></form>';

} while (0);