<?php

if (!defined("_PW")) {
	die();
}

session_start();

if (isset($_POST["submit"])) do {
	
	if (strtolower($_SESSION['captcha']) != strtolower($_POST["captcha"])) {
		$message .= "Captcha kód nesouhlasí!<br>";
		$err = true;
	}
	
	if (empty($_POST["email"]) or empty($_POST["user"])) {
		$message .= "Všechna pole jsou povinná!<br>";
		$err = true;
	}
	
	if ($err) {
		$message = '<div class="alert alert-danger"><p><strong>Při registraci došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
		break;
	}
	
	session_regenerate_id(true);
	$_SESSION["user"] = $_POST["user"];
	
	$user = $mysql->query("SELECT * FROM `users` WHERE `loginname` = ". $mysql->quote($_POST["user"]) ." AND `email` = ". $mysql->quote($_POST["email"]) ." AND `blocked` = 0;");
	if ($user->num_rows > 0) {
		$user = $user->fetch_assoc();
		
		$_SESSION["code"] = bin2hex(openssl_random_pseudo_bytes(10));

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
		$mail->Body = '<!DOCTYPE html><html lang="cs"><head><meta charset="utf-8"></head><body>Dobrý den,<br>někdo (pravděpodobně Vy) požádal o reset hesla na stránkách '. $sys["title"] .'.<br><br>Váš potvrzovací kód je: <b>'. $_SESSION["code"] .'</b><br><br>Tento kód nikomu neukazujte ani nepřeposílejte.<br>Pokud jste žádost nevytvořili, můžete email smazat.</body></html>';
		$mail->send();
	}
	
	$initiated = true;
	
} while(0);

if (isset($_POST["submit2"])) {
	
	session_regenerate_id();
	
	if ($_SESSION["code"] == $_POST["code"] and !empty($_SESSION["code"]) and !empty($_POST["code"])) {
		$_SESSION["authorized"] = true;
	}
	
}

if (isset($_POST["submit3"]) and $_SESSION["authorized"]) do {
	
	if (strlen($_POST["pass"]) < 8) {
		$message .= "Heslo musí mít minimálně 8 znaků!<br>";
		$err = true;
	}
	
	if ($_POST["pass"] != $_POST["pass2"]) {
		$message .= "Hesla se neshodují!<br>";
		$err = true;
	}
	
	if ($err) {
		$message = '<div class="alert alert-danger"><p><strong>Při registraci došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
		break;
	}
	
	require "lib/hash.php";
	$mysql->query("UPDATE `users` SET `hash` = ". $mysql->quote(create_hash($_POST["pass"])) ." WHERE `loginname` = ". $mysql->quote($_SESSION["user"]) .";");
	$mysql->query("UPDATE `users` SET `2fa_gauth` = NULL, `2fa_yubi` = NULL WHERE `loginname` = ". $mysql->quote($_SESSION["user"]) .";");
	session_unset();
	session_destroy();
	header("Location: index.php?p=login");
	die();
	
} while(0);