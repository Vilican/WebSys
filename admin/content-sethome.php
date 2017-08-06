<?php

do {
	
	if (!has_access("admin_content") or !has_access("admin_content_sethome")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - změna hlavní stránky';
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$page["content"] = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		$mysql->query("UPDATE `settings` SET `value` = ". $mysql->quote($_POST["mainpage"]) ." WHERE `setting` = 'homepage';");
		
		header("Location: admin.php?p=content-sethome");
		die();
		
	} while(0);
	
	$pages = $mysql->query("SELECT `id`, `title` FROM `pages` ORDER BY `title` ASC;");
	if ($pages->num_rows > 0) {
		while($page_opt = $pages->fetch_assoc()) {
			if ($sys["homepage"] == $page_opt["id"]) {
				$selected = ' selected="selected"';
			} else {
				$selected = null;
			}
			$page_options .= '<option value="'. $page_opt["id"] .'"'. $selected .'>'. $page_opt["title"] .'</option>';
		}
	}
			
	$page["content"] .= '<p>Hlavní stránka se zobrazí, když není v adrese specifikována jiná stránka, například když uživatel zadá jen adresu vašich stránek ('. $_SERVER["HTTP_HOST"] .')</p><form method="post">
<div class="form-group"><label for="mainpage">Hlavní stránka:</label><select name="mainpage" id="mainpage" class="form-control">'. $page_options .'</select></div>
<input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Změnit" class="btn btn-warning"></form>';
	
} while(0);