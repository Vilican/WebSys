<?php

if (!$act) {
	
	if (has_access("posts_delete_permanent")) {
		$del_perm = ' <button type="button" class="btn btn-danger" id="deletepostperm">Trvale smazat</button>';
	}
	
	echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Smazání</h4></div><div class="modal-body">
<p>Opravdu chcete smazat tento příspěvek?</p></div><div class="modal-footer"><button type="button" id="stop" class="btn btn-primary">Zachovat</button>
<button type="button" class="btn btn-danger" id="deletepost">Smazat</button>'. $del_perm .'<input type="hidden" id="target" value="'. $_POST["post"] .'"></div></div></div>';
	
	exit;
	
}

if (!validate_csrf($_POST["csrf"])) {
	exit;
}

if ($_POST["permanent"] == 1 and has_access("posts_delete_permanent")) {
	
	$mysql->query("DELETE FROM `posts` WHERE `post_id` = ". $mysql->quote($_POST["post"]) .";");
	
} else {

	$mysql->query("UPDATE `posts` SET `deleted` = '1', `deleted_by` = '". $_SESSION["id"] ."' WHERE `post_id` = ". $mysql->quote($_POST["post"]) ."");

}

echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Smazání</h4></div><div class="modal-body">
<p>Příspěvek byl odstraněn</p></div><div class="modal-footer"><button type="button" id="stopreload" class="btn btn-primary">OK</button></div></div></div>';