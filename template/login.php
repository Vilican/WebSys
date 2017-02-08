<?php

$page["title"] = "Přihlášení";

$page["content"] = $message . '
<form action="index.php?p=login" method="post">
<table style="border-spacing: 10px">
<tr><td>Uživatel:</td><td><input type="text" name="user" value="'. santise($_POST["user"]) .'" class="form-control" autocomplete="off"></td></tr>
<tr><td>Heslo:</td><td><input type="password" name="pass" class="form-control" autocomplete="off"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Přihlásit se" class="btn btn-default"></td></tr>
</table></form>';

require "template/page.php";