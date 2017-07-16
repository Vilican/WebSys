<?php

do {
	
	if (!has_access("admin_users_pass")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$user = $mysql->query("SELECT `users`.`id`, `users`.`2fa_gauth`, `users`.`2fa_yubi`, `users`.`username`, `roles`.`level` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `users`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($user->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Tento uživatel neexistuje!</strong></div>';
		break;
	}
	
	$user = $user->fetch_assoc();
	
	if (($user["level"] >= $_SESSION["level"] and $_SESSION["id"] != 0) or $user["id"] == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke změně tohoto objektu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa uživatelů - reset hesla';
	
	if (isset($_GET["remove2fa"])) do {
		
		if (!validate_csrf($_GET["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		$mysql->query("UPDATE `websys`.`users` SET `2fa_gauth` = NULL WHERE `id` = ". $mysql->quote($_GET["id"]) .";");
		$mysql->query("UPDATE `websys`.`users` SET `2fa_yubi` = NULL WHERE `id` = ". $mysql->quote($_GET["id"]) .";");
		header("Location: admin.php?p=users-pass&id=". santise($_GET["id"]));
		die();
		
	} while(0);
	
	if (isset($_POST["resetpass"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>Nesouhlasí CSRF token - to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		if (empty($_POST["pass"])) {
			$message = '<div class="alert alert-danger"><strong>Heslo nesmí být prázdné!</strong></div>';
			break;
		}
		
		if ($_POST["pass"] != $_POST["pass2"]) {
			$message = '<div class="alert alert-danger"><strong>Hesla se neshodují!</strong></div>';
			break;
		}
		
		if (strlen($_POST["pass"]) < 8) {
			$message = '<div class="alert alert-danger"><strong>Heslo musí mít alespoň 8 znaků!</strong></div>';
			break;
		}
		
		require "lib/hash.php";
		
		$mysql->query("UPDATE `websys`.`users` SET `hash` = ". $mysql->quote(create_hash($_POST["pass"])) ." WHERE `id` = ". $mysql->quote($_GET["id"]) .";");
		$message = '<div class="alert alert-success"><strong>Heslo bylo změněno.</strong> Nezapomeňte ho sdělit uživateli!</div>';
		
	} while(0);
	
	$csrf = generate_csrf();
	
	if (!$sys["twofactor_gauth"] and !$sys["twofactor_yubi"]) {
		$user_2fa = '<span class="label label-info">není povoleno v nastavení systému</span>';
	} elseif (!empty($user["2fa_gauth"]) or !empty($user["2fa_yubi"])) {
		$user_2fa = '<span class="label label-success">aktivní</span> <a href="admin.php?p=users-pass&id='. santise($_GET["id"]) .'&remove2fa&csrf='. $csrf .'" class="btn btn-danger btn-xs">Odstranit</a>';
	} else {
		echo $user["2fa_gauth"];
		$user_2fa = '<span class="label label-danger">neaktivní</span>';
	}
	
	$page["content"] .= $message .'<form method="post"><table style="border-spacing:10px">
	<tr><td>Uživatelské jméno:</td><td><input type="text" value="'. $user["username"] .'" class="form-control" disabled="disabled"></td></tr>
	<tr><td>Dvojfaktorové přihlášení:</td><td>'. $user_2fa .'</td></tr>
	<tr><td>Nové heslo:</td><td><input type="password" name="pass" class="form-control"></td></tr>
	<tr><td>Heslo znovu:</td><td><input type="password" name="pass2" class="form-control"></td></tr>
	<tr><td>&nbsp;</td><td><input type="submit" name="resetpass" class="btn btn-warning" value="Resetovat heslo"></td></tr>
	</table><input type="hidden" name="csrf" value="'. $csrf .'"></form>';
	
} while (0);