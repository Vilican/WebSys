<?php

if (!defined("_PW")) {
	die();
}

if ($page["content"] != null) {
	$page["content"] .= '<hr>';
}

if (!isset($_GET["page"]) or !is_numeric($_GET["page"])) {
	$_GET["page"] = 1;
}

if ($_GET["a"] == "flag" and $_GET["id"] != null and $_SESSION["access_flag"] > 0) {

	$post = $mysql->query("SELECT `post_id` FROM `posts` WHERE `location` = ". $mysql->quote($_GET["p"]) ." AND `deleted` = 0 AND `post_id` = ". $mysql->quote($_GET["id"]));
	if ($post->num_rows > 0) {

		if (isset($_POST["submit"]) and validate_csrf($_POST["csrf"])) {
			$mysql->query("INSERT INTO `flags` (`user`, `post`, `reason`) VALUES (". $_SESSION["id"] .", ". $mysql->quote($_GET["id"]) .", ". $mysql->quote($_POST["reason"]) .");");
			header("Location: index.php?p=". $_GET["p"]);
			die();
		} elseif (isset($_POST["submit"])) {
			$message = '<div class="alert alert-danger"><strong>CSRF: token nesouhlasí; to může znamenat útok!</strong></div>';
		}
	
		foreach ($flag_reasons as $value => $title) {
			$reasons .= '<option value="'. $value .'">'. $title .'</option>';
		}
	
		$csrf_token = generate_csrf();
		$page["content"] .= '<form method="post"><select name="reason" class="form-control separate">'. $reasons .'</select>
<button type="submit" name="submit" class="btn btn-warning btn-sm">Nahlásit příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-primary btn-sm">Zrušit</a>
<input type="hidden" name="csrf" value="'. $csrf_token .'"></form><hr>';

	} else {
		
		$message = '<div class="alert alert-danger"><strong>Nahlášení: neznámý příspěvek!</strong></div>';
		
	}
	
} elseif ($_GET["a"] == "del" and $_GET["id"] != null) {
	$post = $mysql->query("SELECT `posts`.*, `users`.`id`, `users`.`username`, `roles`.`color`, `roles`.`level` FROM `posts` LEFT JOIN `users` ON `posts`.`author` = `users`.`id` LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `location` = ". $mysql->quote($_GET["p"]) ." AND `deleted` = 0 AND `post_id` = ". $mysql->quote($_GET["id"]));
	if ($post->num_rows > 0) {
		$post = $post->fetch_assoc();
		if (($_SESSION["access_posts_delete"] > 0 and $_SESSION["level"] > $post["level"]) or $page["author"] == $_SESSION["id"] or (isset($_SESSION["id"]) and $_SESSION["id"] == 0) or ($post["id"] == $_SESSION["id"] and strtotime($post["time"]) + $sys["authoredittime"] + 60 > time())) {
			
			if (isset($_POST["submit"]) and validate_csrf($_POST["csrf"])) {
				$mysql->query("UPDATE `posts` SET `deleted` = 1, `deleted_by` = ". $_SESSION["id"] .", `deleted_reason` = ". $purifier->purify($mysql->quote($_POST["reason"])) ." WHERE `location` = ". $mysql->quote($_GET["p"]) ." AND `deleted` = 0 AND `post_id` = ". $mysql->quote($_GET["id"]));
				header("Location: index.php?p=". $_GET["p"]);
				die();
			} elseif (isset($_POST["submit"])) {
				$message = '<div class="alert alert-danger"><strong>CSRF: token nesouhlasí; to může znamenat útok!</strong></div>';
			}
			
			$csrf_token = generate_csrf();
			$page["content"] .= '<form method="post" id="delPost"><input type="text" placeholder="Důvod smazání" name="reason" class="form-control separate">
<button type="submit" name="submit" class="btn btn-danger btn-sm">Smazat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-primary btn-sm">Zrušit</a>
<input type="hidden" name="csrf" value="'. $csrf_token .'"></form><hr>';
			
		} else {
			$message = '<div class="alert alert-danger"><strong>Smazání: přístup odepřen!</strong></div>';
		}
	} else {
		$message = '<div class="alert alert-danger"><strong>Smazání: neznámý příspěvek!</strong></div>';
	}
} elseif ($_GET["a"] == "edit" and $_GET["id"] != null) {
	$post = $mysql->query("SELECT `posts`.*, `users`.`id`, `users`.`username`, `roles`.`color`, `roles`.`level` FROM `posts` LEFT JOIN `users` ON `posts`.`author` = `users`.`id` LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `location` = ". $mysql->quote($_GET["p"]) ." AND `deleted` = 0 AND `post_id` = ". $mysql->quote($_GET["id"]));
	if ($post->num_rows > 0) {
		$post = $post->fetch_assoc();
		if (($_SESSION["access_posts_edit"] > 0 and $_SESSION["level"] > $post["level"]) or $page["author"] == $_SESSION["id"] or (isset($_SESSION["id"]) and $_SESSION["id"] == 0) or ($post["id"] == $_SESSION["id"] and strtotime($post["time"]) + $sys["authoredittime"] + 60 > time())) {
			
			$editpost = true;
			$csrf_token = generate_csrf();
			
			if ($post["username"] != null) {
				
			
				if ($_SESSION["access_nocaptcha"] == 0) {
				
					$page["content"] .= '<form method="post" id="editPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><textarea placeholder="Text" name="post" class="form-control separate">'. $post["content"] .'</textarea>
<button type="submit" class="btn btn-primary btn-sm">Upravit příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
<img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input placeholder="Captcha" type="text" name="captcha" class="form-control separate"></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"><input type="hidden" name="post_id" value="'. $post["post_id"] .'"></form><hr>';
		
				} else {
		
					$page["content"] .= '<form method="post" id="editPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><textarea placeholder="Text" name="post" class="form-control separate">'. $post["content"] .'</textarea>
<button type="submit" class="btn btn-primary btn-sm">Upravit příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"><input type="hidden" name="csrf" value="'. $csrf_token .'">
<input type="hidden" name="post_id" value="'. $post["post_id"] .'"></form><hr>';
		
				}
			
			} else {
				
				if ($_SESSION["access_nocaptcha"] == 0) {
				
					$page["content"] .= '<form method="post" id="editPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><input placeholder="Jméno" type="text" name="name" class="form-control separate" value="'. $post["anon_author"] .'">
<img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input placeholder="Captcha" type="text" name="captcha" class="form-control separate"></div>
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><textarea placeholder="Text" name="post" class="form-control separate">'. $post["content"] .'</textarea>
<button type="submit" class="btn btn-primary btn-sm">Upravit příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"><input type="hidden" name="post_id" value="'. $post["post_id"] .'"></form><hr>';
		
				} else {
		
					$page["content"] .= '<form method="post" id="editPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><input placeholder="Jméno" type="text" name="name" class="form-control separate" value="'. $post["anon_author"] .'">
<button type="submit" class="btn btn-primary btn-sm">Upravit příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div>
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><textarea placeholder="Text" name="post" class="form-control separate">'. $post["content"] .'</textarea></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"><input type="hidden" name="post_id" value="'. $post["post_id"] .'">
<input type="hidden" name="csrf" value="'. $csrf_token .'"></form><hr>';
		
				}
				
			}
			
		} else {
			$message = '<div class="alert alert-danger"><strong>Editace: přístup odepřen!</strong></div>';
		}
	} else {
		$message = '<div class="alert alert-danger"><strong>Editace: neznámý příspěvek!</strong></div>';
	}
} elseif ((($page["param1"] == 0 and $sys["anonymousposts"] == 1) or ($page["param1"] <= $_SESSION["level"] and $_SESSION["access_addpost"] > 0)) and isset($_GET["post"])) {
	
	$sendpost = true;
	$csrf_token = generate_csrf();
	
	if (!isset($_SESSION["id"])) {
	
		$page["content"] .= '<form method="post" id="sendPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><input placeholder="Jméno" type="text" name="name" class="form-control separate">
<img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input placeholder="Captcha" type="text" name="captcha" class="form-control separate"></div>
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><textarea placeholder="Text" name="post" class="form-control separate"></textarea>
<button type="submit" class="btn btn-primary btn-sm">Přidat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"></form><hr>';

	} elseif ($_SESSION["access_nocaptcha"] == 0) {
		
		$page["content"] .= '<form method="post" id="sendPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><textarea placeholder="Text" name="post" class="form-control separate"></textarea>
<button type="submit" class="btn btn-primary btn-sm">Přidat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div><div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
<img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input placeholder="Captcha" type="text" name="captcha" class="form-control separate"></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"></form><hr>';
		
	} else {
		
		$page["content"] .= '<form method="post" id="sendPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><textarea placeholder="Text" name="post" class="form-control separate"></textarea>
<button type="submit" class="btn btn-primary btn-sm">Přidat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"><input type="hidden" name="csrf" value="'. $csrf_token .'"></form><hr>';
		
	}

} elseif (($page["param1"] == 0 and $sys["anonymousposts"] == 1) or ($page["param1"] <= $_SESSION["level"] and $_SESSION["access_addpost"] > 0)) {
	
	$page["content"] .= '<a href="index.php?p='. $_GET["p"] .'&post" class="btn btn-primary btn-sm">Přidat příspěvek</a><hr>';
	
}

