<?php

if (($page["param1"] == 0 and $sys["anonymousposts"] == 1) or ($page["param1"] <= $_SESSION["level"] and $_SESSION["access_addpost"] > 0)) {
	
	if (!isset($_SESSION["id"])) {
	
		$page["content"] .= '<form method="post" id="sendPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><input placeholder="Jméno" type="text" name="name" class="form-control separate">
<img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input placeholder="Captcha" type="text" name="captcha" class="form-control separate"></div>
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><textarea placeholder="Text" name="post" class="form-control separate"></textarea>
<button type="submit" class="btn btn-primary btn-sm" name="ap">Přidat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"></form><hr>';

	} elseif (!has_access("nocaptcha")) {
		
		$page["content"] .= '<form method="post" id="sendPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><textarea placeholder="Text" name="post" class="form-control separate"></textarea>
<button type="submit" class="btn btn-primary btn-sm" name="ap">Přidat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div>
<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"><img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input placeholder="Captcha" type="text" name="captcha" class="form-control separate"></div></div><input type="hidden" name="page" value="'. $page["id"] .'"></form><hr>';
		
	} else {
		
		$page["content"] .= '<form method="post" id="sendPost"><div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><textarea placeholder="Text" name="post" class="form-control separate"></textarea>
<button type="submit" name="ap" class="btn btn-primary btn-sm">Přidat příspěvek</button> <a href="index.php?p='. $_GET["p"] .'&page='. $_GET["page"] .'" class="btn btn-danger btn-sm">Zrušit</a></div></div>
<input type="hidden" name="page" value="'. $page["id"] .'"><input type="hidden" name="csrf" value="'. $csrf_token .'"></form><hr>';
		
	}
}

if (isset($_POST["ap"])) do {
	
	if (!isset($_SESSION["id"])) {
	
		// validate name
	
	}
	
	if (!has_access("nocaptcha") or !isset($_SESSION["id"])) {
	
		// valida captcha
	
	}
	
	// validate body
	
	// inset post
	
}