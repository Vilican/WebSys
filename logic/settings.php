<?php

$usr = $mysql->query("SELECT * FROM `users` WHERE `id` = ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();

if (isset($_POST["submit"])) {
	
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
		echo $_POST["username"];
		$message .= "Jméno může obsahovat jen písmena, čísla, pomlčku a podtržítko!<br>";
		$err = true;
	}
	
	if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		$message .= "Email nemá platný tvar!<br>";
		$err = true;
	}
	
	$userfields = $mysql->query("SELECT * FROM `userfields` WHERE `internalonly` = 0 AND `usereditable` = 1 ORDER BY `ord` ASC;");
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
			$reg_values .= "`". $field["name"] ."` = ". $mysql->quote(santise($_POST[$field["name"]])) .", ";
		}
	}
	
	if ($mysql->query("SELECT `id` FROM `users` WHERE `loginname` = ". $mysql->quote($_POST["loginname"]) .";")->num_rows > 0 and $_POST["loginname"] != $usr["loginname"]) {
		$message .= "Login je již registrován!<br>";
		$err = true;
	}
	
	if ($mysql->query("SELECT `id` FROM `users` WHERE `username` = ". $mysql->quote($_POST["username"]) .";")->num_rows > 0 and $_POST["username"] != $usr["username"]) {
		$message .= "Jméno je již registrováno!<br>";
		$err = true;
	}
	
	if ($mysql->query("SELECT `id` FROM `users` WHERE `email` = ". $mysql->quote($_POST["email"]) .";")->num_rows > 0 and $_POST["email"] != $usr["email"]) {
		$message .= "Email je již registrován!<br>";
		$err = true;
	}
	
	if ($err) {
		$message = '<div class="alert alert-danger"><p><strong>Při úpravě došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
	} else {
		
		if ($_POST["email"] != $usr["email"]) {
			$invalidate_email = ', `emailvalid` = 0 ';
		}
		
		$mysql->query("UPDATE `users` SET ". $reg_values ." `email` = ". $mysql->quote($_POST["email"]) .", `username` = ". $mysql->quote($_POST["username"]) .", `loginname` = ". $mysql->quote($_POST["loginname"]) . $invalidate_email ." WHERE `id` = ". $mysql->quote($_SESSION["id"]) .";");
		$message = '<div class="alert alert-success"><strong>Uživatelský účet byl upraven.</strong></div>';
		
	}
	
}

$usr = $mysql->query("SELECT * FROM `users` WHERE `id` = ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();
$userfields = $mysql->query("SELECT * FROM `userfields` WHERE `internalonly` = 0 ORDER BY `ord` ASC;");

if ($userfields->num_rows > 0) {
	while ($field = $userfields->fetch_assoc()) {
		$disable = null;
		if ($field["usereditable"] == 0) {
			$disable = ' disabled="disabled"';
		}
		if ($field["type"] == "text") {
			$next_fields .= '<tr><td>'. $field["label"] .':</td><td><textarea name="'. $field["name"] .'" class="form-control"'. $disable .'>'. santise($usr[$field["name"]]) .'</textarea></td></tr>';
			continue;
		}
		$next_fields .= '<tr><td>'. $field["label"] .':</td><td><input type="text" name="'. $field["name"] .'" class="form-control" value="'. santise($usr[$field["name"]]) .'"'. $disable .'></td></tr>';
	}
}

if (file_exists("upload/avatars/". $usr["id"] ."png")) {
	$path = $usr["id"] ."png";
} else {
	$path = "generic.png";
}

if ($usr["emailvalid"] == 0) {
	$email_val = '<tr><td>Ověření emailu:</td><td><a href="index.php?p=settings&emailval" class="btn btn-danger">Ověřit email</a></td></tr>';
}