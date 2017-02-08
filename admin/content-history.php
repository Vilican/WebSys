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
	
	if ($_GET["a"] = "ch" and is_numeric($_GET["r"])) do {
		
		// TODO: REVIEW AND REPAIR
		
		require "lib/diff.php";
		$records = $mysql->query("SELECT `phistory`.*, `users`.`username` FROM `phistory` INNER JOIN `users` ON `phistory`.`author` = `users`.`id` WHERE `phistory`.`page` = ". $mysql->quote($pg["id"]) ." ORDER BY `time` ASC LIMIT 2 OFFSET ". ($_GET["r"] - 1) .";");
		
		if ($records->num_rows == 1 or $_GET["r"] == 1) {
			$record = $records->fetch_assoc();
			$page["content"] .= 'Provedl uživatel '. $record["username"] .', '. date("j.n.Y G:i", strtotime($record["time"])) .' <a href="admin.php?p=content-history&id='. $pg["id"] .'&a=rv&r='. $record["id"] .'" class="btn btn-danger btn-sm separate-horizontal">Vrátit k této editaci</a><hr style="">'. $page["content"];
			break;
		}
		
		if ($records->num_rows > 1) {
			while ($record = $records->fetch_assoc()) {
				
			}
			
			$page["content"] .= 'Provedl uživatel '. $new["username"] .', '. date("j.n.Y G:i", strtotime($new["time"])) .' <a href="admin.php?p=content-history&id='. $pg["id"] .'&a=rv&r='. $new["id"] .'" class="btn btn-danger btn-sm separate-horizontal">Vrátit k této editaci</a><hr style="">';
			
			$diff = new HtmlDiff($string_old, $string_new);
			$diff->build();
			$page["content"] .= $diff->getDifference();
			break;
			
		}
		
		break(2);
		
	} while(0);
	
	if ($_GET["a"] = "rv" and is_numeric($_GET["r"])) do {
		
		// TODO: REVERT EDIT
		
		break(2);
		
	} while(0);
	
	$page["content"] .= '<div class="panel-group">';
	$history = $mysql->query("SELECT `phistory`.*, `users`.`username` FROM `phistory` INNER JOIN `users` ON `phistory`.`author` = `users`.`id` WHERE `phistory`.`page` = ". $mysql->quote($pg["id"]) ." ORDER BY `time` DESC;");
	if ($history->num_rows > 0) {
		$i = $history->num_rows;
		while ($record = $history->fetch_assoc()) {
			if (has_access("admin_content_revert")) {
				$revert = ' <a href="admin.php?p=content-history&id='. $pg["id"] .'&a=rv&r='. $record["id"] .'" class="btn btn-danger btn-sm">Vrátit k této editaci</a>';
			} else {
				$revert = null;
			}	
			$page["content"] .= '<div class="panel panel-default"><div class="panel-body">Editace #'. $i .', uživatel '. $record["username"] .', '. date("j.n.Y G:i", strtotime($record["time"])) .' <a href="admin.php?p=content-history&id='. $pg["id"] .'&a=ch&r='. $record["id"] .'" class="btn btn-primary btn-sm separate-horizontal">Změny</a>'. $revert .'</div></div>';
			$i--;
		}
	}
	$page["content"] .= '</div>';
	
} while(0);