<?php

do {
	
	if (!has_access("admin_users_view") and !has_access("admin_users_edit")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$user = $mysql->query("SELECT `users`.*, `roles`.`rolename`, `roles`.`level` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `users`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($user->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Tento uživatel neexistuje!</strong></div>';
		break;
	}
	
	$user = $user->fetch_assoc();
	
	if (!has_access("admin_users_edit") or ($user["level"] >= $_SESSION["level"] and $_SESSION["id"] != 0)) {
		$page["content"] = '<div class="alert alert-warning"><strong>Pouze pro čtení - nemáte oprávnění provádět zde změny.</strong></div>';
	} else {
		$edit = true;
	}
	
	$page["title"] = 'Správa uživatelů - úprava';
	
	if (isset($_GET["deleteavatar"]) and $edit) {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message .= "Nesouhlasí CSRF token - to může znamenat pokus o útok!<br>";
			$err = true;
		}
		
		unlink("upload/avatars/". santise($_GET["id"]));
		$message = '<div class="alert alert-success"><strong>Avatar byl smazán</strong></div>';
		
	}
	
	if (isset($_POST["submit"]) and $edit) {
		
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
		
		if ($mysql->query("SELECT `id` FROM `users` WHERE `loginname` = ". $mysql->quote($_POST["loginname"]) .";")->num_rows > 0 and $_POST["loginname"] != $user["loginname"]) {
			$message .= "Login je již registrován!<br>";
			$err = true;
		}
		
		if ($mysql->query("SELECT `id` FROM `users` WHERE `username` = ". $mysql->quote($_POST["username"]) .";")->num_rows > 0 and $_POST["username"] != $user["username"]) {
			$message .= "Jméno je již registrováno!<br>";
			$err = true;
		}
		
		if ($mysql->query("SELECT `id` FROM `users` WHERE `email` = ". $mysql->quote($_POST["email"]) .";")->num_rows > 0 and $_POST["email"] != $user["email"]) {
			$message .= "Email je již registrován!<br>";
			$err = true;
		}
		
		if ($user["id"] == 0) {
			$_POST["blocked"] = null;
		}
		
		if ($err) {
			$message = '<div class="alert alert-danger"><p><strong>Při úpravě došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
		} else {
			
			$mysql->query("UPDATE `users` SET ". $reg_values ." `email` = ". $mysql->quote($_POST["email"]) .", `username` = ". $mysql->quote($_POST["username"]) .", `loginname` = ". $mysql->quote($_POST["loginname"]) .", `emailvalid` = ". $mysql->quote(parse_from_checkbox($_POST["emailvalid"])) .", `blocked` = ". $mysql->quote(parse_from_checkbox($_POST["blocked"])) ." WHERE `id` = ". $mysql->quote($_GET["id"]) .";");
			$message = '<div class="alert alert-success"><strong>Uživatelský účet byl upraven</strong></div>';
			
		}
		
	}
	
	$user = $mysql->query("SELECT `users`.*, `roles`.`rolename`, `roles`.`level` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `users`.`id` = ". $mysql->quote($_GET["id"]) .";")->fetch_assoc();
	
	$userfields = $mysql->query("SELECT * FROM `userfields` ORDER BY `ord` ASC;");
	if ($userfields->num_rows > 0) {
		while ($field = $userfields->fetch_assoc()) {
			if ($field["type"] == "text") {
				if ($edit) {
					$next_fields .= '<tr><td>'. $field["label"] .':</td><td><textarea name="'. $field["name"] .'" class="form-control">'. restore_value($user[$field["name"]], santise($_POST[$field["name"]])) .'</textarea></td></tr>';
					continue;
				}
				$next_fields .= '<tr><td>'. $field["label"] .':</td><td>'. $user[$field["name"]] .'</td></tr>';
				continue;
			}
			if ($edit) {
				$next_fields .= '<tr><td>'. $field["label"] .':</td><td><input type="text" name="'. $field["name"] .'" class="form-control" value="'. restore_value($user[$field["name"]], santise($_POST[$field["name"]])) .'"></td></tr>';
				continue;
			}
			$next_fields .= '<tr><td>'. $field["label"] .':</td><td>'. $user[$field["name"]] .'</td></tr>';
		}
	}
	
	if ($edit) {
		$csrf = generate_csrf();
		$page["content"] .= $message .'<div class="col-md-4 col-lg-2"><img src="'. displayAvatar($user["id"]) .'" class="avatar img-responsive" alt="Profilový obrázek">
		<a href="admin.php?p=users-edit&id='. santise($_GET["id"]) .'&deleteavatar&csrf='. $csrf .'" class="btn btn-danger btn-sm">Vymazat avatara</a></div>
		<div class="col-md-8 col-lg-10"><form method="post"><table style="border-spacing:10px">
		<tr><td>Přihlašovací jméno:</td><td><input type="text" name="loginname" value="'. restore_value(santise($user["loginname"]), santise($_POST["loginname"])) .'" class="form-control"></td></tr>
		<tr><td>Uživatelské jméno:</td><td><input type="text" name="username" value="'. restore_value(santise($user["username"]), santise($_POST["username"])) .'" class="form-control"></td></tr>
		<tr><td>Email:</td><td><input type="text" name="email" value="'. restore_value(santise($user["email"]), santise($_POST["email"])) .'" class="form-control"></td></tr>
		<tr><td>Ověřený email:</td><td><input type="checkbox" name="emailvalid" '. parse_to_checkbox($user["emailvalid"]) .' class="form-control" style="width:8%"></td></tr>
		<tr><td>Blokován:</td><td><input type="checkbox" name="blocked" '. parse_to_checkbox($user["blocked"]) .' class="form-control" style="width:8%"></td></tr>
		<tr><td>Role:</td><td>'. $user["rolename"] .'</a></td></tr>
		<tr><td>Poslední aktivita:</td><td>'. $user["lastact"] .'</a></td></tr>
		'. $email_val . $next_fields .'
		<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Uložit změny" class="btn btn-default"></td></tr>
		</table><input type="hidden" name="csrf" value="'. $csrf .'"></form></div>';
	} else {
		
		if ($user["emailvalid"]) {
			$emailval = ' (ověřený)';
		} else {
			$emailval = ' (NEOVĚŘENÝ)';
		}
		
		if ($user["blocked"]) {
			$blocked = ' <span class="text text-danger">Tento uživatel byl zablokován</span>';
		}
		
		$page["content"] .= '<div class="col-md-4 col-lg-2"><img src="'. displayAvatar($user["id"]) .'" class="avatar img-responsive" alt="Profilový obrázek"></div>
		<div class="col-md-8 col-lg-10">'. $blocked .'<table style="border-spacing:10px">
		<tr><td>Přihlašovací jméno:</td><td>'. $user["loginname"] .'</td></tr>
		<tr><td>Uživatelské jméno:</td><td>'. $user["username"] .'</td></tr>
		<tr><td>Role:</td><td>'. $user["rolename"] .'</a></td></tr>
		<tr><td>Poslední aktivita:</td><td>'. $user["lastact"] .'</a></td></tr>
		<tr><td>Email:</td><td><a href="mailto:'. $user["email"] .'">'. $user["email"] .'</a> '. $emailval .'</td></tr>
		'. $next_fields .'</table></div>';
	}
	
} while (0);