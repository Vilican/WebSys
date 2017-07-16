<?php

do {
	
	if (!has_access("admin_content") or !has_access("admin_content_review_articles")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	if (isset($_GET["approve"])) do {
		
		if (!validate_csrf($_GET["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token! To může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		if (empty($_GET["id"])) {
			$message = '<div class="alert alert-danger"><strong>ID je prázdné!</strong></div>';
			break;
		}
		
		$mysql->query("UPDATE `articles` SET `date` = NOW(), `approved` = 1, `approved_by` = ". $mysql->quote($_SESSION["id"]) ." WHERE `id` = ". $mysql->quote($_GET["id"]) .";");
		
		header("Location: admin.php?p=content-review-articles");
		die();
		
	} while(0);
	
	$page["title"] = 'Správa obsahu - neschválené články';
	$page["content"] = $message ."<p>Tyto články nebyly dosud schváleny. Zde je můžete jednoduše schvalovat.</p>";
	$page["content"] .= '<table class="table table-hover table-hover table-bordered table-responsive"><thead><tr><th>ID</th><th>Titulek</th><th>Autor</th><th>Kategorie</th><th>Publikováno</th><th>Akce</th></thead><tbody>';
	
	$csrf = generate_csrf();
	
	$articles = $mysql->query("SELECT `articles`.`id`, `articles`.`author`, `articles`.`title`, `pages`.`title` AS `location`, `articles`.`date`, `users`.`username` FROM `articles` INNER JOIN `users` ON `articles`.`author` = `users`.`id` INNER JOIN `pages` ON `articles`.`location` = `pages`.`id` WHERE `articles`.`approved` = 0 ORDER BY `articles`.`date` DESC;");
	if ($articles->num_rows > 0) {
		while($article = $articles->fetch_assoc()) {
			$actions = '<a href="admin.php?p=content-show-article&id='. $article["id"] .'" class="btn btn-info" target="_blank">Zobrazit</a>';
			$actions .= '<a href="admin.php?p=content-review-articles&approve&id='. $article["id"] .'&csrf='. $csrf .'" class="btn btn-warning">Schválit</a>';
			$page["content"] .= "<tr><td>". $article["id"] ."</td><td>". $article["title"] ."</td><td>". $article["username"] ."</td><td>". $article["location"] ."</td><td>". date("j.n.Y G:i", strtotime($article["date"])) .'</td><td><div class="btn-group btn-group-sm">'. $actions .'</div></td></tr>';
		}
	}

	$page["content"] .= '</tbody></table>';

} while(0);