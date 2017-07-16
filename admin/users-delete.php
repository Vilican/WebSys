<?php

do {
	
	if (!has_access("admin_users_delete")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$user = $mysql->query("SELECT `users`.`id`, `users`.`username`, `roles`.`level` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `users`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($user->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Tento uživatel neexistuje!</strong></div>';
		break;
	}
	
	$user = $user->fetch_assoc();
	
	if (($user["level"] >= $_SESSION["level"] and $_SESSION["id"] != 0) or $user["id"] == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke změně tohoto objektu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa uživatelů - smazání';
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		unlink("upload/avatars/". santise($_GET["id"]));
		$mysql->query("UPDATE `articles` SET `author` = 0 WHERE `author` = ". $mysql->quote($_GET["id"]) .";");
		$mysql->query("UPDATE `posts` SET `anon_author` = ". $mysql->quote($user["username"]) .",`anon_ip` = '-', `author` = NULL WHERE `author` = ". $mysql->quote($_GET["id"]) .";");
		$mysql->query("UPDATE `pages` SET `author` = 0 WHERE `author` = ". $mysql->quote($_GET["id"]) .";");
		$mysql->query("UPDATE `phistory` SET `author` = 0 WHERE `author` = ". $mysql->quote($_GET["id"]) .";");
		$mysql->query("DELETE FROM `users` WHERE `id` = ". $mysql->quote($_GET["id"]) .";");
		header("Location: admin.php?p=users");
		die();
		
	} while(0);
	
	$page["content"] .= $message .'<form method="post"><table style="border-spacing:10px">
	<tr><td>Uživatelské jméno:</td><td><input type="text" value="'. $user["username"] .'" class="form-control" disabled="disabled"></td></tr>
	<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Opravdu odstranit" class="btn btn-danger"></td></tr>
	</table><input type="hidden" name="csrf" value="'. generate_csrf() .'"></form>';
	
} while (0);