<?php

if (!$_SESSION["access_admin_content"] > 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Správa obsahu - smazání boxu';

if (isset($_GET["id"])) {
	$box = $mysql->query("SELECT `content` FROM `boxes` WHERE `id` = ". $mysql->quote($_GET["id"]) .";");
	if ($box->num_rows > 0) {
		$box = $box->fetch_assoc();
		if ($_SESSION["access_admin_content_boxes"] > 0) {
			
			if (isset($_POST["submit"])) {
			
				if (!validate_csrf($_POST["csrf"])) {
					$message = '<div class="alert alert-danger"><strong>CSRF: kontrola nesouhlasí; to může znamenat pokus o útok!</strong></div>';
				} else {
					$mysql->query("DELETE FROM `boxes` WHERE `id` = ". $mysql->quote($_GET["id"]) .";");
					header("Location: admin.php?p=content&boxes");
					die();
				}
			}
			
			$page["content"] .= '<form method="post"><table style="border-spacing: 10px">
<tr><td>Box:</td><td><textarea class="form-control" disabled="disabled">'. $box["content"] .'</textarea></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="csrf" value="'. generate_csrf() .'"><input type="submit" name="submit" value="Smazat" class="btn btn-danger"></td></tr>
</table></form>';
			
		} else {
			$page["content"] .= '<div class="alert alert-danger"><strong>Smazání: chybí oprávnění!</strong></div>';
		}
	} else {
		$page["content"] .= '<div class="alert alert-danger"><strong>Smazání: taková stránka neexistuje!</strong></div>';
	}
} else {
	$page["content"] .= '<div class="alert alert-danger"><strong>Smazání: taková stránka neexistuje!</strong></div>';
}

} ?>