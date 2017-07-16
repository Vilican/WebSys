<?php

do {
	
	if (!has_access("admin_users")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa uživatelů';
	
	if (has_access("admin_users_edit")) {
		$page["content"] = '<p><a href="admin.php?p=users-add" class="btn btn-default btn-sm">Vytvořit</a></p>';
	}
	
	$page["content"] .= '<table class="table table-striped table-hover table-bordered"><thead><tr><th class="col-md-3">Uživatel</th><th class="col-md-3">Role</th><th class="col-md-5">Akce</th></tr></thead><tbody>';
	
	$user_list = $mysql->query("SELECT `users`.`id`, `users`.`username`, `roles`.`rolename`, `roles`.`level` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` ORDER BY `users`.`username`;");
	
	while($user = $user_list->fetch_assoc()) {
		
		$actions = null;
		
		if (has_access("admin_users_view")) {
			$actions = '<a href="admin.php?p=users-edit&id='. $user["id"] .'" class="btn btn-sm btn-info">Zobrazit</a>';
		}
		
		if (has_access("admin_users_edit") and ($user["level"] < $_SESSION["level"] or $_SESSION["id"] == 0)) {
			$actions = '<a href="admin.php?p=users-edit&id='. $user["id"] .'" class="btn btn-sm btn-primary">Upravit</a>';
		}
		
		if (has_access("admin_users_pass") and ($user["level"] < $_SESSION["level"] or $_SESSION["id"] == 0) and $user["id"] != 0) {
			$actions .= ' <a href="admin.php?p=users-pass&id='. $user["id"] .'" class="btn btn-sm btn-warning">Heslo</a>';
		}
		
		if (has_access("admin_roles") and ($user["level"] < $_SESSION["level"] or $_SESSION["id"] == 0)) {
			$actions .= ' <a href="admin.php?p=users-group&id='. $user["id"] .'" class="btn btn-sm btn-warning">Skupina</a>';
		}
		
		if (has_access("admin_users_delete") and ($user["level"] < $_SESSION["level"] or $_SESSION["id"] == 0) and $user["id"] != 0) {
			$actions .= ' <a href="admin.php?p=users-delete&id='. $user["id"] .'" class="btn btn-sm btn-danger">Smazat</a>';
		}
		
		$page["content"] .= '<tr><td>'. $user["username"] .'</td><td>'. $user["rolename"] .'</td><td>'. $actions .'</td></tr>';
		
	}
	
	$page["content"] .= '</tbody></table>';
	
} while (0);