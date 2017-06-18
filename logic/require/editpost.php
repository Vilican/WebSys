<?php

if (!$act) {
	
	if (!empty($post["anon_author"])) {
		$name_form = 'Jméno: <input type="text" name="author" value="'. $post["anon_author"] .'"class="form-control"><br>';
	}
	
	echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Úprava</h4></div><div class="modal-body">
<p><form>'. $name_form .'Text: <textarea name="content" class="form-control">'. $post["content"] .'</textarea></form></p></div><div class="modal-footer">
<button type="button" id="stop" class="btn btn-primary">Zrušit</button><button type="button" class="btn btn-default" id="editpost">Upravit</button>
<input type="hidden" id="target" value="'. $_POST["post"] .'"></div></div></div>';
	
	exit;
	
}

if (!validate_csrf($_POST["csrf"])) {
	exit;
}

if (empty($_POST["content"])) {
	echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Úprava</h4></div><div class="modal-body">
<p>Příspěvek nemůže být prázdný</p></div><div class="modal-footer"><button type="button" id="stopreload" class="btn btn-primary">OK</button></div></div></div>';
	exit;
}

if (!empty($_POST["author"])) {
	
	$mysql->query("UPDATE `posts` SET `anon_author` = ". $mysql->quote(santise($_POST["author"])) .", `content` = ". $mysql->quote(santise($_POST["content"])) ." WHERE `post_id` = ". $mysql->quote($_POST["post"]) .";");
	
} else {
	
	$mysql->query("UPDATE `posts` SET `content` = ". $mysql->quote(santise($_POST["content"])) ." WHERE `post_id` = ". $mysql->quote($_POST["post"]) .";");
	
}

echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Úprava</h4></div><div class="modal-body">
<p>Příspěvek byl upraven</p></div><div class="modal-footer"><button type="button" id="stopreload" class="btn btn-primary">OK</button></div></div></div>';