$page["content"] .= $message;

$post_count = $mysql->query("SELECT `posts`.`post_id` FROM `posts` WHERE `location` = ". $mysql->quote($_GET["p"]) ." AND `deleted` = 0;")->num_rows;

if ($post_count > $sys["paging"]) {
	$page_count = ceil($post_count / $sys["paging"]);
	$paging = '<p>Stránky: ';
	for ($i = 1; $i <= $page_count; $i++) {
		if ($_GET["page"] == $i) {
			$paging .= '<a href="index.php?p='. $_GET["p"] .'&page='. $i .'" class="btn btn-warning btn-sm">'. $i .'</a> ';
		} else {
			$paging .= '<a href="index.php?p='. $_GET["p"] .'&page='. $i .'" class="btn btn-primary btn-sm">'. $i .'</a> ';
		}
	}
	$paging .= '</p>';
}

$posts = $mysql->query("SELECT `posts`.*, `users`.`id`, `users`.`username`, `roles`.`color`, `roles`.`level` FROM `posts` LEFT JOIN `users` ON `posts`.`author` = `users`.`id` LEFT JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `location` = ". $mysql->quote($_GET["p"]) ." AND `deleted` = 0 ORDER BY `time` DESC LIMIT ". ($sys["paging"] * ($_GET["page"] - 1)) .", ". ($sys["paging"] * $_GET["page"] - 1) .";");

