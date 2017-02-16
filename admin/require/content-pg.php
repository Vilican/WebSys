<?php

$page["content"] .= '<div id="pages" class="tab-pane fade">';

if (has_access("admin_content_edit") or has_access("admin_content_edit_all")) {
	$page["content"] .= '<p><form method="post" action="admin.php?p=content">
Vytvořit: <select name="type">'. $options .'</select>
<button type="submit" class="btn btn-primary btn-xs">Přidat</button>
</form></p>';
}

$page["content"] .= '<table class="table table-hover table-hover table-bordered table-responsive"><thead><tr><th>ID</th><th>Název</th><th>Editor</th><th>Typ</th><th>Přístup</th><th>Pořadí</th><th>Akce</th></tr></thead><tbody>';

$pages = $mysql->query("SELECT `pages`.`id`, `pages`.`title`, `pages`.`type`, `pages`.`ord`, `pages`.`author`, `pages`.`visible`, `pages`.`access`, `users`.`username` FROM `pages` INNER JOIN `users` ON `pages`.`author` = `users`.`id` ORDER BY `pages`.`ord` ASC;");
if ($pages->num_rows > 0) {
	while($row_page = $pages->fetch_assoc()) {
		$actions = null;
		if ($row_page["access"] == 0) { $row_page["access"] = "všichni"; }
		if (has_access("admin_content_edit_all") or (has_access("admin_content_edit") and $row_page["author"] == $_SESSION["id"])) {
			$actions = '<a href="admin.php?p=content-edit-page&id='. $row_page["id"] .'" class="btn btn-default">Upravit</a>
			<a href="admin.php?p=content-history&id='. $row_page["id"] .'" class="btn btn-primary">Historie</a>';
		}
		if (has_access("admin_content_changeeditor")) {
			$actions .= '<a href="admin.php?p=content-change-author&id='. $row_page["id"] .'" class="btn btn-warning">Změnit editora</a>';
		}
		if (has_access("admin_content_delete_all") or (has_access("admin_content_delete") and $row_page["author"] == $_SESSION["id"])) {
			$actions .= '<a href="admin.php?p=content-delete&id='. $row_page["id"] .'" class="btn btn-danger">Smazat</a>';
		}
		$page["content"] .= "<tr><td>". $row_page["id"] ."</td><td>". $row_page["title"] ."</td><td>". $row_page["username"] ."</td><td>". $page_types[$row_page["type"]] ."</td><td>". $row_page["access"] ."</td><td>". $row_page["ord"] .'</td><td><div class="btn-group btn-group-sm">'. $actions .'</div></td></tr>';
	}
}

$page["content"] .= '</tbody></table></div>';