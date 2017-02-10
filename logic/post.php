<?php

if (!is_numeric($_POST["post"])) {
	exit;
}

$post = $mysql->query("SELECT `posts`.`post_id`, `posts`.`anon_author`, `posts`.`author`, `posts`.`time`, `posts`.`content`, `roles`.`level` FROM `posts` LEFT JOIN `users` ON `posts`.`author` = `users`.`id` LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `location` = ". $mysql->quote($_GET["p"]) ." AND `deleted` = 0 AND `post_id` = ". $mysql->quote($_POST["post"]) .";");

if ($post->num_rows == 0) {
	exit;
}

$post = $post->fetch_assoc();

if (($_POST["act"] == "delprep" or $_POST["act"] == "delpost") and (has_access("posts_delete") or ($page["author"] == $_SESSION["id"] and isset($_SESSION["id"])) or ($post["author"] == $_SESSION["id"] and isset($_SESSION["id"]) and strtotime($post["time"]) + $sys["authoredittime"] > time()))) {
	
	if ($_POST["act"] == "delpost") {
		$act = true;
	}
	
	require "logic/require/delpost.php";
	exit;
}

if (($_POST["act"] == "editprep" or $_POST["act"] == "editpost") and (((has_access("posts_edit") or ($page["author"] == $_SESSION["id"] and isset($_SESSION["id"]))) and ($_SESSION["level"] > $post["level"] or $_SESSION["id"] == 0)) or ($post["author"] == $_SESSION["id"] and isset($_SESSION["id"]) and strtotime($post["time"]) + $sys["authoredittime"] > time()))) {
	
	if ($_POST["act"] == "editpost") {
		$act = true;
	}
	
	require "logic/require/editpost.php";
	exit;
}

if (($_POST["act"] == "flagprep" or $_POST["act"] == "flagpost") and has_access("flag")) {
	
	if ($_POST["act"] == "flagpost") {
		$act = true;
	}
	
	require "logic/require/flagpost.php";
	exit;
}