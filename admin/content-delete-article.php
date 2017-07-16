<?php

do {

	if (!has_access("admin_content")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - smazání článku';
	
	if (!isset($_GET["id"])) {
		$page["content"] = '<div class="alert alert-danger"><strong>Smazání: takový článek neexistuje!</strong></div>';
		break;
	}
	
	$art = $mysql->query("SELECT `articles`.`title`, `articles`.`author` FROM `articles` WHERE `articles`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($art->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Smazání: takový článek neexistuje!</strong></div>';
		break;
	}
	
	$art = $art->fetch_assoc();
	
	if (!has_access("admin_content_articles_delete_all") and !(has_access("admin_content_articles_delete") and $art["author"] == $_SESSION["id"])) {
		$page["content"] = '<div class="alert alert-danger"><strong>Smazání: chybí oprávnění!</strong></div>';
		break;
	}
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$page["content"] = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		$mysql->query("DELETE FROM `articles` WHERE `articles`.`id` = ". $mysql->quote($_GET["id"]) .";");
		header("Location: admin.php?p=content");
		die();
		
	} while(0);
	
	$page["content"] .= '<form method="post"><table style="border-spacing: 10px">
<tr><td>Článek:</td><td><input type="text" class="form-control" value="'. $art["title"] .'" disabled="disabled"></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Smazat" class="btn btn-danger"></td></tr>
</table></form>';

} while(0);