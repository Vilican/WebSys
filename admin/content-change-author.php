<?php

do {
	
	if (!has_access("admin_content") or !has_access("admin_content_changeeditor")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - změna editora';
	
	if (!isset($_GET["id"])) {
		$page["content"] = '<div class="alert alert-danger"><strong>Editace: taková stránka neexistuje!</strong></div>';
		break;
	}
	
	$pg = $mysql->query("SELECT `pages`.`title`, `users`.`id`, `users`.`username` FROM `pages` INNER JOIN `users` ON `pages`.`author` = `users`.`id` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");

	if ($pg->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Editace: taková stránka neexistuje!</strong></div>';
		break;
	}
	
	$pg = $pg->fetch_assoc();
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$page["content"] = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		$mysql->query("UPDATE `pages` SET `author` = ". $mysql->quote($_POST["author"]) ." WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");
		$pg = $mysql->query("SELECT `pages`.`title`, `users`.`id`, `users`.`username` FROM `pages` INNER JOIN `users` ON `pages`.`author` = `users`.`id` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";")->fetch_assoc();
		$page["content"] = '<div class="alert alert-success"><strong>Editor byl změněn</strong></div>';
		
	} while(0);
	
	$users = $mysql->query("SELECT `users`.`id`, `users`.`username` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `roles`.`access_admin_content_edit` = 1;");
	if ($users->num_rows > 0) {
		while($user = $users->fetch_assoc()) {
			if ($pg["id"] == $user["id"]) {
				$selected = ' selected="selected"';
			} else {
				$selected = null;
			}
			$users_options .= '<option value="'. $user["id"] .'"'. $selected .'>'. $user["username"] .'</option>';
		}
	}
			
	$page["content"] .= '<form method="post"><table style="border-spacing: 10px">
<tr><td>Stránka:</td><td><input type="text" class="form-control" value="'. $pg["title"] .'" disabled="disabled"></td></tr>
<tr><td>Editor:</td><td><select name="author" class="form-control">'. $users_options .'</select></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Upravit" class="btn btn-default"></td></tr>
</table></form>';
	
} while(0);