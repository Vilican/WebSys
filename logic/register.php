<?php

if (!defined("_PW")) {
	die();
}

if (isset($_POST["submit"])) {

	if ($_POST["user"] == null or $_POST["pass"] == null or $_POST["pass2"] == null or $_POST["email"] == null) {
		
		$message = '<div class="alert alert-danger"><strong>Všechna pole musí být vyplněna!</strong></div>';
		
	} elseif (strlen($_POST["user"]) < 3 or strlen($_POST["user"]) > 24) {
		
		$message = '<div class="alert alert-danger"><strong>Uživatelské jméno musí mít 3 až 24 znaků!</strong></div>';
		
	} elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
		
		$message = '<div class="alert alert-danger"><strong>Email nemá platný tvar!</strong></div>';
		
	} elseif (strlen($_POST["pass"]) < 8) {
		
		$message = '<div class="alert alert-danger"><strong>Heslo musí mít alespoň 8 znaků!</strong></div>';
		
	} elseif ($_POST["pass"] !== $_POST["pass2"]) {
		
		$message = '<div class="alert alert-danger"><strong>Zadaná hesla nejsou shodná!</strong></div>';
		
	} else {
		
		if ($mysql->query("SELECT * FROM `users` WHERE `loginname` = ". $mysql->quote($_POST["user"]) .";")->num_rows > 0) {
			
			$message = '<div class="alert alert-danger"><strong>Uživatelské jméno se již používá!</strong></div>';
			
		} elseif ($mysql->query("SELECT * FROM `users` WHERE `email` = ". $mysql->quote($_POST["email"]) .";")->num_rows > 0) {
		
			$message = '<div class="alert alert-danger"><strong>Email se již používá!</strong></div>';
		
		} else {
		
			require "lib/hash.php";
			$mysql->query("INSERT INTO `users` (`loginname`, `username`, `realname`, `email`, `hash`, `role`, `blocked`, `lastact`) VALUES (". $mysql->quote($_POST["user"]) .", ". $mysql->quote($_POST["user"]) .", NULL, ". $mysql->quote($_POST["email"]) .", '". create_hash($_POST["pass"]) ."', '". $sys["reggroup"] ."', '0', NOW());");
		
			if ($mysql->error() != null) {
				
				$message = '<div class="alert alert-danger"><strong>Při zápisu do databáze nastal problém. Prosím oznamte to správci stránek.</strong></div>';
				
			} else {
		
				$message = '<div class="alert alert-success"><strong>Registrace proběhla úspěšně. <a href="index.php?p=login" class="alert-link">Přihlásit se >></a></strong></div>';
			
			}
			
		}
		
	}
	
}

?>