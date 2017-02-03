<?php

if (!defined("_PW")) {
	die();
}

if (isset($_POST["submit"])) {

	if ($_POST["user"] != null and $_POST["pass"] != null) {
	
		$user = $mysql->query("SELECT * FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `deleted` = 0 AND `loginname`= ". $mysql->quote($_POST["user"]) .";");
	
		if ($user->num_rows < 1) {
		
			$message = '<div class="alert alert-danger"><strong>Chybné jméno nebo heslo!</strong></div>';
	
		} else {

			$user = $user->fetch_assoc();
			require "lib/hash.php";
	
			if (validate_password($_POST["pass"], $user["hash"]) != 1) {
			
				$message = '<div class="alert alert-danger"><strong>Chybné jméno nebo heslo!</strong></div>';
			
			} elseif ($user["blocked"] == 1) {
					
				$message = '<div class="alert alert-danger"><strong>Váš účet je zablokován!</strong></div>';
				
			} else {
				
				unset($user["hash"]);
				unset($user["lastact"]);
				unset($user["blocked"]);
				unset($user["deleted"]);
				
				session_regenerate_id(true);				
				$_SESSION = $user;
				
				header("Location: index.php");
				die();
			
			}

		}

	} else {
		
		$message = '<div class="alert alert-danger"><strong>Chybné jméno nebo heslo!</strong></div>';
		
	}

}

?>