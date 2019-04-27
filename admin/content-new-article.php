<?php

do {

	if (!has_access("admin_content")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - vytvoření článku';
	
	if (!has_access("admin_content_articles_edit_all") and !has_access("admin_content_articles_edit")) {
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

		if (has_access('admin_content_articles_edit_autoapprove')) {
			$approved = "1";
		} else {
			$approved = "0";
		}
		
		$mysql->query("INSERT INTO `articles` (`title`, `text`, `description`, `location`, `author`, `approved`) VALUES (". $mysql->quote(santise($_POST["title"])) .", ". $mysql->quote($_POST["content"]) .", ". $mysql->quote(santise($_POST["description"])) .", ". $mysql->quote($_POST["location"]) .", ". $mysql->quote($_SESSION["id"]) .", ". $approved .");");

		header("Location: admin.php?p=content-edit-article&id=". $mysql->insert_id());
		die();
		
	} while (0);
	
	$cats = $mysql->query("SELECT `pages`.`id`, `pages`.`title` FROM `pages` WHERE `pages`.`type` = 5;");
	if ($cats->num_rows > 0) {
		while($cat = $cats->fetch_assoc()) {
			$categories .= '<option value="'. $cat["id"] .'">'. $cat["title"] .'</option>';
		}
	}
	
	$ckeditor = true;
	$page["content"] .= $message .'
<form method="post"><table style="border-spacing: 10px">
<tr><td>Kategorie <span class="text-danger">*</span>:</td><td><select name="location" class="form-control">'. $categories .'</select></td></tr>
<tr><td>Titulek <span class="text-danger">*</span>:</td><td><input type="text" name="title" class="form-control" value="'. santise($_POST["title"]) .'"></td></tr>
<tr><td>Popis:</td><td><textarea name="description" class="form-control">'. santise($_POST["description"]) .'</textarea></td></tr>
<tr><td>Obsah <span class="text-danger">*</span>:</td><td><textarea name="content" class="form-control">'. $_POST["content"] .'</textarea>'. $lic .'</td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Vytvořit" class="btn btn-default"></td></tr>
</table></form>';

} while (0);