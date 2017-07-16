<?php

do {

	if (!has_access("admin_content")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - smazání stránky';
	
	if (!isset($_GET["id"])) {
		$page["content"] = '<div class="alert alert-danger"><strong>Smazání: taková stránka neexistuje!</strong></div>';
		break;
	}
	
	$pg = $mysql->query("SELECT `pages`.`id`, `pages`.`title`, `pages`.`type`, `pages`.`author` FROM `pages` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($pg->num_rows == 0) {
		$page["content"] = '<div class="alert alert-danger"><strong>Smazání: taková stránka neexistuje!</strong></div>';
		break;
	}
	
	$pg = $pg->fetch_assoc();
	
	if (!has_access("admin_content_delete_all") and !(has_access("admin_content_delete") and $pg["author"] == $_SESSION["id"])) {
		$page["content"] = '<div class="alert alert-danger"><strong>Smazání: chybí oprávnění!</strong></div>';
		break;
	}
	
	if ($pg["type"] == 4) {
		$msg = '<br><p class="text-info">Poznámka: Smazáním galerie nesmažete složku s obrázky.</p>';
	}
	
	if ($pg["type"] == 5) {
		$msg = '<br><p class="text-danger">POZOR: Smazáním kategorie se permanentně smažou i všechny články v ní!</p>';
	}
	
	if (isset($_POST["submit"])) do {
		
		if (!validate_csrf($_POST["csrf"])) {
			$page["content"] = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
			break;
		}
		
		if ($pg["id"] == $sys["homepage"]) {
			$page["content"] = '<div class="alert alert-danger"><strong>Smazání: nelze smazat hlavní stránku!</strong></div>';
			break;
		}
		
		$mysql->query("DELETE FROM `pages` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");
		header("Location: admin.php?p=content");
		die();
		
	} while(0);
	
	$page["content"] .= '<form method="post"><table style="border-spacing: 10px">
<tr><td>Stránka:</td><td><input type="text" class="form-control" value="'. $pg["title"] .'" disabled="disabled"></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Smazat" class="btn btn-danger"></td></tr>
</table>'. $msg .'</form>';

} while(0);