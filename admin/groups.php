<?php

do {
	
	if (!has_access("admin_roles")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa skupin';
	
	$page["content"] = '<p><a href="admin.php?p=groups-add" class="btn btn-default btn-sm">Vytvořit</a></p><table class="table table-striped table-hover table-bordered"><thead><tr><th class="col-md-4">Název</th><th class="col-md-4">Úroveň</th><th class="col-md-4">Akce</th></tr></thead><tbody>';
	
	$role_list = $mysql->query("SELECT `roles`.`role_id`, `roles`.`rolename`, `roles`.`level` FROM `roles` ORDER BY `level` DESC;");
	
	while($role = $role_list->fetch_assoc()) {
		
		$page["content"] .= '<tr><td>'. $role["rolename"] .'</td><td>'. $role["level"] .'</td><td><a href="admin.php?p=groups-edit&id='. $role["role_id"] .'" class="btn btn-sm btn-primary">Upravit</a> <a href="admin.php?p=groups-delete&id='. $role["role_id"] .'" class="btn btn-sm btn-danger">Smazat</a></td></tr>';
		
	}
	
	$page["content"] .= '</tbody></table>';
	
} while(0);