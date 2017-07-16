<?php

if ($sys["twofactor_gauth"] == 1 || $sys["twofactor_yubi"] == 1) {
	$twofactor_form = '<tr><td>Druhý faktor:</td><td><input type="text" name="twofactor" class="form-control" autocomplete="off" placeholder="Pouze pokud máte zapnutý"></td></tr>';
}

$page["title"] = "Přihlášení";

$page["content"] = $message .'
<form action="index.php?p=login" method="post">
<table style="border-spacing:10px">
<tr><td>Uživatel:</td><td><input type="text" name="user" value="'. santise($_POST["user"]) .'" class="form-control" autocomplete="off"></td></tr>
<tr><td>Heslo:</td><td><input type="password" name="pass" class="form-control" autocomplete="off"></td></tr>
'. $twofactor_form .'
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Přihlásit se" class="btn btn-default"></td></tr>
</table><input type="hidden" name="csrf" value="'. generate_csrf() .'"></form>';

if ($sys["lostpass"] == 1) {
	$page["content"] .= '<br><a href="index.php?p=lostpass">Nemůžu se přihlásit >></a>';
}

require "template/page.php";