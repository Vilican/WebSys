<?php

if (!defined("_PW")) {
	die();
}

do {
	
	if (!empty($page["content"])) {
		$page["content"] .= '<hr>';
	}
	
	if (!isset($_GET["page"]) or !is_numeric($_GET["page"])) {
		$_GET["page"] = 1;
	}
	
	$art_count = $mysql->query("SELECT `id` FROM `articles` WHERE `location` = ". $mysql->quote($page["id"]) ." AND `approved` = 1;")->num_rows;
	
	if ($art_count > $sys["paging"]) {
		$page_count = ceil($art_count / $sys["paging"]);
		$paging = '<ul class="pagination">';
		for ($i = 1; $i <= $page_count; $i++) {
			if ($_GET["page"] == $i) {
				$paging .= '<li class="active"><a href="index.php?p='. santise($_GET["p"]) .'&page='. $i .'">'. $i .'</a></li>';
				continue;
			}
			$paging .= '<li><a href="index.php?p='. santise($_GET["p"]) .'&page='. $i .'">'. $i .'</a></li>';
		}
		$paging .= '</ul>';
	}
	
	$articles = $mysql->query("SELECT `articles`.`id`, `articles`.`title`, `users`.`username`, `articles`.`date`, `articles`.`description` FROM `articles` INNER JOIN `users` ON `articles`.`author` = `users`.`id` WHERE `articles`.`location` = ". $mysql->quote($page["id"]) ." AND `approved` = 1 ORDER BY `articles`.`date` DESC LIMIT ". ($sys["paging"] * ($_GET["page"] - 1)) .", ". ($sys["paging"] * $_GET["page"] - 1) .";");
	
	if ($articles->num_rows > 0) {
		
		$page["content"] .= $paging .'<div class="list-group">';
		
		while ($article = $articles->fetch_assoc()) {
			
			$page["content"] .= '<a href="index.php?p='. santise($_GET["p"]) .'&id='. $article["id"] .'" class="list-group-item mgdown"><p class="list-group-item-heading">'. $article["title"] .'</p><p class="list-group-item-text">'. $article["description"] .'</p></a>';
			
		}
		
		$page["content"] .= '</div>'. $paging;
		
	}
	
} while(0);