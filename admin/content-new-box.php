<?php

if (!$_SESSION["access_admin_content"] > 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Správa obsahu - vytvoření boxu';

if ($_SESSION["access_admin_content_boxes"] > 0) {
			
	if (isset($_POST["submit"])) {
		
		if (!validate_csrf($_POST["csrf"])) {
			$message = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
		} elseif (!is_numeric($_POST["ord"]) or strlen($_POST["ord"]) > 4 or $_POST["ord"] < 0) {
			$err = true;
			$message .= '<div class="alert alert-danger"><strong>Pořadí není číslo nebo je moc dlouhé (max. 4 znaky)!</strong></div>';
		} elseif (strlen($_POST["content"]) == 0) {
			$err = true;
			$message .= '<div class="alert alert-danger"><strong>Obsah je prázdný!</strong></div>';
		} elseif (!is_numeric($_POST["access"]) or strlen($_POST["access"]) > 4 or $_POST["access"] < 0) {
			$err = true;
			$message .= '<div class="alert alert-danger"><strong>Přístup není číslo nebo je moc dlouhé (max. 4 znaky)!</strong></div>';
		} else {
			
			$mysql->query("INSERT INTO `boxes`(`ord`, `content`, `visible`, `access`) VALUES (". $mysql->quote($_POST["ord"]) .", ". $mysql->quote($_POST["content"]) .", ". parse_from_checkbox($_POST["visible"]) .", ". $mysql->quote($_POST["access"]) .");");
			header("Location: admin.php?p=content&boxes");
			die();
		
		}
	}
			
	$page["content"] .= $message . '<form method="post">Obsah: <textarea class="form-control separate" name="content">'. $_POST["content"] .'</textarea>Pořadí: <input type="text" name="ord" value="'. $_POST["ord"] .'" class="form-control separate">
	Přístup: <input type="text" name="access" value="'. $_POST["access"] .'" class="form-control separate"><p><input type="checkbox" name="visible"'. parse_to_checkbox(parse_from_checkbox($_POST["visible"])) .'> Viditelné</p><button type="submit" name="submit" class="btn btn-primary">Vytvořit box</button>
	<input type="hidden" name="csrf" value="'. generate_csrf() .'"></form>';
			
} else {
	$page["content"] .= '<div class="alert alert-danger"><strong>Vytvoření: chybí oprávnění!</strong></div>';
}

} ?>