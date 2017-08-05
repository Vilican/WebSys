<?php

if (!defined("_PW")) {
	die();
}

do {
	
	if (!isset($_GET["page"]) or !is_numeric($_GET["page"])) {
		$_GET["page"] = 1;
	}
	
	$article = $mysql->query("SELECT `articles`.*, `users`.`username` FROM `articles` INNER JOIN `users` ON `articles`.`author` = `users`.`id` WHERE `articles`.`location` = ". $mysql->quote($page["id"]) ." AND `approved` = 1 AND `articles`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($article->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Tento článek neexistuje!</strong></div>';
		break;
	}
	
	$article = $article->fetch_assoc();
	
	$page["title"] = "Článek: " . $article["title"];
	$page["content"] = '<p>Napsal '. id_to_user($article["username"]) .', '. date("j.n.Y G:i", strtotime($article["date"])) .'</p><hr>'. $article["text"];
	
} while(0);