<?php

if (!defined("_PW")) {
	die();
}

$userfields = $mysql->query("SELECT * FROM `userfields` WHERE `regattr` = 1 ORDER BY `ord` ASC;");
if ($userfields->num_rows > 0) {
	
	while ($field = $userfields->fetch_assoc()) {
		
		if ($field["type"] == "text") {
			$reg_fields .= '<tr><td>'. $field["label"] .':</td><td><textarea name="'. $field["name"] .'" class="form-control" value="'. $_POST[$field["name"]] .'"></textarea></td></tr>';
			continue;
		}
		$reg_fields .= '<tr><td>'. $field["label"] .':</td><td><input type="text" name="'. $field["name"] .'" class="form-control" value="'. $_POST[$field["name"]] .'"></td></tr>';
		
	}
}

if ($sys["regcaptcha"] == "1") {
	
	$reg_fields .= '<tr><td>Captcha</td><td><img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input type="text" name="captcha" class="form-control"></td></tr>';
	
}

if (isset($_POST["submit"])) {
	
	if (strtolower($_SESSION['captcha']) != strtolower($_POST["captcha"]) and $sys["regcaptcha"]) {
		$message .= "Captcha kód nesouhlasí!<br>";
		$err = true;
	}
	
	if (!validate_length($_POST["loginname"], 3, 24)) {
		$message .= "Login musí mít minimálně 3 znaky a maximálně 24 znaků!<br>";
		$err = true;
	}
	
	if (!check_type($_POST["loginname"], "username")) {
		$message .= "Login může obsahovat jen písmena, čísla, pomlčku a podtržítko!<br>";
		$err = true;
	}

	if (!validate_length($_POST["username"], 3, 32)) {
		$message .= "Jméno musí mít minimálně 3 znaky a maximálně 32 znaků!<br>";
		$err = true;
	}
	
	if (!check_type($_POST["username"], "username")) {
		$message .= "Jméno může obsahovat jen písmena, čísla, pomlčku a podtržítko!<br>";
		$err = true;
	}
	
	if (strlen($_POST["pass"]) < 8) {
		$message .= "Heslo musí mít minimálně 8 znaků!<br>";
		$err = true;
	}
	
	if ($_POST["pass"] != $_POST["pass2"]) {
		$message .= "Hesla se neshodují!<br>";
		$err = true;
	}
	
	if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		$message .= "Email nemá platný tvar!<br>";
		$err = true;
	}
	
	$userfields = $mysql->query("SELECT * FROM `userfields` WHERE `regattr` = 1 ORDER BY `ord` ASC;");
	if ($userfields->num_rows > 0) {
		while ($field = $userfields->fetch_assoc()) {
			if (!validate_length($_POST[$field["name"]], $field["minlength"], $field["maxlength"])) {
				$message .= $field["label"] ." - minimální délka ". $field["minlength"] .", maximální ". $field["maxlength"] ."!<br>";
				$err = true;
			}
			if (!check_type($_POST[$field["name"]], $field["type"])) {
				$message .= $field["label"] ." - neplatný tvar!<br>";
				$err = true;
			}
			$reg_values .= "`". $field["name"] ."` = ". $purifier->purify($mysql->quote($_POST[$field["name"]])) .", ";
		}
	}
	
	if ($mysql->query("SELECT `id` FROM `users` WHERE `loginname` = ". $mysql->quote($_POST["loginname"]) .";")->num_rows > 0) {
		$message .= "Login je již registrován!<br>";
		$err = true;
	}
	
	if ($mysql->query("SELECT `id` FROM `users` WHERE `username` = ". $mysql->quote($_POST["username"]) .";")->num_rows > 0) {
		$message .= "Jméno je již registrován!<br>";
		$err = true;
	}
	
	if ($mysql->query("SELECT `id` FROM `users` WHERE `email` = ". $mysql->quote($_POST["email"]) .";")->num_rows > 0) {
		$message .= "Email je již registrován!<br>";
		$err = true;
	}
	
	if ($err) {
		$message = '<div class="alert alert-danger"><p><strong>Při registraci došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
	} else {
		
		require "lib/hash.php";
		$mysql->query("INSERT INTO `users` (`loginname`, `username`, `email`, `hash`, `role`, `lastact`) VALUES (". $mysql->quote($_POST["loginname"]) .", ". $mysql->quote($_POST["username"]) .", ". $mysql->quote($_POST["email"]) .", ". $mysql->quote(create_hash($_POST["pass"])) .", ". $mysql->quote($sys["reggroup"]) .", NOW());");
		$mysql->query("UPDATE `users` SET ". $reg_values ." `id` = ". $mysql->quote($mysql->insert_id()) ." WHERE `id` = ". $mysql->quote($mysql->insert_id()) .";");
		$message = '<div class="alert alert-success"><strong>Registrace proběhla úspěšně.</strong></div>';
		
	}
	
}