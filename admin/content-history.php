<?php

do {
	
	if (!has_access("admin_content")) {
		$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
		break;
	}
	
	$page["title"] = 'Správa obsahu - historie stránky';
	
	if (!isset($_GET["id"])) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Historie: taková stránka neexistuje!</strong></div>';
		break;
	}
	
	$pg = $mysql->query("SELECT * FROM `pages` WHERE `pages`.`id` = ". $mysql->quote($_GET["id"]) .";");
	
	if ($pg->num_rows == 0) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Historie: taková stránka neexistuje!</strong></div>';
		break;
	}
	
	$pg = $pg->fetch_assoc();
	
	if (!has_access("admin_content_edit_all") and !(has_access("admin_content_edit") and $pg["author"] == $_SESSION["id"])) {
		$page["content"] .= '<div class="alert alert-danger"><strong>Historie: chybí oprávnění!</strong></div>';
		break;
	}
	
	if ($_GET["a"] == "ch" and is_numeric($_GET["r"])) do {
		
		require "lib/diff.php";
		
		$records = $mysql->query("SELECT `phistory`.*, `users`.`username` FROM `phistory` INNER JOIN `users` ON `phistory`.`author` = `users`.`id` WHERE `phistory`.`page` = ". $mysql->quote($pg["id"]) ." AND `phistory`.`id` <= ". $mysql->quote($_GET["r"]) ." ORDER BY `time` DESC LIMIT 2;");
		
		if ($records->num_rows == 1) {
			$record = $records->fetch_assoc();
			$page["content"] .= '<p>Provedl uživatel '. $record["username"] .', '. date("j.n.Y G:i", strtotime($record["time"])) .'</p><em>Stránka byla při této editaci vytvořena.</em><hr style="">'. $record["content"];
		}
		
		if ($records->num_rows > 1) {
			for ($i = 1; $i <= $records->num_rows; $i++) {
				if ($i == 1) {
					$string_new = $records->fetch_assoc();
					continue;
				}
				$string_old = $records->fetch_assoc();
			}
			
			$page["content"] .= '<p>Provedl uživatel '. $string_new["username"] .', '. date("j.n.Y G:i", strtotime($string_new["time"])) .'</p><em>V rozdílech se nemusí zobrazovat změny formátování.</em><hr style="">';
			
			$diff = new HtmlDiff($string_old["content"], $string_new["content"]);
			$diff->build();
			$page["content"] .= $diff->getDifference();			
		}
		
		break(2);
		
	} while(0);
	
	$page["content"] .= '<div class="panel-group">';
	$history = $mysql->query("SELECT `phistory`.*, `users`.`username` FROM `phistory` INNER JOIN `users` ON `phistory`.`author` = `users`.`id` WHERE `phistory`.`page` = ". $mysql->quote($pg["id"]) ." ORDER BY `time` DESC;");
	if ($history->num_rows > 0) {
		$i = $history->num_rows;
		$csrf_token = generate_csrf();
		while ($record = $history->fetch_assoc()) {	
			$page["content"] .= '<div class="panel panel-default"><div class="panel-body">Editace #'. $i .', uživatel '. $record["username"] .', '. date("j.n.Y G:i", strtotime($record["time"])) .' <a href="admin.php?p=content-history&id='. $pg["id"] .'&a=ch&r='. $record["id"] .'" class="btn btn-primary btn-sm separate-horizontal">Změny obsahu</a></div></div>';
			$i--;
		}
	}
	$page["content"] .= '</div>';
	
} while(0);