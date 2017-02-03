<?php

if (!$_SESSION["access_admin_content"] > 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Správa obsahu - smazané příspěvky';

if (isset($_GET["purge"]) and $_SESSION["access_admin_content_review_deleted_purge"] > 0) {
	
	if (isset($_POST["submit"]) and validate_csrf($_POST["csrf"])) {
		
		$mysql->query("DELETE FROM `posts` WHERE `deleted` = 1;");
		$page["content"] = '<div class="alert alert-success"><strong>Smazané příspěvky byly odstraněny</strong></div><a href="admin.php?p=content" class="btn btn-primary">Zpět</a>';
		
	} else {
	
		$page["content"] .= '<form method="post"><p class="text-danger">Všechny příspěvky, které byly smazány, budou <span class="underline">bez revize permanentně odstraněny</span>. Opravdu?</p>
<input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Ano, odstranit" class="btn btn-danger"> <a href="admin.php?p=content" class="btn btn-primary">Zpět</a>
</form>';

	}

} elseif ($_SESSION["access_admin_content_review_deleted"] > 0) {
	
	if (isset($_GET["id"]) and $_GET["a"] == "del" and validate_csrf($_GET["csrf"])) {
		$mysql->query("DELETE FROM `posts` WHERE `post_id` = ". $mysql->quote($_GET["id"]) ." AND `deleted` = 1;");
		header("Location: admin.php?p=content-review-deleted");
		die();
	} elseif (isset($_GET["id"]) and $_GET["a"] == "res" and validate_csrf($_GET["csrf"])) {
		$mysql->query("UPDATE `posts` SET `deleted` = NULL, `deleted_by` = NULL, `deleted_reason` = NULL WHERE `post_id` = ". $mysql->quote($_GET["id"]) .";");
		header("Location: admin.php?p=content-review-deleted");
		die();
	}

	$page["content"] .= "<p>Tyto příspěvky byly smazány. Zde je můžete odstranit nebo obnovit.</p>";

	if (!isset($_GET["page"]) or !is_numeric($_GET["page"])) {
		$_GET["page"] = 1;
	}
	
	$csrf_token = generate_csrf();
	$page["content"] .= $message;

	$post_count = $mysql->query("SELECT `posts`.`post_id` FROM `posts` WHERE `deleted` = 1;")->num_rows;

	if ($post_count > $sys["paging"]) {
		$page_count = ceil($post_count / $sys["paging"]);
		$paging = '<p>Stránky: ';
		for ($i = 1; $i <= $page_count; $i++) {
			if ($_GET["page"] == $i) {
				$paging .= '<a href="admin.php?p=content-review-deleted&page='. $i .'" class="btn btn-warning btn-sm">'. $i .'</a> ';
			} else {
				$paging .= '<a href="admin.php?p=content-review-deleted&page='. $i .'" class="btn btn-primary btn-sm">'. $i .'</a> ';
			}
		}
		$paging .= '</p>';
	}

	$posts = $mysql->query("SELECT `posts`.*, `users`.`id`, `users`.`username`, `roles`.`color`, `roles`.`level`, `pages`.`title`, `topics`.`name`
	FROM `posts` INNER JOIN `pages` ON  `posts`.`location` = `pages`.`id`
	LEFT JOIN `topics` ON  `posts`.`sublocation` = `topics`.`id`
	LEFT JOIN `users` ON `posts`.`author` = `users`.`id`
	LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id`
	WHERE `deleted` = 1 ORDER BY `time` DESC
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
			
			if ($post["deleted_reason"] != null) {
				$reason = " Důvod: " . $post["deleted_reason"];
			} else {
				$reason = null;
			}
			
			$del_by = $mysql->query("SELECT `users`.`username` FROM `posts` INNER JOIN `users` ON `posts`.`deleted_by` = `users`.`id` WHERE `post_id` = ". $post["post_id"] .";")->fetch_assoc();
		
			$actions = 'Tento příspěvek byl smazán.'. $reason .' Smazal: '. $del_by["username"] .' <a href="admin.php?p=content-review-deleted&a=del&id='. $post["post_id"] .'&csrf='. $csrf_token .'" class="btn btn-danger btn-sm separate-horizontal">Odstranit</a> <a href="admin.php?p=content-review-deleted&a=res&id='. $post["post_id"] .'&csrf='. $csrf_token .'" class="btn btn-primary btn-sm">Obnovit</a>';

			$page["content"] .= '<div class="panel panel-default"><div class="panel-heading"><small>Odeslal(a) '. $author .'; stránka: '. $post["title"] . $topic .'<span class="rfloat">'. date("j.n.Y G:i", strtotime($post["time"])) .'<span class="separate-horizontal"></span></span></small></div><div class="panel-body">'. $post["content"] .'</div><div class="panel-footer panel-footer-warn">'. $actions .'</div></div>';
		
		}
		
		$page["content"] .= $paging;
	
	} else {
		
		$page["content"] = '<div class="alert alert-info"><strong>Revize: žádné příspěvky k revizi</strong></div><a href="admin.php?p=content" class="btn btn-primary">Zpět</a>';
		
	}

} else {
	$page["content"] = '<div class="alert alert-danger"><strong>Revize: chybí oprávnění!</strong></div>';
}

} ?>