<?php

$page["title"] = "Registrace";

$page["content"] = $message . '
<form action="index.php?p=reg" method="post">
<table style="border-spacing: 10px">
<tr><td>Uživatel:</td><td><input type="text" name="user" class="form-control" value="'. $_POST["user"] .'"></td></tr>
<tr><td>Heslo:</td><td><input type="password" name="pass" class="form-control" autocomplete="off"></td></tr>
<tr><td>Heslo znovu:</td><td><input type="password" name="pass2" class="form-control" autocomplete="off"></td></tr>
<tr><td>Email:</td><td><input type="text" name="email" class="form-control" value="'. $_POST["email"] .'"></td></tr>
<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Registrovat" class="btn btn-default"></td></tr>
</table></form>';

require "template/page.php";

?>