<?php

$num = $mysql->query("SELECT `id` FROM `flags` WHERE `user` = ". $_SESSION["id"] ." AND `post` = ". $mysql->quote($_POST["post"]) .";")->num_rows;

if (!$act) {
	
	if ($num == 0) {
		
		echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Nahlášení</h4></div><div class="modal-body">
<p>Opravdu chcete nahlásit tento příspěvek?</p></div><div class="modal-footer"><button type="button" id="stop" class="btn btn-primary">Zrušit</button>
<button type="button" class="btn btn-warning" id="flagpost">Nahlásit</button><input type="hidden" id="target" value="'. $_POST["post"] .'"></div></div></div>';
		exit;

	}
	
	echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Nahlášení</h4></div><div class="modal-body">
<p>Tento příspěvek byl již nahlášen</p></div><div class="modal-footer"><button type="button" id="stop" class="btn btn-primary">OK</button></div></div></div>';
	exit;
	
}

if ($num == 0) {

	$mysql->query("INSERT INTO `flags` (`user`, `post`) VALUES ('". $_SESSION["id"] ."', ". $mysql->quote($_POST["post"]) .");");

}

echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Nahlášení</h4></div><div class="modal-body">
<p>Příspěvek byl nahlášen</p></div><div class="modal-footer"><button type="button" id="stopreload" class="btn btn-primary">OK</button></div></div></div>';