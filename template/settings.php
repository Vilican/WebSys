<?php

if (isset($_GET["chpass"])) {

$page["title"] = "Uživatelské nastavení - změna hesla";

$page["content"] = $message .'
<form action="index.php?p=settings&chpass" method="post">
<table style="border-spacing:10px">
<tr><td>Staré heslo:</td><td><input type="password" name="oldpass" class="form-control"></td></tr>
<tr><td>Nové heslo:</td><td><input type="password" name="newpass" class="form-control"></td></tr>
<tr><td>Heslo znovu:</td><td><input type="password" name="newpass2" class="form-control"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Změnit" class="btn btn-default"></td></tr>
</table><input type="hidden" name="csrf" value="'. $csrf .'"></form>';

} elseif (isset($_GET["emailval"])) {

$page["title"] = "Uživatelské nastavení - ověření emailu";

$page["content"] = $message .'
<table style="border-spacing:10px"><form action="index.php?p=settings&emailval" method="post">
<tr><td>Captcha</td><td><img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input type="text" name="captcha" class="form-control"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="request" value="Poslat kód" class="btn btn-default"></td></tr>
<input type="hidden" name="csrf" value="'. $csrf .'"></form>
<form action="index.php?p=settings&emailval" method="post">
<tr><td>Kód</td><td><input type="text" name="valcode" class="form-control"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="validate" value="Validovat" class="btn btn-default"></td></tr>
<input type="hidden" name="csrf" value="'. $csrf .'"></form></table>';

} elseif (isset($_GET["newavatar"])) {

$page["title"] = "Uživatelské nastavení - správa avatara";

$page["content"] = $message .'
<form action="index.php?p=settings&newavatar" method="post" enctype="multipart/form-data"><table style="border-spacing:10px">
<tr><td>Avatar:</td><td><img src="'. displayAvatar($_SESSION["id"]) .'" class="avatar img-responsive" alt="Profilový obrázek"></td></tr>
'. $avatar_actions .'
<input type="hidden" name="csrf" value="'. $csrf .'"></table></form>';

} elseif (isset($_GET["2fa"]) and ($sys["twofactor_yubi"] or $sys["twofactor_gauth"])) {

$page["title"] = "Uživatelské nastavení - druhý faktor";

$page["content"] = $message .'
<form action="index.php?p=settings&2fa" method="post"><table style="border-spacing:10px">
'. $gauth . $yubikey .'
<input type="hidden" name="csrf" value="'. $csrf .'"></table></form>';

} else {

if ($sys["twofactor_yubi"] || $sys["twofactor_gauth"]) {
	$button_2fa = ' <a class="btn btn-primary" href="index.php?p=settings&2fa">Druhý faktor</a>';
}

$page["title"] = "Uživatelské nastavení";

$page["content"] = $message .'
<p><a class="btn btn-primary" href="index.php?p=settings&newavatar">Změnit avatara</a> <a class="btn btn-primary" href="index.php?p=settings&chpass">Změnit heslo</a>'. $button_2fa .'</p>
<form action="index.php?p=settings" method="post">
<p><img src="'. displayAvatar($_SESSION["id"]) .'" class="avatar img-responsive" alt="Profilový obrázek"></p>
<table style="border-spacing:10px">
<tr><td>Přihlašovací jméno:</td><td><input type="text" name="loginname" value="'. restore_value(santise($usr["loginname"]), santise($_POST["loginname"])) .'" class="form-control"></td></tr>
<tr><td>Uživatelské jméno:</td><td><input type="text" name="username" value="'. restore_value(santise($usr["username"]), santise($_POST["username"])) .'" class="form-control"></td></tr>
<tr><td>Email:</td><td><input type="text" name="email" value="'. restore_value(santise($usr["email"]), santise($_POST["email"])) .'" class="form-control"></td></tr>
'. $email_val . $next_fields .'
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Aktualizovat" class="btn btn-default"></td></tr>
</table><input type="hidden" name="csrf" value="'. $csrf .'"></form>';

}

require "template/page.php";