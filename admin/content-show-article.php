<?php

do {

	if (!has_access("admin_content") or !has_access("admin_content_review_articles")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - náhled článku';
	
	if (empty($_GET["id"])) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Náhled: takový článek neexistuje!</strong></div>';
		break;
	}
	
	$art = $mysql->query("SELECT `articles`.*, `pages`.`title` AS `location` FROM `articles` INNER JOIN `pages` ON `articles`.`location` = `pages`.`id` WHERE `articles`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($art->num_rows == 0) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Náhled: takový článek neexistuje!</strong></div>';
		break;
	}
	
	$art = $art->fetch_assoc();
	
	$ckeditor = true;
	$page["content"] .= $message .'
<table style="border-spacing: 10px">
<tr><td>Kategorie:</td><td><input type="text" class="form-control" value="'. $art["location"] .'" disabled="disabled"></td></tr>
<tr><td>Titulek:</td><td><input type="text" name="title" class="form-control" value="'. $art["title"] .'" disabled="disabled"></td></tr>
<tr><td>Popis:</td><td><textarea name="description" class="form-control" disabled="disabled">'. $art["description"] .'</textarea></td></tr>
<tr><td>Obsah:</td><td><span class="text text-danger">Neprovádějte změny! Zde není možné je uložit!</span><br><textarea name="content" class="form-control">'. $art["text"] .'</textarea></td></tr>
</table>';

} while (0);