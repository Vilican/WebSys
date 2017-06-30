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
<form action="index.php?p=settings&newavatar" method="post"><table style="border-spacing:10px">
<tr><td>Avatar:</td><td><img src="upload/avatars/'. $path .'" class="avatar img-responsive" alt="Profilový obrázek"></td></tr>
<tr><td>Nahrát:</td><td><input type="text" name="email" class="form-control"> <input type="submit" name="setavatar" value="Nahrát" class="btn btn-default"></td></tr>
<tr><td>&nbsp;</td><td><a href="index.php?p=settings&newavatar&removeavatar&csrf='. $csrf .'" class="btn btn-danger">Smazat avatara</a></td></tr>
<input type="hidden" name="csrf" value="'. $csrf .'"></table></form>';

} else {

$page["title"] = "Uživatelské nastavení";

$page["content"] = $message .'
<form action="index.php?p=settings" method="post">
<table style="border-spacing:10px">
<tr><td>Avatar:</td><td><p><img src="upload/avatars/'. $path .'" class="avatar img-responsive" alt="Profilový obrázek"></p><p><a class="btn btn-primary" href="index.php?p=settings&newavatar">Změnit avatara</a></p></td></tr>
<tr><td>Heslo:</td><td><a class="btn btn-primary" href="index.php?p=settings&chpass">Změnit heslo</a></td></tr>
<tr><td>Přihlašovací jméno:</td><td><input type="text" name="loginname" value="'. restore_value(santise($usr["loginname"]), santise($_POST["loginname"])) .'" class="form-control"></td></tr>
<tr><td>Uživatelské jméno:</td><td><input type="text" name="username" value="'. restore_value(santise($usr["username"]), santise($_POST["username"])) .'" class="form-control"></td></tr>
<tr><td>Email:</td><td><input type="text" name="email" value="'. restore_value(santise($usr["email"]), santise($_POST["email"])) .'" class="form-control"></td></tr>
'. $email_val . $next_fields .'
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Aktualizovat" class="btn btn-default"></td></tr>
</table><input type="hidden" name="csrf" value="'. $csrf .'"></form>';

}

require "template/page.php";