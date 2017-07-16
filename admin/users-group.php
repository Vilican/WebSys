<?php

do {
	
	if (!has_access("admin_roles")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa uživatelů - změna skupiny';
	
	$user = $mysql->query("SELECT `users`.`id`, `users`.`username`, `users`.`role`, `roles`.`level` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `users`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($user->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Tento uživatel neexistuje!</strong></div>';
		break;
	}
	
	$user = $user->fetch_assoc();
	
	if ($user["level"] >= $_SESSION["level"] and $_SESSION["id"] != 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke změně tohoto objektu!</strong></div>';
		break;
	}
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		$mysql->query("UPDATE `users` SET `role` = ". $mysql->quote($_POST["role"]) ." WHERE `id` = ". $mysql->quote($_GET["id"]) .";");
		$message = '<div class="alert alert-success"><strong>Skupina změněna</strong></div>';
		$user = $mysql->query("SELECT `users`.`id`, `users`.`username`, `users`.`role`, `roles`.`level` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `users`.`id` = ". $mysql->quote($_GET["id"]) .";")->fetch_assoc();
		
	} while(0);
	
	$roles = $mysql->query("SELECT `role_id`, `rolename` FROM `roles` ORDER BY `level` DESC;");
	if ($roles->num_rows > 0) {
		while($role = $roles->fetch_assoc()) {
			if ($user["role"] == $role["role_id"]) {
				$selected = ' selected="selected"';
			} else {
				$selected = null;
			}
			$roles_options .= '<option value="'. $role["role_id"] .'"'. $selected .'>'. $role["rolename"] .'</option>';
		}
	}
	
	if ($user["id"] == 0) {
		$message .= '<div class="alert alert-info">Tento uživatel je superuživatelem: má všechna práva bez ohledu na skupinu.</div>';
	}
	
	$page["content"] .= $message .'<form method="post"><table style="border-spacing: 10px">
<tr><td>Uživatel:</td><td><input type="text" class="form-control" value="'. $user["username"] .'" disabled="disabled"></td></tr>
<tr><td>Skupina:</td><td><select name="role" class="form-control">'. $roles_options .'</select></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Změnit" class="btn btn-default"></td></tr>
</table></form>';
	
} while(0);