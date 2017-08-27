<?php

do {
	
	if (!has_access("admin_roles")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$role = $mysql->query("SELECT `rolename`, `role_id` FROM `roles` WHERE `role_id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($role->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Tato role neexistuje!</strong></div>';
		break;
	}
	
	$role = $role->fetch_assoc();
	
	$page["title"] = 'Správa skupin - smazání';
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		if ($mysql->query("SELECT `id` FROM `users` WHERE `role` = ". $mysql->quote($_GET["id"]) .";")->num_rows) {
			$message = '<div class="alert alert-danger"><strong>Tato skupina není prázdná! Před odstraněním skupiny musíte nejprve přesunout její členy!</strong></div>';
			break;
		}
		
		$mysql->query("DELETE FROM `roles` WHERE `role_id` = ". $mysql->quote($_GET["id"]) .";");
		header("Location: admin.php?p=groups");
		die();
		
	} while(0);
	
	$page["content"] .= $message .'<form method="post"><table style="border-spacing:10px">
	<tr><td>Skupina:</td><td><input type="text" value="'. $role["rolename"] .'" class="form-control" disabled="disabled"></td></tr>
	<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Opravdu odstranit" class="btn btn-danger"></td></tr>
	</table><input type="hidden" name="csrf" value="'. generate_csrf() .'"></form>';
	
} while (0);