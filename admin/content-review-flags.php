<?php

if (!$_SESSION["access_admin_content"] > 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Správa obsahu - nahlášené příspěvky';

if ($_SESSION["access_admin_content_review_flags"] > 0) {
	
	if (isset($_GET["pid"]) and $_GET["a"] == "reject" and validate_csrf($_GET["csrf"])) {
		$post_flags = $mysql->query("SELECT `users`.`id` FROM `flags` INNER JOIN `users` ON `flags`.`user` = `users`.`id` WHERE `post` = ". $mysql->quote($_GET["pid"]) .";");
		if ($post_flags->num_rows > 0) {
			while ($flag = $post_flags->fetch_assoc()) {
				$users[] = $flag["id"];
			}
		}
		$mysql->query("UPDATE `users` SET `flags_incorrect` = `flags_incorrect` + 1 WHERE `id` IN (". implode(',', array_map('intval', $users)) .");");
		$mysql->query("DELETE FROM `flags` WHERE `post` = ". $mysql->quote($_GET["pid"]) .";");
		header("Location: admin.php?p=content-review-flags");
		die();
	} elseif (isset($_GET["pid"]) and isset($_GET["fid"]) and $_GET["a"] == "accept" and validate_csrf($_GET["csrf"])) {
		$post_flags = $mysql->query("SELECT `users`.`id` FROM `flags` INNER JOIN `users` ON `flags`.`user` = `users`.`id` WHERE `post` = ". $mysql->quote($_GET["pid"]) ." AND `reason` = ". $mysql->quote($_GET["fid"]) .";");
		if ($post_flags->num_rows > 0) {
			while ($flag = $post_flags->fetch_assoc()) {
				$users[] = $flag["id"];
			}
		}
		$mysql->query("UPDATE `users` SET `flags_correct` = `flags_correct` + 1 WHERE `id` IN (". implode(',', array_map('intval', $users)) .");");
		$mysql->query("DELETE FROM `flags` WHERE `post` = ". $mysql->quote($_GET["pid"]) ." AND `reason` = ". $mysql->quote($_GET["pid"]) .";");
		
		$post_flags = null; $flag = null; $users = null;
		
		$post_flags = $mysql->query("SELECT `users`.`id` FROM `flags` INNER JOIN `users` ON `flags`.`user` = `users`.`id` WHERE `post` = ". $mysql->quote($_GET["pid"]) .";");
		if ($post_flags->num_rows > 0) {
			while ($flag = $post_flags->fetch_assoc()) {
				$users[] = $flag["id"];
			}
		}
		$mysql->query("UPDATE `users` SET `flags_incorrect` = `flags_incorrect` + 1 WHERE `id` IN (". implode(',', array_map('intval', $users)) .");");
		$mysql->query("DELETE FROM `flags` WHERE `post` = ". $mysql->quote($_GET["pid"]) .";");
		
		$mysql->query("UPDATE `posts` SET `deleted` = 1, `deleted_by` = ". $mysql->quote($_SESSION["id"]) .", `deleted_reason` = ". $mysql->quote($flag_reasons[$_GET["fid"]]) ." WHERE `posts`.`post_id` = ". $mysql->quote($_GET["pid"]) .";");
		
		header("Location: admin.php?p=content-review-flags");
		die();
	}

	$page["content"] .= "<p>Tyto příspěvky byly nahlášeny. Zde můžete nahlášení zamítnout nebo příspěvky zlikvidovat.</p>";

	if (!isset($_GET["page"]) or !is_numeric($_GET["page"])) {
		$_GET["page"] = 1;
	}
	
	$csrf_token = generate_csrf();
	$page["content"] .= $message;

	$post_count = $mysql->query("SELECT `flags`.`id` FROM `flags`;")->num_rows;

	if ($post_count > $sys["paging"]) {
		$page_count = ceil($post_count / $sys["paging"]);
		$paging = '<p>Stránky: ';
		for ($i = 1; $i <= $page_count; $i++) {
			if ($_GET["page"] == $i) {
				$paging .= '<a href="admin.php?p=content-review-flags&page='. $i .'" class="btn btn-warning btn-sm">'. $i .'</a> ';
			} else {
				$paging .= '<a href="admin.php?p=content-review-flags&page='. $i .'" class="btn btn-primary btn-sm">'. $i .'</a> ';
			}
		}
		$paging .= '</p>';
	}

	$posts = $mysql->query("SELECT DISTINCT `posts`.*, `users`.`id`, `users`.`username`, `roles`.`color`, `roles`.`level`, `pages`.`title`, `topics`.`name`
	FROM `flags` INNER JOIN	`posts` ON `flags`.`post` = `posts`.`post_id`
	INNER JOIN `pages` ON  `posts`.`location` = `pages`.`id`
	LEFT JOIN `topics` ON  `posts`.`sublocation` = `topics`.`id`
	LEFT JOIN `users` ON `posts`.`author` = `users`.`id`
	LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id`
	ORDER BY `time` DESC
	LIMIT ". ($sys["paging"] * ($_GET["page"] - 1)) .", ". ($sys["paging"] * $_GET["page"] - 1) .";");

	if ($posts->num_rows > 0) {

		$page["content"] .= $paging;

		while ($post = $posts->fetch_assoc()) {
		
			if ($post["username"] != null) {
				$author = '<span style="color:'. $post["color"] .'">'. $post["username"] .'</span>';
			} else {
				$author = '<span class="italic" title="Neregistrován">'. $post["anon_author"] .'</span>';
			}
			
			if ($post["name"] != null) {
				$topic = '; téma: ' . $post["name"];
			} else {
				$topic = null;
			}
			
			$flags = $mysql->query("SELECT `flags`.`reason` FROM `flags` WHERE `post` = ". $mysql->quote($post["post_id"]) ." ORDER BY `reason`;");
			if ($flags->num_rows > 0) {
				while ($flag = $flags->fetch_assoc()) {
					$reports[$flag["reason"]] ++;
				}
				foreach ($reports as $reason => $count) {
					$report_output .= $flag_reasons[$reason] . " (". $count .') <a href="admin.php?p=content-review-flags&a=accept&pid='. $post["post_id"] .'&fid='. $reason .'&csrf='. $csrf_token .'" class="btn btn-primary btn-xs">Přijmout a smazat příspěvek</a><br>';
				}
				$flag = $report_output .'<a href="admin.php?p=content-review-flags&a=reject&pid='. $post["post_id"] .'&csrf='. $csrf_token .'" class="btn btn-danger btn-xs">Odmítnout</a>';
			}
			
			$page["content"] .= '<div class="panel panel-default"><div class="panel-heading"><small>Odeslal(a) '. $author .'; stránka: '. $post["title"] . $topic .'<span class="rfloat">'. date("j.n.Y G:i", strtotime($post["time"])) .'<span class="separate-horizontal"></span></span></small></div><div class="panel-body">'. $post["content"] .'</div><div class="panel-footer panel-footer-warn">'. $flag .'</div></div>';
		
		}
		
		$page["content"] .= $paging;
	
	} else {
		
		$page["content"] = '<div class="alert alert-info"><strong>Revize: žádné příspěvky k revizi</strong></div><a href="admin.php?p=content" class="btn btn-primary">Zpět</a>';
		
	}

} else {
	$page["content"] = '<div class="alert alert-danger"><strong>Revize: chybí oprávnění!</strong></div>';
}

}