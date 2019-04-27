<?php

if (!defined("_PW")) {
	die();
}

if (isset($_POST["submit"])) do {
	
	if (!validate_csrf($_POST["csrf"])) {
		$message = '<div class="alert alert-danger"><strong>Nesouhlasí kontrolní CSRF token!</strong></div>';
		break;
	}
	
	if (empty($_POST["user"]) or empty($_POST["pass"])) {
		$message = '<div class="alert alert-danger"><strong>Přihlášení selhalo!</strong></div>';
		break;
	}
	
	$user = $mysql->query("SELECT * FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `loginname`= ". $mysql->quote($_POST["user"]) .";");
	
	if ($user->num_rows < 1) {
		$message = '<div class="alert alert-danger"><strong>Přihlášení selhalo!</strong></div>';
		break;
	}
	
	$user = $user->fetch_assoc();
	require "lib/hash.php";
	
	if (validate_password($_POST["pass"], $user["hash"]) != 1) {
		$message = '<div class="alert alert-danger"><strong>Přihlášení selhalo!</strong></div>';
		break;
	}
	
	if ((!empty($user["2fa_gauth"]) and $sys["twofactor_gauth"]) or (!empty($user["2fa_yubi"]) and $sys["twofactor_yubi"])) {
		
		if (!empty($user["2fa_yubi"]) and $sys["twofactor_yubi"]) {
			
			require "lib/twofactor/yubico/YubiAuth.php";
			$yubi = new YubiAuth($sys["yubi_id"], $sys["yubi_key"], $sys["yubi_url"]);
			
			if ($yubi->validate($_POST["twofactor"], $user["2fa_yubi"])) {
				$pass_2fa = true;
			}
			
		}
		
		if (!empty($user["2fa_gauth"]) and $sys["twofactor_gauth"]) {
			
			require "lib/twofactor/GoogleAuthenticator.php";
			$ga = new PHPGangsta_GoogleAuthenticator();
			
			if ($ga->verifyCode($user["2fa_gauth"], $_POST["twofactor"], 1)) {
				$pass_2fa = true;
			}
			
		}
		
		if (!$pass_2fa) {
			$message = '<div class="alert alert-danger"><strong>Přihlášení selhalo!</strong></div>';
			break;
		}
		
	}
	
	if ($user["blocked"] == 1) {
		$message = '<div class="alert alert-danger"><strong>Váš účet je zablokován!</strong></div>';
		break;
	}
	
	unset($user["2fa_gauth"]);
	unset($user["2fa_yubi"]);
	unset($user["hash"]);
	unset($user["lastact"]);
	unset($user["blocked"]);
	
	session_regenerate_id(true);				
	$_SESSION = $user;
    $_SESSION["timestamp"] = time();
	
	header("Location: index.php");
	die();
	
} while(0);