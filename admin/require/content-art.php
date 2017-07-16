<?php

$page["content"] .= '<div id="articles" class="tab-pane fade">';

if (has_access("admin_content_articles_edit") or has_access("admin_content_articles_edit_all")) {
	$page["content"] .= '<p><a href="admin.php?p=content-new-article" class="btn btn-primary btn-xs">Přidat článek</a></p>';
}

$page["content"] .= '<table class="table table-hover table-hover table-bordered table-responsive"><thead><tr><th>ID</th><th>Titulek</th><th>Autor</th><th>Kategorie</th><th>Publikováno</th><th>Akce</th></thead><tbody>';

$articles = $mysql->query("SELECT `articles`.`id`, `articles`.`author`, `articles`.`title`, `pages`.`title` AS `location`, `articles`.`approved`, `articles`.`date`, `users`.`username` FROM `articles` INNER JOIN `users` ON `articles`.`author` = `users`.`id` INNER JOIN `pages` ON `articles`.`location` = `pages`.`id` ORDER BY `articles`.`date` DESC;");
if ($articles->num_rows > 0) {
	while($article = $articles->fetch_assoc()) {
		$actions = null;
		if (has_access("admin_content_articles_edit_all") or (has_access("admin_content_articles_edit") and $article["author"] == $_SESSION["id"])) {
			if ($article["approved"] != 1 or has_access("admin_content_articles_edit_approved")) {
				$actions = '<a href="admin.php?p=content-edit-article&id='. $article["id"] .'" class="btn btn-primary">Upravit</a>';
			}
		}
		if (has_access("admin_content_articles_changeeditor")) {
			$actions .= '<a href="admin.php?p=content-change-author-article&id='. $article["id"] .'" class="btn btn-warning">Změnit autora</a>';
		}
		if (has_access("admin_content_articles_delete_all") or (has_access("admin_content_articles_delete") and $article["author"] == $_SESSION["id"])) {
			$actions .= '<a href="admin.php?p=content-delete-article&id='. $article["id"] .'" class="btn btn-danger">Smazat</a>';
		}
		$unapproved = null;
		if ($article["approved"] == 0) {
			$unapproved = '<small class="label label-danger label-small" data-toggle="tooltip" title="" data-original-title="Zatím neschváleno">X</small>';
		}
		$page["content"] .= "<tr><td>". $article["id"] ."</td><td>". $article["title"] ." ". $unapproved ."</td><td>". $article["username"] ."</td><td>". $article["location"] ."</td><td>". date("j.n.Y G:i", strtotime($article["date"])) .'</td><td><div class="btn-group btn-group-sm">'. $actions .'</div></td></tr>';
	}
}

$page["content"] .= '</tbody></table></div>';