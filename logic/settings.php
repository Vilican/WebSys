<?php

if (!isset($_SESSION["id"])) {
	$message = '<div class="alert alert-danger"><strong>Tato stránka je jen pro registrované!</strong></div>';
	require "template/page.php";
	die();
}

if (isset($_GET["emailval"]) and isset($_POST["request"])) {
	
	$usr = $mysql->query("SELECT `emailvalid` FROM `users` WHERE `id` = ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();
	if ($usr["emailvalid"] == 1) {
		die();
	}
	
	$_SESSION["valcode"] = bin2hex(openssl_random_pseudo_bytes(10));

	require 'lib/phpmailer/PHPMailerAutoload.php';
	$mail = new PHPMailer;
	$mail->isSMTP();
	$mail->Host = _SMTPSRV;
	$mail->SMTPAuth = true;
	$mail->Username = _SMTPUSR;
	$mail->Password = _SMTPPASS;
	$mail->SMTPSecure = _SMTPPROTO;
	$mail->Port = _SMTPPORT;
	$mail->setFrom(_SMTPFROMADDR, _SMTPFROMNAME);
	if (_SMTPCERTINVALID) {
		$mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));
	}
	$mail->addAddress($_POST["email"]);
	$mail->isHTML(true);
	$mail->CharSet = 'UTF-8';
	$mail->Subject = 'Reset hesla';
	$mail->Body = '<!DOCTYPE html><html lang="cs"><head><meta charset="utf-8"></head><body>Dobrý den,<br>někdo (pravděpodobně Vy) požádal o ověření tohoto emailu na stránkách '. $sys["title"] .'.<br><br>Váš potvrzovací kód je: <b>'. $_SESSION["valcode"] .'</b><br><br>Tento kód nikomu neukazujte ani nepřeposílejte.<br>Pokud jste žádost nevytvořili, můžete email smazat.</body></html>';
	$mail->send();
	
}

if (isset($_GET["emailval"]) and isset($_POST["validate"])) {
	
	if (!empty($_SESSION["valcode"]) and $_SESSION["valcode"] == $_POST["valcode"]) {
		unset($_SESSION["valcode"]);
		$message = '<div class="alert alert-success"><strong>Email byl ověřen!</strong></div>';
		
	}
}

if (isset($_GET["chpass"])) {
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = "Nesouhlasí CSRF token - to může znamenat pokus o útok!<br>";
			break;
		}
		
		if (strlen($_POST["newpass"]) < 8) {
			$message = '<div class="alert alert-danger"><strong>Minimální délka hesla je 8 znaků!</strong></div>';
			break;
		}
		
		if ($_POST["newpass"] != $_POST["newpass2"]) {
			$message = '<div class="alert alert-danger"><strong>Nová hesla se neshodují!</strong></div>';
			break;
		}
		
		require "lib/hash.php";
		$usr = $mysql->query("SELECT `users`.`hash` FROM `users` WHERE `id` = ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();
		
		if (!validate_password($_POST["oldpass"], $usr["hash"])) {
			$message = '<div class="alert alert-danger"><strong>Staré heslo je špatné!</strong></div>';
			break;
		}
		
		$mysql->query("UPDATE `users` SET `hash` = ". $mysql->quote(create_hash($_POST["newpass"])) ." WHERE `id` = ". $mysql->quote($_SESSION["id"]) .";");
		$message = '<div class="alert alert-success"><strong>Heslo bylo změněno</strong></div>';
		
	} while(0);
	
} else {

	$usr = $mysql->query("SELECT * FROM `users` WHERE `id` = ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();

	if (isset($_POST["submit"])) {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message .= "Nesouhlasí CSRF token - to může znamenat pokus o útok!<br>";
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
			$message = '<div class="alert alert-success"><strong>Uživatelský účet byl upraven</strong></div>';
			
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
				$next_fields .= '<tr><td>'. $field["label"] .':</td><td><textarea name="'. $field["name"] .'" class="form-control"'. $disable .'>'. restore_value(santise($usr[$field["name"]]), santise($_POST[$field["name"]])) .'</textarea></td></tr>';
				continue;
			}
			$next_fields .= '<tr><td>'. $field["label"] .':</td><td><input type="text" name="'. $field["name"] .'" class="form-control" value="'. restore_value(santise($usr[$field["name"]]), santise($_POST[$field["name"]])) .'"'. $disable .'></td></tr>';
		}
	}

	if (file_exists("upload/avatars/". $usr["id"] ."png")) {
		$path = $usr["id"] ."png";
	} else {
		$path = "generic.png";
	}

	if ($usr["emailvalid"] == 0) {
		$email_val = '<tr><td>Ověření emailu:</td><td><button type="button" class="btn btn-danger" id="emailval">Ověřit email</button></td></tr>';
	}
	
}

$csrf = generate_csrf();