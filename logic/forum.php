<?php

if (!defined("_PW")) {
	die();
}

if (isset($_GET["modth"])) {
	require "logic/require/thread.php";
	die();
}

do {
	
	if (!empty($_GET["th"])) {
		require "logic/thread.php";
		break;
	}
	
	if (!empty($page["content"])) {
		$page["content"] .= '<hr>';
	}
	
	if (!isset($_GET["page"]) or !is_numeric($_GET["page"])) {
		$_GET["page"] = 1;
	}
	
	$topics_count = $mysql->query("SELECT `id` FROM `topics` WHERE `location` = ". $mysql->quote($page["id"]) ." AND `deleted` = 0;")->num_rows;
	
	if ($topics_count > $sys["paging"]) {
		$page_count = ceil($topics_count / $sys["paging"]);
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
	
	$topics = $mysql->query("SELECT `topics`.`id`, `topics`.`name`, `topics`.`user`, `users`.`username`, `roles`.`color` FROM `topics` LEFT JOIN `users` ON `topics`.`user` = `users`.`id` LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `topics`.`location` = ". $mysql->quote($page["id"]) ." AND `deleted` = 0 ORDER BY `topics`.`lastact` DESC LIMIT ". ($sys["paging"] * ($_GET["page"] - 1)) .", ". ($sys["paging"] * $_GET["page"] - 1) .";");
	
	if ($topics->num_rows > 0) {
		
		if (($page["param2"] <= $_SESSION["level"] or $_SESSION["id"] == 0) and isset($_SESSION["id"])) {
			$page["content"] .= '<p><button type="button" class="btn btn-primary btn-sm thread-add">Přidat místnost</button></p>';
		}
		
		$page["content"] .= '<p class="text-smaller">Správce fóra: '. id_to_user($page["author"]) .'</p>'. $paging .'<div class="list-group">';
		
		while ($topic = $topics->fetch_assoc()) {
			
			if (!empty($topic["username"])) {
				$topic["username"] = '<p class="list-group-item-text">Moderátor: <span style="color:'. $topic["color"] .'">'. $topic["username"] .'</span></p>';
			}
			
			$actions = null;
			if (has_access("thread_edit") or ($page["author"] == $_SESSION["id"] and isset($_SESSION["id"])) or ($topic["user"] == $_SESSION["id"] and isset($_SESSION["id"]))) {
				$actions = '<img src="template/img/edit.png" class="icon thread-edit" data-toggle="tooltip" title="Upravit" alt="Upravit" data-postid="'. $topic["id"] .'">&nbsp;';
				$jsthread = true;
			}
			
			if (has_access("thread_delete") or ($page["author"] == $_SESSION["id"] and isset($_SESSION["id"])) or ($topic["user"] == $_SESSION["id"] and isset($_SESSION["id"]))) {
				$actions .= '<img src="template/img/delete.png" class="icon thread-del" data-toggle="tooltip" title="Smazat" alt="Smazat" data-postid="'. $topic["id"] .'">&nbsp;';
				$jsthread = true;
			}
			
			$page["content"] .= '<a href="index.php?p='. $page["id"] .'&th='. $topic["id"] .'" class="list-group-item"><p class="list-group-item-heading">'. $topic["name"] .'<span class="rfloat">'. $actions .'</span></p>'. $topic["username"] .'</a>';
			
		}
		
		$page["content"] .= '<input type="hidden" id="csrf" value="'. generate_csrf() .'"></div><div id="modal" class="modal fade" role="dialog"></div>'. $paging;
		
	}
	
} while(0);