<?php

if (isset($_POST["ap"])) do {
	
	if (!(($page["param1"] == 0 and $sys["anonymousposts"] == 1) or ($page["param1"] <= $_SESSION["level"] and $_SESSION["access_addpost"] > 0))) {
		break;
	}
	
	if (!isset($_SESSION["id"])) {
	
		if (strlen($_POST["name"]) < 3) {
			$msg .= '<div class="alert alert-danger"><strong>Jméno musí mít alespoň 3 znaky!</strong></div>';
			break;
		}
		
		if (strlen($_POST["name"]) > 24) {
			$msg .= '<div class="alert alert-danger"><strong>Jméno smí mít maximálně 24 znaků!</strong></div>';
			break;
		}
	
	}
	
	if (!has_access("nocaptcha") or !isset($_SESSION["id"])) {
	
		if (strtolower($_SESSION['captcha']) != strtolower($_POST["captcha"])) {
			$msg .= '<div class="alert alert-danger"><strong>Chybně opsaný kód z obrázku!</strong></div>';
			break;
		}
	
	} else {
		
		if (!validate_csrf($_POST["csrf"])) {
			$msg .= '<div class="alert alert-danger"><strong>Chyba CSRF!</strong></div>';
			break;
		}
		
	}
	
	if (empty($_POST["post"])) {
		$msg .= '<div class="alert alert-danger"><strong>Příspěvek je prázdný!</strong></div>';
		break;
	}
	
	if (strlen($_POST["post"]) > 1024) {
		$msg .= '<div class="alert alert-danger"><strong>Maximální délka příspěvku je 1024 znaků!</strong></div>';
		break;
	}
	
	if (isset($_POST["name"])) {
		$mysql->query("INSERT INTO `posts` (`anon_author`, `anon_ip`, `location`, `sublocation`, `content`) VALUES (". $mysql->quote(santise($_POST["name"])) .", '". $_SERVER['REMOTE_ADDR'] ."', ". $mysql->quote($_GET["p"]) .", ". $mysql->quote($_GET["th"]) .", ". $mysql->quote(santise($_POST["post"])) .");");
	} else {
		$mysql->query("INSERT INTO `posts` (`author`, `location`, `sublocation`, `content`) VALUES (". $_SESSION["id"] .", ". $mysql->quote($_GET["p"]) .", ". $mysql->quote($_GET["th"]) .", ". $mysql->quote(santise($_POST["post"])) .");");
	}
	
	header("Location: index.php?p=". santise($_GET["p"]) ."&page=". santise($_GET["page"]));
	die();
	
} while(0);

if (($page["param1"] == 0 and $sys["anonymousposts"] == 1) or ($page["param1"] <= $_SESSION["level"] and $_SESSION["access_addpost"] > 0)) {
	
	if (!isset($_SESSION["id"])) {
	
		$page["content"] .= $msg .'<form method="post" id="sendPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><input placeholder="Jméno" type="text" name="name" value="'. santise($_POST["name"]) .'" class="form-control separate">
<img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input placeholder="Captcha" type="text" name="captcha" class="form-control separate"></div>
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><textarea placeholder="Text (můžete použít základní BBCode)" name="post" class="form-control separate">'. santise($_POST["post"]) .'</textarea>
<button type="submit" class="btn btn-primary btn-sm" name="ap">Přidat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'&th='. $_GET["th"] .'" class="btn btn-danger btn-sm">Zrušit</a></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"></form><hr>';

	} elseif (!has_access("nocaptcha")) {
		
		$page["content"] .= $msg .'<form method="post" id="sendPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><textarea placeholder="Text (můžete použít základní BBCode)" name="post" class="form-control separate">'. santise($_POST["post"]) .'</textarea>
<button type="submit" class="btn btn-primary btn-sm" name="ap">Přidat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'&th='. $_GET["th"] .'" class="btn btn-danger btn-sm">Zrušit</a></div>
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input placeholder="Captcha" type="text" name="captcha" class="form-control separate"></div></div><input type="hidden" name="page" value="'. $page["id"] .'"></form><hr>';
		
	} else {
		
		$page["content"] .= $msg .'<form method="post" id="sendPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><textarea placeholder="Text (můžete použít základní BBCode)" name="post" class="form-control separate">'. santise($_POST["post"]) .'</textarea>
<button type="submit" name="ap" class="btn btn-primary btn-sm">Přidat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'&th='. $_GET["th"] .'" class="btn btn-danger btn-sm">Zrušit</a></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"><input type="hidden" name="csrf" value="'. $csrf_token .'"></form><hr>';
		
	}
}