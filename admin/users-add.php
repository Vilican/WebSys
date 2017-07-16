<?php

do {
	
	if (!has_access("admin_users_edit")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
		
	$page["title"] = 'Správa uživatelů - přidání';
	
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
		
		$userfields = $mysql->query("SELECT * FROM `userfields` ORDER BY `ord` ASC;");
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
		
		if ($mysql->query("SELECT `id` FROM `users` WHERE `loginname` = ". $mysql->quote($_POST["loginname"]) .";")->num_rows > 0) {
			$message .= "Login je již registrován!<br>";
			$err = true;
		}
		
		if ($mysql->query("SELECT `id` FROM `users` WHERE `username` = ". $mysql->quote($_POST["username"]) .";")->num_rows > 0) {
			$message .= "Jméno je již registrováno!<br>";
			$err = true;
		}
		
		if ($mysql->query("SELECT `id` FROM `users` WHERE `email` = ". $mysql->quote($_POST["email"]) .";")->num_rows > 0) {
			$message .= "Email je již registrován!<br>";
			$err = true;
		}
		
		if ($err) {
			$message = '<div class="alert alert-danger"><p><strong>Při úpravě došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
		} else {
			
			require "lib/hash.php";
			$mysql->query("INSERT INTO `users` (`loginname`, `username`, `email`, `hash`, `role`, `lastact`) VALUES (". $mysql->quote($_POST["loginname"]) .", ". $mysql->quote($_POST["username"]) .", ". $mysql->quote($_POST["email"]) .", ". $mysql->quote(create_hash($_POST["pass"])) .", ". $mysql->quote($sys["reggroup"]) .", NOW());");
			$userid = $mysql->insert_id();
			$mysql->query("UPDATE `users` SET ". $reg_values ." `blocked` = ". $mysql->quote(parse_from_checkbox($_POST["blocked"])) .", `emailvalid` = ". $mysql->quote(parse_from_checkbox($_POST["emailvalid"])) ." WHERE `id` = ". $mysql->quote($userid) .";");
			header("Location: admin.php?p=users-edit&id=". $userid);
			die();
			
		}
		
	}
		
	$userfields = $mysql->query("SELECT * FROM `userfields` ORDER BY `ord` ASC;");
	if ($userfields->num_rows > 0) {
		while ($field = $userfields->fetch_assoc()) {
			if ($field["type"] == "text") {
				$next_fields .= '<tr><td>'. $field["label"] .':</td><td><textarea name="'. $field["name"] .'" class="form-control">'. santise($_POST[$field["name"]]) .'</textarea></td></tr>';
				continue;
			}
			$next_fields .= '<tr><td>'. $field["label"] .':</td><td><input type="text" name="'. $field["name"] .'" class="form-control" value="'. santise($_POST[$field["name"]]) .'"></td></tr>';
		}
	}
	
	$page["content"] .= $message .'<div class="col-md-4 col-lg-2"><img src="upload/avatars/generic.png" class="avatar img-responsive" alt="Profilový obrázek"></div>
	<div class="col-md-8 col-lg-10"><form method="post"><table style="border-spacing:10px">
	<tr><td>Přihlašovací jméno:</td><td><input type="text" name="loginname" value="'. santise($_POST["loginname"]) .'" class="form-control"></td></tr>
	<tr><td>Uživatelské jméno:</td><td><input type="text" name="username" value="'. santise($_POST["username"]) .'" class="form-control"></td></tr>
	<tr><td>Email:</td><td><input type="text" name="email" value="'. santise($_POST["email"]) .'" class="form-control"></td></tr>
	<tr><td>Ověřený email:</td><td><input type="checkbox" name="emailvalid" class="form-control" style="width:8%"></td></tr>
	<tr><td>Heslo:</td><td><input type="password" name="pass" class="form-control"></td></tr>
	<tr><td>Blokován:</td><td><input type="checkbox" name="blocked" class="form-control" style="width:8%"></td></tr>
	'. $email_val . $next_fields .'
	<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Uložit změny" class="btn btn-default"></td></tr>
	</table><input type="hidden" name="csrf" value="'. generate_csrf() .'"></form></div>';
	
} while (0);