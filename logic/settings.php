<?php

if (!isset($_SESSION["id"])) {
	$message = '<div class="alert alert-danger"><strong>Tato stránka je jen pro registrované!</strong></div>';
	require "template/page.php";
	die();
}

if (isset($_GET["newavatar"]) and isset($_POST["setavatar"])) do {
	
	if ($_FILES["avatarFile"]["size"] > 1048576) {
		$message = '<div class="alert alert-danger"><strong>Maximální velikost obrázku je 1 MB!</strong></div>';
		break;
	}
	
	$extension = pathinfo($_FILES["avatarFile"]["name"], PATHINFO_EXTENSION);
	if ($extension != "jpg" && $extension != "png" && $extension != "jpeg" && $extension != "gif") {
		$message = '<div class="alert alert-danger"><strong>Jsou povoleny pouze obrázky JPG, PNG a GIF!</strong></div>';
		break;
	}
	
	if (!move_uploaded_file($_FILES["avatarFile"]["tmp_name"], "upload/avatars/". santise($_SESSION["id"]))) {
		$message = '<div class="alert alert-danger"><strong>Obrázek nebylo možné nahrát!</strong></div>';
		break;
    }
	
	$message = '<div class="alert alert-success"><strong>Avatar byl uložen</strong></div>';
	
} while(0);

if (isset($_GET["newavatar"]) and isset($_GET["removeavatar"])) do {
	
 	if (!validate_csrf($_GET["csrf"])) {
		$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
		break;
	}
  
	unlink("upload/avatars/". santise($_SESSION["id"]));
	$message = '<div class="alert alert-success"><strong>Avatar byl smazán</strong></div>';
  
} while(0);

if (isset($_GET["emailval"]) and isset($_POST["request"])) do {
	
	if (strtolower($_SESSION['captcha']) != strtolower($_POST["captcha"])) {
		$message = '<div class="alert alert-danger"><strong>Captcha kód nesouhlasí!</strong></div>';
		break;
	}
	
	if (!validate_csrf($_POST["csrf"])) {
		$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
		break;
	}
	
	$usr = $mysql->query("SELECT `emailvalid` FROM `users` WHERE `id` = ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();
	
	if ($usr["emailvalid"] == 1) {
		$message = '<div class="alert alert-danger"><strong>Váš email byl již validován!</strong></div>';
		break;
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
	$mail->addAddress($_SESSION["email"]);
	$mail->isHTML(true);
	$mail->CharSet = 'UTF-8';
	$mail->Subject = 'Validace emailu';
	$mail->Body = '<!DOCTYPE html><html lang="cs"><head><meta charset="utf-8"></head><body>Dobrý den,<br>někdo (pravděpodobně Vy) požádal o ověření tohoto emailu na stránkách '. $sys["title"] .'.<br><br>Váš potvrzovací kód je: <b>'. $_SESSION["valcode"] .'</b><br><br>Tento kód nikomu neukazujte ani nepřeposílejte.<br>Pokud jste žádost nevytvořili, můžete email smazat.</body></html>';
	$mail->send();
	
	$message = '<div class="alert alert-success"><strong>Kód byl odeslán</strong></div>';
	
} while(0);

if (isset($_GET["emailval"]) and isset($_POST["validate"])) do {
	
	if (!validate_csrf($_POST["csrf"])) {
		$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
		break;
	}
	
	if (!empty($_SESSION["valcode"]) and $_SESSION["valcode"] == $_POST["valcode"]) {
		unset($_SESSION["valcode"]);
		$message = '<div class="alert alert-success"><strong>Email byl ověřen!</strong></div>';
		$mysql->query("UPDATE `users` SET `emailvalid` = 1 WHERE `id` = ". $mysql->quote($_SESSION["id"]) .";");
		break;
	}
	
	$message = '<div class="alert alert-danger"><strong>Kód je nesprávný nebo vyexpirovaný!</strong></div>';

} while(0);

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
		$email_val = '<tr><td>Ověření emailu:</td><td><a href="index.php?p=settings&emailval" class="btn btn-danger" id="emailval">Ověřit email</a></td></tr>';
	}
	
}

