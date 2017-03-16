<?php

if (!defined("_PW")) {
	die();
}

if (isset($_GET["mod"])) {
	require "logic/postthread.php";
	die();
}

if (!empty($page["content"])) {
	$page["content"] .= '<hr>';
}

if (!isset($_GET["page"]) or !is_numeric($_GET["page"])) {
	$_GET["page"] = 1;
}

$csrf_token = generate_csrf();

do {
	
	if (!$sys["anonymousposts"] and !isset($_SESSION["id"])) {
		break;
	}
	
	if ($page["param1"] != 0 and !(isset($_SESSION["id"]) and $_SESSION["level"] >= $page["param1"])) {
		break;
	}
	
	if ($_GET["act"] == "add") {
		require "logic/require/addpost-thread.php";
		break;
	}
	
	$page["content"] .= '<a href="index.php?p='. santise($_GET["p"]) .'&th='. santise($_GET["th"]) .'&page='. $_GET["page"] .'&act=add" class="btn btn-sm btn-default">Přidat příspěvek</a><hr>';
	
} while (0);

$post_count = $mysql->query("SELECT `posts`.`post_id` FROM `posts` WHERE `location` = ". $mysql->quote($_GET["p"]) ." AND `sublocation` = ". $mysql->quote($_GET["th"]) ." AND `deleted` = 0;")->num_rows;

if ($post_count > $sys["paging"]) {
	$page_count = ceil($post_count / $sys["paging"]);
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

$posts = $mysql->query("SELECT `posts`.`post_id`, `posts`.`author`, `posts`.`anon_author`, `posts`.`anon_ip`, `posts`.`content`, `posts`.`time`, `users`.`username`, `roles`.`level`, `roles`.`color` FROM `posts` LEFT JOIN `users` ON `posts`.`author` = `users`.`id` LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `location` = ". $mysql->quote($_GET["p"]) ." AND `deleted` = 0 ORDER BY `time` DESC LIMIT ". ($sys["paging"] * ($_GET["page"] - 1)) .", ". ($sys["paging"] * $_GET["page"] - 1) .";");

if ($posts->num_rows > 0) {
	
	$page["content"] .= $paging;
	
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
		
		$actions = null;
		if (((has_access("posts_edit") or ($page["author"] == $_SESSION["id"] and isset($_SESSION["id"]))) and ($_SESSION["level"] > $post["level"] or $_SESSION["id"] == 0)) or ($post["author"] == $_SESSION["id"] and isset($_SESSION["id"]) and strtotime($post["time"]) + $sys["authoredittime"] > time())) {
			$actions = '<img src="template/img/edit.png" class="icon post-edit" data-toggle="tooltip" title="Upravit" alt="Upravit" data-postid="'. $post["post_id"] .'">&nbsp;';
			$jspost = true;
		}
		
		if (has_access("posts_delete") or ($page["author"] == $_SESSION["id"] and isset($_SESSION["id"])) or ($post["author"] == $_SESSION["id"] and isset($_SESSION["id"]) and strtotime($post["time"]) + $sys["authoredittime"] > time())) {
			$actions .= '<img src="template/img/delete.png" class="icon post-del" data-toggle="tooltip" title="Smazat" alt="Smazat" data-postid="'. $post["post_id"] .'">&nbsp;';
			$jspost = true;
		}
		
		if (has_access("flag") and $sys["flags"]) {
			$actions .= '<img src="template/img/flag.png" class="icon post-flag" data-toggle="tooltip" title="Nahlásit" alt="Nahlásit" data-postid="'. $post["post_id"] .'">';
			$jspost = true;
		}
		
		$page["content"] .= '<div class="panel panel-default"><div class="panel-heading">'. $post["username"] .'<small class="rfloat">'. date("j.n.Y G:i", strtotime($post["time"])) .' <span id="acts">'. $actions .'</span></small></div><div class="panel-body">'. $post["content"] .'</div></div>';
		
	}
	
	$page["content"] .= $paging;
	
}

$page["content"] .= '<input type="hidden" id="csrf" value="'. $csrf_token .'"><div id="modal" class="modal fade" role="dialog"></div>';