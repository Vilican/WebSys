<?php

do {
	
	if (!has_access("admin_content") or !has_access("admin_content_review_flags")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - nahlášené příspěvky';
	$page["content"] .= "<p>Tyto příspěvky byly nahlášeny. Zde můžete příspěvky jednoduše revidovat.</p>";
	
	if (!empty($_GET["sid"])) {
		
		$mysql->query("DELETE FROM `flags` WHERE `post` = ". $mysql->quote($_GET["sid"]) .";");
		
	}
	
	if (!isset($_GET["page"]) or !is_numeric($_GET["page"])) {
		$_GET["page"] = 1;
	}
	
	$post_count = $mysql->query("SELECT DISTINCT `post` FROM `flags`;")->num_rows;
	
	if ($post_count == 0) {
		$page["content"] = '<div class="alert alert-info"><strong>Revize: žádné příspěvky k revizi</strong></div><a href="admin.php?p=content" class="btn btn-primary">Zpět</a>';
		break;
	}
	
	if ($post_count > $sys["paging"]) {
		$page_count = ceil($post_count / $sys["paging"]);
		$paging = '<ul class="pagination">';
		for ($i = 1; $i <= $page_count; $i++) {
			if ($_GET["page"] == $i) {
				$paging .= '<li class="active"><a href="admin.php?p=content-review-flags&page='. $i .'">'. $i .'</a></li>';
				continue;
			}
			$paging .= '<li><a href="admin.php?p=content-review-flags&page='. $i .'">'. $i .'</a></li>';
		}
		$paging .= '</p>';
	}
	
	$posts = $mysql->query("SELECT DISTINCT `posts`.*, `users`.`id`, `users`.`username`, `roles`.`color`, `roles`.`level`, `pages`.`title`, `topics`.`name`
	FROM `flags` INNER JOIN	`posts` ON `flags`.`post` = `posts`.`post_id`
	INNER JOIN `pages` ON `posts`.`location` = `pages`.`id`
	LEFT JOIN `topics` ON `posts`.`sublocation` = `topics`.`id`
	LEFT JOIN `users` ON `posts`.`author` = `users`.`id`
	LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id`
	ORDER BY `time` DESC
	LIMIT ". ($sys["paging"] * ($_GET["page"] - 1)) .", ". ($sys["paging"] * $_GET["page"] - 1) .";");
	
	$page["content"] .= $paging;
	
	while ($post = $posts->fetch_assoc()) {
		
		$addr = 'index.php?p='. $post["location"];
		
		if (empty($post["username"])) {
			if (has_access("posts_showip")) {
				$post["username"] = '<i data-toggle="tooltip" title="IP: '. $post["anon_ip"] .'">'. $post["anon_author"] .'</i>';
			} else {
				$post["username"] = '<i data-toggle="tooltip" title="Neregistrován">'. $post["anon_author"] .'</i>';
			}
			$post["color"] = "grey";
		} else {
			$post["username"] = '<a target="_blank" href="index.php?p=profile&id='. $post["id"] .'" style="color:'. $post["color"] .'">'. $post["username"] .'</a>';
		}
		
		if (!empty($post["name"])) {
			$thread = " | vlákno: ". $post["name"];
			$addr .= "&th=" . $post["sublocation"];
		}
		
		$flaggers = $mysql->query("SELECT `users`.`username` FROM `flags` INNER JOIN `users` ON `flags`.`user` = `users`.`id`;");
		
		$flaggers_list = null;
		while ($flagger = $flaggers->fetch_assoc()) {
			$flaggers_list .= $flagger["username"] .", ";
		}
		
		$footer = 'Uživatelé, kteří nahlásili příspěvek: '. substr($flaggers_list, 0, -2) .'<br><a target="_blank" href="'. $addr .'" class="btn btn-primary btn-xs">Přejít do diskuze</a> <a href="admin.php?p=content-review-flags&sid='. $post["post_id"] .'" class="btn btn-warning btn-xs">Označit jako vyřízené</a>';
		$page["content"] .= '<div class="panel panel-default"><div class="panel-heading">autor '. $post["username"] .' | stránka '. $post["title"] . $thread .'<small class="rfloat">'. date("j.n.Y G:i", strtotime($post["time"])) .' <span id="acts">'. $actions .'</span></small></div><div class="panel-body">'. bb_to_html($post["content"]) .'</div><div class="panel-footer panel-footer-warn">'. $footer .'</div></div>';
		
	}
	
	$page["content"] .= $paging;
	
} while(0);