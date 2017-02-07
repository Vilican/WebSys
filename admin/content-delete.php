<?php

if (!$_SESSION["access_admin_content"] > 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Správa obsahu - smazání stránky';

if (isset($_GET["id"])) {
	$pg = $mysql->query("SELECT `pages`.`id`, `pages`.`title` FROM `pages` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");
	if ($pg->num_rows > 0) {
		$pg = $pg->fetch_assoc();
		if ($_SESSION["access_admin_content_delete_all"] > 0 or ($_SESSION["access_admin_content_delete"] > 0 and $pg["author"] == $_SESSION["id"])) {
			
			if ($pg["id"] != $sys["homepage"]) {
			
				if (isset($_POST["submit"])) {
				
					if (!validate_csrf($_POST["csrf"])) {
						$message = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
					} else {
						$mysql->query("DELETE FROM `pages` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");
						header("Location: admin.php?p=content");
						die();
					}
				}
			
				$page["content"] .= '<form method="post"><table style="border-spacing: 10px">
<tr><td>Stránka:</td><td><input type="text" class="form-control" value="'. $pg["title"] .'" disabled="disabled"></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Smazat" class="btn btn-danger"></td></tr>
</table></form>';

			} else {
				
				$page["content"] .= '<form method="post"><table style="border-spacing: 10px">
<tr><td>Stránka:</td><td><input type="text" class="form-control" value="'. $pg["title"] .'" disabled="disabled"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Smazat" class="btn btn-danger" disabled="disabled"> <span class="text-danger">Nelze smazat havní stránku!</span></td></tr>
</table></form>';
				
			}
			
		} else {
			$page["content"] .= '<div class="alert alert-danger"><strong>Smazání: chybí oprávnění!</strong></div>';
		}
	} else {
		$page["content"] .= '<div class="alert alert-danger"><strong>Smazání: taková stránka neexistuje!</strong></div>';
	}
} else {
	$page["content"] .= '<div class="alert alert-danger"><strong>Smazání: taková stránka neexistuje!</strong></div>';
}

}