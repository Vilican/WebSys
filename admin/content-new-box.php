<?php

do {
	
	if (!has_access("admin_content") or !has_access("admin_content_boxes")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - vytvoření boxu';
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		if (!is_numeric($_POST["ord"]) or strlen($_POST["ord"]) > 4 or $_POST["ord"] < 0) {
			$message .= 'Pořadí není kladné číslo nebo je moc dlouhé (max. 4 znaky)!<br>';
			$err = true;
		}
		
		if (strlen($_POST["content"]) == 0) {
			$message .= 'Obsah je prázdný!<br>';
			$err = true;
		}
		
		if (!is_numeric($_POST["access"]) or strlen($_POST["access"]) > 4 or $_POST["access"] < 0) {
			$message .= 'Přístup není kladné číslo nebo je moc dlouhé (max. 4 znaky)!<br>';
			$err = true;
		}
		
		if ($err) {
			$message = '<div class="alert alert-danger"><p><strong>Při ukládání došlo k následujícím chybám:</strong></p><p>'. $message .'</p></div>';
			break;
		}
		
		$mysql->query("INSERT INTO `boxes`(`ord`, `content`, `visible`, `access`) VALUES (". $mysql->quote($_POST["ord"]) .", ". $mysql->quote($_POST["content"]) .", ". parse_from_checkbox($_POST["visible"]) .", ". $mysql->quote($_POST["access"]) .");");
		header("Location: admin.php?p=content&boxes");
		die();
		
	} while(0);
	
	$page["content"] .= $message . '<form method="post">Obsah: <textarea class="form-control separate" name="content">'. $_POST["content"] .'</textarea>Pořadí: <input type="text" name="ord" value="'. $_POST["ord"] .'" class="form-control separate">
Přístup: <input type="text" name="access" value="'. $_POST["access"] .'" class="form-control separate"><p><input type="checkbox" name="visible"'. parse_to_checkbox(parse_from_checkbox($_POST["visible"])) .'> Viditelné</p><button type="submit" name="submit" class="btn btn-primary">Vytvořit box</button>
<input type="hidden" name="csrf" value="'. generate_csrf() .'"></form>';
	
} while(0);