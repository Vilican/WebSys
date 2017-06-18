<?php

$page["title"] = "Ztracené heslo";

if ($_SESSION["authorized"]) {

$page["content"] = $message .'
<form action="index.php?p=lostpass" method="post">
<table style="border-spacing:10px">
<tr><td>Heslo:</td><td><input type="password" name="pass" class="form-control" autocomplete="off"></td></tr>
<tr><td>Heslo znovu:</td><td><input type="password" name="pass2" class="form-control" autocomplete="off"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit3" value="Dokončit reset" class="btn btn-default"></td></tr>
</table></form>';

}

if ($initiated) {

$page["content"] = '<div class="alert alert-info"><strong>Pokud zadané údaje odpovídají, dostanete email s kódem.</strong></div>
<div class="alert alert-warning"><strong>Na zadání tohoto kódu máte jen jeden pokus. Poté si budete muset vyžádat nový kód.</strong></div>
<form action="index.php?p=lostpass" method="post">
<table style="border-spacing:10px">
<tr><td>Potvrzovací kód:</td><td><input type="text" name="code" class="form-control" autocomplete="off"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit2" value="Pokračovat" class="btn btn-default"></td></tr>
</table></form>';

}

if (!$initiated and !$_SESSION["authorized"]) {

$page["content"] = $message .'
<form action="index.php?p=lostpass" method="post">
<table style="border-spacing:10px">
<tr><td>Uživatel:</td><td><input type="text" name="user" class="form-control" autocomplete="off"></td></tr>
<tr><td>Email:</td><td><input type="text" name="email" class="form-control" autocomplete="off"></td></tr>
<tr><td>Captcha</td><td><img id="captcha" src="lib/captcha.php?t=kofl" width="120" height="30" border="1">
<a href="#" onclick="document.getElementById(\'captcha\').src = \'lib/captcha.php?t=kofl&amp;tk=\' + Math.random(); document.getElementById(\'captcha_code\').value = \'\'; return false;">Změnit kód</a>
<input type="text" name="captcha" class="form-control"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Zahájit reset" class="btn btn-default"></td></tr>
</table></form>';

}

require "template/page.php";