if ($posts->num_rows > 0) {

	$page["content"] .= $paging;

	while ($post = $posts->fetch_assoc()) {
		
		if ($post["username"] != null) {
			$author = '<span style="color:'. $post["color"] .'">'. $post["username"] .'</span>';
		} else {
			$author = '<span class="italic" title="Neregistrován">'. $post["anon_author"] .'</span>';
		}
		
		$actions = null;
		if (($_SESSION["access_posts_edit"] > 0 and $_SESSION["level"] > $post["level"]) or $page["author"] == $_SESSION["id"] or (isset($_SESSION["id"]) and $_SESSION["id"] == 0) or ($post["id"] == $_SESSION["id"] and strtotime($post["time"]) + $sys["authoredittime"] > time())) {
			$actions .= ' <a href="index.php?p='. $_GET["p"] .'&a=edit&id='. $post["post_id"] .'&page='. $_GET["page"] .'"><img src="template/img/edit.png" alt="Upravit" title="Upravit" class="icon"></a>';
		}
		if (($_SESSION["access_posts_delete"] > 0 and $_SESSION["level"] > $post["level"]) or $page["author"] == $_SESSION["id"] or (isset($_SESSION["id"]) and $_SESSION["id"] == 0) or ($post["id"] == $_SESSION["id"] and strtotime($post["time"]) + $sys["authoredittime"] > time())) {
			$actions .= ' <a href="index.php?p='. $_GET["p"] .'&a=del&id='. $post["post_id"] .'&page='. $_GET["page"] .'"><img src="template/img/delete.png" alt="Smazat" title="Smazat" class="icon"></a>';
		}
		if ($_SESSION["access_flag"] > 0 and $_SESSION["access_admin_content_review_flags"] == 0 and $sys["flags"] == 1) {
			$flags = $mysql->query("SELECT `id` FROM `flags` WHERE `post` = ". $mysql->quote($post["post_id"]) ." AND `user` = ". $_SESSION["id"]);
			if ($flags->num_rows > 0) {
				$actions .= ' <img src="template/img/flaged.png" alt="Nahlášeno" title="Nahlášeno" class="icon">';
			} else {
				$actions .= ' <a href="index.php?p='. $_GET["p"] .'&a=flag&id='. $post["post_id"] .'&page='. $_GET["page"] .'"><img src="template/img/flag.png" alt="Nahlásit" title="Nahlásit" class="icon"></a>';
			}
		}
		$flag = null;
		if ($_SESSION["access_admin_content_review_flags"] > 0 and $sys["flags"] == 1) {
			$jsflag = true;
			$flags = $mysql->query("SELECT `flags`.`reason` FROM `flags` WHERE `post` = ". $mysql->quote($post["post_id"]) ." ORDER BY `reason`;");
			if ($flags->num_rows > 0) {
				while ($flag = $flags->fetch_assoc()) {
					$reports[$flag["reason"]] ++;
				}
				foreach ($reports as $reason => $count) {
					$report_output .= $flag_reasons[$reason] . " (". $count .') <button type="button" name="accept" class="btn btn-primary btn-xs" onclick="flag('. $reason .', '. $post["post_id"] .');this.disabled=true">Přijmout</button><br>';
				}
				$flag = ' <div class="panel-footer panel-footer-warn"><img src="template/img/flaged.png" title="Nahlášeno" alt="Nahlášeno"> <span class="text-danger">NAHLÁŠENO</span><br>'. $report_output .'<button type="button" name="reject" class="btn btn-danger btn-xs" onclick="flag(\'reject\', '. $post["post_id"] .');this.disabled=true">Odmítnout</button></div>';
			}
		}
		
		$page["content"] .= '<div class="panel panel-default"><div class="panel-heading"><small>Odeslal(a) '. $author .'<span class="rfloat">'. date("j.n.Y G:i", strtotime($post["time"])) .'<span class="separate-horizontal">'. $actions .'</span></span></small></div><div class="panel-body">'. $post["content"] .'</div>'. $flag .'</div>';
		
	}
		
	if (!isset($csrf_token)) {
		$csrf_token = generate_csrf();
	}
	$page["content"] .= $paging . '<input type="hidden" id="csrf-posts" value="'. $csrf_token .'">';
	
}