if (isset($_GET["enableg"])) do {
	
	if (!validate_csrf($_GET["csrf"])) {
		$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
		break;
	}
	
	require "lib/twofactor/GoogleAuthenticator.php";
	$google2fa = new PHPGangsta_GoogleAuthenticator();
	$secret = $google2fa->createSecret();
	
	$mysql->query("UPDATE `users` SET `2fa_gauth` = ". $mysql->quote($secret) ." WHERE `id` = ". $mysql->quote($_SESSION["id"]) .";");
	header("Location: index.php?p=settings&2fa");
	die();
	
} while(0);

if (isset($_GET["disableg"])) do {
	
	if (!validate_csrf($_GET["csrf"])) {
		$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
		break;
	}
	
	$mysql->query("UPDATE `users` SET `2fa_gauth` = NULL WHERE `id` = ". $mysql->quote($_SESSION["id"]) .";");
	header("Location: index.php?p=settings&2fa");
	die();
	
} while(0);

if (isset($_POST["enabley"])) do {
	
	if (!validate_csrf($_POST["csrf"])) {
		$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
		break;
	}
	
	$mysql->query("UPDATE `users` SET `2fa_yubi` = ". $mysql->quote(santise($_POST["yubiid"])) ." WHERE `id` = ". $mysql->quote($_SESSION["id"]) .";");
	header("Location: index.php?p=settings&2fa");
	die();
	
} while(0);

if (isset($_GET["disabley"])) do {
	
	if (!validate_csrf($_GET["csrf"])) {
		$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
		break;
	}
	
	$mysql->query("UPDATE `users` SET `2fa_yubi` = NULL WHERE `id` = ". $mysql->quote($_SESSION["id"]) .";");
	header("Location: index.php?p=settings&2fa");
	die();
	
} while(0);

$csrf = generate_csrf();

if (isset($_GET["newavatar"])) {
	
	if (!file_exists("upload/avatars/". santise($_SESSION["id"]))) {
		$avatar_actions = '<tr><td>Nahrát:</td><td><input type="file" name="avatarFile" id="avatarFile" class="form-control"></td></tr>
		<tr><td>&nbsp;</td><td><input type="submit" name="setavatar" value="Nahrát" class="btn btn-default"></td></tr>';
	} else {
		$avatar_actions = '<tr><td>&nbsp;</td><td><a href="index.php?p=settings&newavatar&removeavatar&csrf='. $csrf .'" class="btn btn-danger">Smazat avatara</a></td></tr>';
	}
}

if (isset($_GET["2fa"]) and ($sys["twofactor_yubi"] or $sys["twofactor_gauth"])) {
	if ($sys["twofactor_gauth"]) {
		$gauth_token = $mysql->query("SELECT `2fa_gauth` FROM `users` WHERE `id` = ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();
		$gauth_token = $gauth_token["2fa_gauth"];
		if (empty($gauth_token)) {
			$gauth = '<tr><td>Google Authenticator - akce:</td><td><a href="index.php?p=settings&2fa&enableg&csrf='. $csrf .'" class="btn btn-info">Povolit</a></td></tr>';
		} else {
			$gauth = '<tr><td>Google Authenticator tajný klíč:</td><td><input type="text" value="'. $usr["2fa_gauth"] .'" class="form-control" disabled="disabled"></td></tr>
			<tr><td>Google Authenticator - akce:</td><td><a href="index.php?p=settings&2fa&disableg&csrf='. $csrf .'" class="btn btn-danger">Zakázat</a></td></tr>';
		}
	}
	if ($sys["twofactor_yubi"]) {
		$yubi = $mysql->query("SELECT `2fa_yubi` FROM `users` WHERE `id` = ". $mysql->quote($_SESSION["id"]) ." LIMIT 1;")->fetch_assoc();
		$yubi = $yubi["2fa_yubi"];
		if (empty($yubi)) {
			$yubikey = '<form method="post"><tr><td>YubiKey ID klíče:</td><td><input type="text" name="yubiid" class="form-control"></td></tr>
			<tr><td>YubiKey - akce:</td><td><button type="submit" name="enabley" class="btn btn-info">Povolit</a><input type="hidden" value="'. $csrf .'" name="csrf"></td></tr></form>';
		} else {
			$yubikey = '<tr><td>YubiKey ID klíče:</td><td><input type="text" value="'. $usr["2fa_yubi"] .'" class="form-control" disabled="disabled"></td></tr>
			<tr><td>YubiKey - akce:</td><td><a href="index.php?p=settings&2fa&disabley&csrf='. $csrf .'" class="btn btn-danger">Zakázat</a></td></tr>';
		}
	}
}