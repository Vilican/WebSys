<?php

do {
	
	if (!has_access("admin_content") or !has_access("admin_content_review_deleted")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - smazané příspěvky';
	$page["content"] .= "<p>Tyto příspěvky byly smazádny. Zde můžete příspěvky obnovit nebo odstranit.</p>";
	
	if (!isset($_GET["page"]) or !is_numeric($_GET["page"])) {
		$_GET["page"] = 1;
	}
	
	if ($_GET["act"] == "undo" and is_numeric($_GET["id"]) and validate_csrf($_GET["csrf"])) {
		$mysql->query("UPDATE `posts` SET `deleted` = '0', `deleted_by` = null WHERE `post_id` = ". $mysql->quote($_GET["id"]) .";");
		header("Location: admin.php?p=content-review-deleted");
		die();
	}
	
	if ($_GET["act"] == "del" and is_numeric($_GET["id"]) and validate_csrf($_GET["csrf"])) {
		$mysql->query("DELETE FROM `posts` WHERE `post_id` = ". $mysql->quote($_GET["id"]) .";");
		header("Location: admin.php?p=content-review-deleted");
		die();
	}
	
	$post_count = $mysql->query("SELECT `post_id` FROM `posts` WHERE `deleted` = 1;")->num_rows;
	
	if ($post_count == 0) {
		$page["content"] = '<div class="alert alert-info"><strong>Revize: žádné příspěvky k revizi</strong></div><a href="admin.php?p=content" class="btn btn-primary">Zpět</a>';
		break;
	}
	
	if ($post_count > $sys["paging"]) {
		$page_count = ceil($post_count / $sys["paging"]);
		$paging = '<ul class="pagination">';
		for ($i = 1; $i <= $page_count; $i++) {
			if ($_GET["page"] == $i) {
				$paging .= '<li class="active"><a href="admin.php?p=content-review-deleted&page='. $i .'">'. $i .'</a></li>';
				continue;
			}
			$paging .= '<li><a href="admin.php?p=content-review-deleted&page='. $i .'">'. $i .'</a></li>';
		}
		$paging .= '</p>';
	}
	
	$posts = $mysql->query("SELECT `posts`.*, `users`.`id`, `users`.`username`, `roles`.`color`, `roles`.`level`, `pages`.`title`, `topics`.`name`
	FROM `posts` INNER JOIN `pages` ON `posts`.`location` = `pages`.`id`
	LEFT JOIN `topics` ON `posts`.`sublocation` = `topics`.`id`
	LEFT JOIN `users` ON `posts`.`author` = `users`.`id`
	LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id`
	WHERE `posts`.`deleted` = 1
	ORDER BY `time` DESC
	LIMIT ". ($sys["paging"] * ($_GET["page"] - 1)) .", ". ($sys["paging"] * $_GET["page"] - 1) .";");
	
	$page["content"] .= $paging;
	$csrf_token = generate_csrf();
	
	while ($post = $posts->fetch_assoc()) {
		
		if (empty($post["username"])) {
			if (has_access("posts_showip")) {
				$post["username"] = '<i data-toggle="tooltip" title="IP: '. $post["anon_ip"] .'">'. $post["anon_author"] .'</i>';
			} else {
				$post["username"] = '<i data-toggle="tooltip" title="Neregistrován">'. $post["anon_author"] .'</i>';
			}
			$post["color"] = "grey";
		} else {
			$post["username"] = '<span style="color:'. $post["color"] .'">'. $post["username"] .'</span>';
		}
		
		if (!empty($post["name"])) {
			$post["name"] = " | vlákno/místnost". $post["name"];
		}
		
		$delby = $mysql->query("SELECT `username` FROM `users` WHERE `id` = ". $mysql->quote($post["deleted_by"]) .";");
		$delby = $delby->fetch_assoc();
		
		$footer = 'Tento příspěvek smazal: '. $delby["username"] .'<br><a href="admin.php?p=content-review-deleted&act=undo&id='. $post["post_id"] .'&csrf='. $csrf_token .'" class="btn btn-warning btn-xs">Obnovit příspěvek</a> <a href="admin.php?p=content-review-deleted&act=del&id='. $post["post_id"] .'&csrf='. $csrf_token .'"class="btn btn-danger btn-xs">Odstranit příspěvek</a>';
		$page["content"] .= '<div class="panel panel-default"><div class="panel-heading">autor '. $post["username"] .' | stránka '. $post["title"] . $thread .'<small class="rfloat">'. date("j.n.Y G:i", strtotime($post["time"])) .' <span id="acts">'. $actions .'</span></small></div><div class="panel-body">'. bb_to_html($post["content"]) .'</div><div class="panel-footer panel-footer-warn">'. $footer .'</div></div>';
		
	}
	
	$page["content"] .= $paging;
	
} while(0);