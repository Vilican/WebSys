<?php

if (!defined("_PW")) {
	die();
}

if (isset($_POST["submit"])) do {
	
	if (empty($_POST["user"]) or empty($_POST["pass"])) {
		$message = '<div class="alert alert-danger"><strong>Chybné jméno nebo heslo!</strong></div>';
		break;
	}
	
	$user = $mysql->query("SELECT * FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `deleted` = 0 AND `loginname`= ". $mysql->quote($_POST["user"]) .";");
	
	if ($user->num_rows < 1) {
		$message = '<div class="alert alert-danger"><strong>Chybné jméno nebo heslo!</strong></div>';
		break;
	}
	
	$user = $user->fetch_assoc();
	require "lib/hash.php";
	
	if (validate_password($_POST["pass"], $user["hash"]) != 1) {
		$message = '<div class="alert alert-danger"><strong>Chybné jméno nebo heslo!</strong></div>';
		break;
	}
	
	if ($user["blocked"] == 1) {
		$message = '<div class="alert alert-danger"><strong>Váš účet je zablokován!</strong></div>';
		break;
	}
	
	unset($user["hash"]);
	unset($user["lastact"]);
	unset($user["blocked"]);
	unset($user["deleted"]);
	
	session_regenerate_id(true);				
	$_SESSION = $user;
	
	header("Location: index.php");
	die();
	
} while(0);