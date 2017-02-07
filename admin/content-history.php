<?php

if (!$_SESSION["access_admin_content"] > 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Správa obsahu - historie stránky';

if (isset($_GET["id"])) {
	$pg = $mysql->query("SELECT * FROM `pages` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");
	if ($pg->num_rows > 0) {
		$pg = $pg->fetch_assoc();
		if ($_SESSION["access_admin_content_edit_all"] > 0 or ($_SESSION["access_admin_content_edit"] > 0 and $pg["author"] == $_SESSION["id"])) {
			
			if ($_GET["a"] = "ch" and is_numeric($_GET["r"])) {
				
				require "lib/diff.php";
				
				$records = $mysql->query("SELECT `phistory`.*, `users`.`username` FROM `phistory` INNER JOIN `users` ON `phistory`.`author` = `users`.`id` WHERE `phistory`.`page` = ". $mysql->quote($pg["id"]) ." ORDER BY `time` ASC LIMIT 2 OFFSET ". ($_GET["r"] - 1) .";");
				
				if ($records->num_rows == 1 or $_GET["r"] == 1) {
					$record = $records->fetch_assoc();
					$page["content"] .= 'Provedl uživatel '. $record["username"] .', '. date("j.n.Y G:i", strtotime($record["time"])) .' <a href="admin.php?p=content-history&id='. $pg["id"] .'&a=rv&r='. $record["id"] .'" class="btn btn-danger btn-sm separate-horizontal">Vrátit k této editaci</a><hr style="">'. $page["content"];
				} elseif ($records->num_rows > 1) {
					while ($record = $records->fetch_assoc()) {
						
					}
					
					$page["content"] .= 'Provedl uživatel '. $new["username"] .', '. date("j.n.Y G:i", strtotime($new["time"])) .' <a href="admin.php?p=content-history&id='. $pg["id"] .'&a=rv&r='. $new["id"] .'" class="btn btn-danger btn-sm separate-horizontal">Vrátit k této editaci</a><hr style="">';
				
					$diff = new HtmlDiff($string_old, $string_new);
					$diff->build();
					$page["content"] .= $diff->getDifference();
					
				} else {
					#header("Location: admin.php?p=content-history&id=". $pg["id"]);
					#die();
				}
				
			} elseif ($_GET["a"] = "rv" and is_numeric($_GET["r"])) {
				
				
			} else {
			
				$page["content"] .= '<div class="panel-group">';
			
				$history = $mysql->query("SELECT `phistory`.*, `users`.`username` FROM `phistory` INNER JOIN `users` ON `phistory`.`author` = `users`.`id` WHERE `phistory`.`page` = ". $mysql->quote($pg["id"]) ." ORDER BY `time` DESC;");
				if ($history->num_rows > 0) {
					$i = $history->num_rows;
					while ($record = $history->fetch_assoc()) {
					
						if ($_SESSION["access_admin_content_revert"] > 0) {
							$revert = ' <a href="admin.php?p=content-history&id='. $pg["id"] .'&a=rv&r='. $record["id"] .'" class="btn btn-danger btn-sm">Vrátit k této editaci</a>';
						} else {
							$revert = null;
						}
					
						$page["content"] .= '<div class="panel panel-default"><div class="panel-body">Editace #'. $i .', uživatel '. $record["username"] .', '. date("j.n.Y G:i", strtotime($record["time"])) .' <a href="admin.php?p=content-history&id='. $pg["id"] .'&a=ch&r='. $record["id"] .'" class="btn btn-primary btn-sm separate-horizontal">Změny</a>'. $revert .'</div></div>';
						$i--;

					}
				}
			
				$page["content"] .= '</div>';
				
			}
			
		} else {
			$page["content"] .= '<div class="alert alert-danger"><strong>Editace: chybí oprávnění!</strong></div>';
		}
	} else {
		$page["content"] .= '<div class="alert alert-danger"><strong>Editace: taková stránka neexistuje!</strong></div>';
	}
} else {
	$page["content"] .= '<div class="alert alert-danger"><strong>Editace: taková stránka neexistuje!</strong></div>';
}

}