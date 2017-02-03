<?php

if (!$_SESSION["access_admin_content"] > 0) {
	$page["content"] = '<div class="alert alert-danger"><strong>Nemáte dostatečné oprávnění ke vstupu do tohoto modulu!</strong></div>';
} else {

$page["title"] = 'Správa obsahu';

$page_types = array(1 => 'stránka', 2 => 'diskuze', 3 => 'fórum');
$page_type_ids = array(1 => 'page', 2 => 'talk', 3 => 'forum');

if (isset($_POST["type"])) {
	header("Location: admin.php?p=content-new-". $page_type_ids[$_POST["type"]]);
	die();
}

foreach ($page_types as $id => $label) {
	$options .= '<option value="'. $id .'">'. $label .'</option>';
}

$page["content"] .= '<ul class="nav nav-inpage nav-tabs">';

if ($_SESSION["access_admin_content_edit"] > 0 or $_SESSION["access_admin_content_edit_all"] > 0 or $_SESSION["access_admin_content_changeeditor"] > 0 or $_SESSION["access_admin_content_delete"] > 0 or $_SESSION["access_admin_content_delete_all"] > 0) {
	$page["content"] .= '<li><a data-toggle="tab" href="#pages">Stránky</a></li>';
}
if ($_SESSION["access_admin_content_articles_edit"] > 0 or $_SESSION["access_admin_content_articles_edit_all"] > 0 or $_SESSION["access_admin_content_articles_changeeditor"] > 0 or $_SESSION["access_admin_content_articles_delete"] > 0 or $_SESSION["access_admin_content_articles_delete_all"] > 0) {
	$page["content"] .= '<li><a data-toggle="tab" href="#articles">Články</a></li>';
}
if ($_SESSION["access_admin_content_boxes"] > 0) {
	if (isset($_GET["boxes"])) {
		$li_boxes = ' class="active"';
		$div_boxes = ' in active';
	}
	$page["content"] .= '<li'. $li_boxes .'><a data-toggle="tab" href="#boxes">Boxy</a></li>';
}
if ($_SESSION["access_admin_content_review"] > 0) {
	$page["content"] .= '<li><a data-toggle="tab" href="#review">Revize</a></li>';
}

$page["content"] .= '</ul><div class="tab-content">';

if ($_SESSION["access_admin_content_edit"] > 0 or $_SESSION["access_admin_content_edit_all"] > 0 or $_SESSION["access_admin_content_changeeditor"] > 0 or $_SESSION["access_admin_content_delete"] > 0 or $_SESSION["access_admin_content_delete_all"] > 0) {

$page["content"] .= '<div id="pages" class="tab-pane fade">';

if ($_SESSION["access_admin_content_edit"] > 0 or $_SESSION["access_admin_content_edit_all"] > 0) {
	$page["content"] .= '<p><form method="post" action="admin.php?p=content">
Vytvořit: <select name="type">'. $options .'</select>
<button type="submit" class="btn btn-primary btn-xs">Přidat</button>
</form></p>';
}

$page["content"] .= '<table class="table table-hover table-hover table-bordered table-responsive"><thead><tr><th>ID</th><th>Název</th><th>Editor</th><th>Typ</th><th>Přístup</th><th>Pořadí</th><th>Akce</th></tr></thead><tbody>';

$pages = $mysql->query("SELECT `pages`.`id`, `pages`.`title`, `pages`.`type`, `pages`.`ord`, `pages`.`author`, `pages`.`visible`, `pages`.`access`, `users`.`username` FROM `pages` INNER JOIN `users` ON `pages`.`author` = `users`.`id` ORDER BY `pages`.`ord` ASC;");
if ($pages->num_rows > 0) {
	while($row_page = $pages->fetch_assoc()) {
		$actions = null;
		if ($row_page["access"] == 0) { $row_page["access"] = "všichni"; }
		if ($_SESSION["access_admin_content_edit_all"] > 0 or ($_SESSION["access_admin_content_edit"] > 0 and $row_page["author"] == $_SESSION["id"])) {
			$actions = '<a href="admin.php?p=content-edit-'. $page_type_ids[$row_page["type"]] .'&id='. $row_page["id"] .'" class="btn btn-default">Upravit</a>
			<a href="admin.php?p=content-history&id='. $row_page["id"] .'" class="btn btn-primary">Historie</a>';
		}
		if ($_SESSION["access_admin_content_changeeditor"] > 0) {
			$actions .= '<a href="admin.php?p=content-change-author&id='. $row_page["id"] .'" class="btn btn-warning">Změnit editora</a>';
		}
		if ($_SESSION["access_admin_content_delete_all"] > 0 or ($_SESSION["access_admin_content_delete"] > 0 and $row_page["author"] == $_SESSION["id"])) {
			$actions .= '<a href="admin.php?p=content-delete&id='. $row_page["id"] .'" class="btn btn-danger">Smazat</a>';
		}
		$page["content"] .= "<tr><td>". $row_page["id"] ."</td><td>". $row_page["title"] ."</td><td>". $row_page["username"] ."</td><td>". $page_types[$row_page["type"]] ."</td><td>". $row_page["access"] ."</td><td>". $row_page["ord"] .'</td><td><div class="btn-group btn-group-sm">'. $actions .'</div></td></tr>';
	}
}

$page["content"] .= '</tbody></table></div>';

}

if ($_SESSION["access_admin_content_articles_edit"] > 0 or $_SESSION["access_admin_content_articles_edit_all"] > 0 or $_SESSION["access_admin_content_articles_changeeditor"] > 0 or $_SESSION["access_admin_content_articles_delete"] > 0 or $_SESSION["access_admin_content_articles_delete_all"] > 0) {

$page["content"] .= '<div id="articles" class="tab-pane fade">a</div>';

}

if ($_SESSION["access_admin_content_boxes"] > 0) {

if (isset($_POST["submit"]) and validate_csrf($_POST["csrf"])) {
	unset($_POST["submit"]);
	unset($_POST["csrf"]);
	foreach ($_POST as $key => $val) {
		$key = explode("-", $key);
		$boxes[$key[1]][$key[0]] = $val;
	}
	foreach ($boxes as $box_id => $box_param) {
		if (!is_numeric($box_param["ord"]) or strlen($box_param["ord"]) > 4 or $box_param["ord"] < 0) {
			$err = true;
			$message .= '<div class="alert alert-danger"><strong>Pořadí není číslo nebo je moc dlouhé (max. 4 znaky)!</strong></div>';
		} elseif (strlen($box_param["content"]) == 0) {
			$err = true;
			$message .= '<div class="alert alert-danger"><strong>Obsah je prázdný!</strong></div>';
		} elseif (!is_numeric($box_param["access"]) or strlen($box_param["access"]) > 4 or $box_param["access"] < 0) {
			$err = true;
			$message .= '<div class="alert alert-danger"><strong>Přístup není číslo nebo je moc dlouhé (max. 4 znaky)!</strong></div>';
		} else {
			$mysql->query("UPDATE `boxes` SET `ord` = ". $mysql->quote($box_param["ord"]) .", `content` = ". $mysql->quote($box_param["content"]) .", `visible` = ". parse_from_checkbox($box_param["visible"]) .", `access` = ". $mysql->quote($box_param["access"]) ." WHERE `id` = ". $mysql->quote($box_id) .";");
		}
	}
	if ($err == true) {
		$message .= '<div class="alert alert-warning"><strong>Některé boxy nemohly být upraveny</strong></div>';
	} else {
		$message .= '<div class="alert alert-success"><strong>Boxy upraveny</strong></div>';
	}
}

$boxes = $mysql->query("SELECT * FROM `boxes` ORDER BY `ord` ASC;");
if ($boxes->num_rows > 0) {
	$page["content"] .= '<div id="boxes" class="tab-pane fade'. $div_boxes .'">'. $message .'<form method="post" action="admin.php?p=content&boxes"><button name="submit" type="submit" class="btn btn-primary">Uložit změny</button> <a href="admin.php?p=content-new-box" class="btn btn-primary">Přidat box</a><hr>';
	while($box = $boxes->fetch_assoc()) {
		$pom++;
		if ($pom % 2 != 0) {
			$page["content"] .= '<div class="row separate">';
		}
		
		$page["content"] .= '<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">Obsah: <textarea class="form-control separate" name="content-'. $box["id"] .'">'. $box["content"] .'</textarea>Pořadí: <input type="text" name="ord-'. $box["id"] .'" value="'. $box["ord"] .'" class="form-control separate">
		Přístup: <input type="text" name="access-'. $box["id"] .'" value="'. $box["access"] .'" class="form-control separate"><input type="checkbox" name="visible-'. $box["id"] .'"'. parse_to_checkbox($box["visible"]) .'> Viditelné
		<a href="admin.php?p=content-delete-box&id='. $box["id"] .'" class="btn btn-danger btn-xs rfloat">Odstranit</a></div>';
		
		if ($pom % 2 == 0) {
			$page["content"] .= '</div><hr>';
		}
	}
	if ($pom % 2 != 0) {
		$page["content"] .= '</div><hr>';
	}
	$page["content"] .= '<input type="hidden" name="csrf" value="'. generate_csrf() .'"></form></div>';
} else {
	$page["content"] = '<div class="alert alert-warning"><strong>Neexistují žádné boxy!</strong></div>';
}

}

if ($_SESSION["access_admin_content_review"] > 0) {

$page["content"] .= '<div id="review" class="tab-pane fade"><p>Jsou k dispozici tyto revizní fronty:</p>';

	if ($_SESSION["access_admin_content_review_deleted"] > 0) {
		
		$post_count = $mysql->query("SELECT `post_id` FROM `posts` WHERE `deleted` = 1;")->num_rows;
		
		if ($post_count > 0) {
			$badge = ' <span class="label label-danger">'. $post_count .'</span>';
			$btn_type = 'warning';
		} else {
			$badge = null;
			$btn_type = 'primary';
		}
		
		$page["content"] .= '<p><a href="admin.php?p=content-review-deleted" class="btn btn-'. $btn_type .'">Smazané příspěvky '. $badge .'</a>';
		if ($_SESSION["access_admin_content_review_deleted_purge"] > 0) {
			$page["content"] .= ' Speciální možnosti: <a href="admin.php?p=content-review-deleted&purge" class="btn btn-danger btn-xs">Odstranit smazané</a>';
		}
		$page["content"] .= '</p>';
	}

	if ($_SESSION["access_admin_content_review_flags"] > 0) {
		
		$flag_count = $mysql->query("SELECT DISTINCT(`post_id`) FROM `flags` INNER JOIN `posts` ON `flags`.`post` = `posts`.`post_id`;")->num_rows;
		
		if ($flag_count > 0) {
			$badge = ' <span class="label label-danger">'. $flag_count .'</span>';
			$btn_type = 'warning';
		} else {
			$badge = null;
			$btn_type = 'primary';
		}
		
		$page["content"] .= '<p><a href="admin.php?p=content-review-flags" class="btn btn-'. $btn_type .'">Nahlášené příspěvky '. $badge .'</a></p>';
	}
	
$page["content"] .= '</div>';
	
}

$page["content"] .= '</div>';

} ?>