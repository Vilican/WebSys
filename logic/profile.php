<?php

$usr = $mysql->query("SELECT * FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `id` = ". $mysql->quote($_GET["id"]) ." LIMIT 1;");

if ($usr->num_rows == 0) {
	$page["title"] = "Profil";
	$page["content"] = '<div class="alert alert-danger"><strong>Tento uživatel neexistuje</strong></div>';
	require "template/page.php";
	exit;
}

$usr = $usr->fetch_assoc();
$userfields = $mysql->query("SELECT * FROM `userfields` WHERE `internalonly` = 0 AND `public` = 1 ORDER BY `ord` ASC;");

if ($userfields->num_rows > 0) {
	while ($field = $userfields->fetch_assoc()) {
		if ($field["type"] == "text") {
			$next_fields .= '<tr><td>'. $field["label"] .':</td><td>'. $usr[$field["name"]] .'</td></tr>';
			continue;
		}
		$next_fields .= '<tr><td>'. $field["label"] .':</td><td>'. $usr[$field["name"]] .'></td></tr>';
	}
}