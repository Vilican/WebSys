<?php

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
$page["content"] .= '<div id="boxes" class="tab-pane fade'. $div_boxes .'">'. $message .'<form method="post" action="admin.php?p=content&boxes"><button name="submit" type="submit" class="btn btn-primary">Uložit změny</button> <a href="admin.php?p=content-new-box" class="btn btn-primary">Přidat box</a><hr>';
while ($boxes->num_rows > 0 and $box = $boxes->fetch_assoc()) {
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