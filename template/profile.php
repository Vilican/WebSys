<?php

$page["title"] = "Profil";

$page["content"] = '<div class="col-md-4 col-lg-2"><img src="'. displayAvatar($usr["id"]) .'" class="avatar img-responsive" alt="Profilový obrázek"></div>
<div class="col-md-8 col-lg-10"><table style="border-spacing:10px">
<tr><td>Uživatel:</td><td>'. id_to_user($usr["id"]) .'</td></tr>
<tr><td>Role:</td><td>'. $usr["rolename"] .'</a></td></tr>
<tr><td>Email:</td><td><a href="mailto:'. $usr["email"] .'">'. $usr["email"] .'</a></td></tr>
'. $next_fields .'</table></div>';

require "template/page.php";