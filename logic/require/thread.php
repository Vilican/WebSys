<?php

if (($_POST["act"] == "addprep" or $_POST["act"] == "addthread") and (($page["param2"] <= $_SESSION["level"] or $_SESSION["id"] == 0) and isset($_SESSION["id"]))) {

	if ($_POST["act"] == "addthread") {
		
		if (!validate_csrf($_POST["csrf"])) {
			exit;
		}
		
		$mysql->query("INSERT INTO `topics` (`name`, `location`, `user`) VALUES (". $mysql->quote($_POST["name"]) .", ". $mysql->quote($page["id"]) .", ". $mysql->quote($_SESSION["id"]) .");");
		
		echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Vytvoření</h4></div><div class="modal-body">
<p>Vlákno bylo přidáno</p></div><div class="modal-footer"><button type="button" id="stopreload" class="btn btn-primary">OK</button></div></div></div>';
		exit;
		
	}
	
	echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Vytvoření</h4></div><div class="modal-body">
<p><form>Název: <input type="text" name="name" class="form-control"></form></p></div><div class="modal-footer"><button type="button" id="stop" class="btn btn-primary">Zrušit</button>
<button type="button" class="btn btn-default" id="addthread">Přidat</button></div></div></div>';
	exit;

}

if (!is_numeric($_POST["thread"])) {
	exit;
}

$thread = $mysql->query("SELECT * FROM `topics` WHERE `location` = ". $mysql->quote($page["id"]) ." AND `deleted` = 0 AND `id` = ". $mysql->quote($_POST["thread"]) .";");

if ($thread->num_rows == 0) {
	exit;
}

$thread = $thread->fetch_assoc();

if (($_POST["act"] == "delprep" or $_POST["act"] == "delthread") and (has_access("thread_delete") or ($page["author"] == $_SESSION["id"] and isset($_SESSION["id"])) or ($thread["user"] == $_SESSION["id"] and isset($_SESSION["id"])))) {
	
	if ($_POST["act"] == "delthread") {
		
		if (!validate_csrf($_POST["csrf"])) {
			exit;
		}
		
		$mysql->query("UPDATE `topics` SET `deleted` = '1', `deleted_by` = '". $_SESSION["id"] ."' WHERE `id` = ". $mysql->quote($_POST["thread"]) .";");
		
		echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Smazání</h4></div><div class="modal-body">
<p>Vlákno bylo smazáno</p></div><div class="modal-footer"><button type="button" id="stopreload" class="btn btn-primary">OK</button></div></div></div>';
		exit;
		
	}
	
	echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Smazání</h4></div><div class="modal-body">
<p>Opravdu chcete smazat toto vlákno? Toto smaže i jeho příspěvky!</p></div><div class="modal-footer"><button type="button" id="stop" class="btn btn-primary">Zachovat</button>
<button type="button" class="btn btn-danger" id="deletethread">Smazat</button><input type="hidden" id="target" value="'. $_POST["thread"] .'"></div></div></div>';
	exit;
	
}

if (($_POST["act"] == "editprep" or $_POST["act"] == "editthread") and (has_access("thread_edit") or ($page["author"] == $_SESSION["id"] and isset($_SESSION["id"])) or ($thread["user"] == $_SESSION["id"] and isset($_SESSION["id"])))) {
	
	if ($_POST["act"] == "editthread") {
		
		if (!validate_csrf($_POST["csrf"])) {
			exit;
		}
		
		if (!has_access("thread_edit_moderator") and !($page["author"] == $_SESSION["id"] and isset($_SESSION["id"]))) {
			$mysql->query("UPDATE `topics` SET `name` = ". $mysql->quote($_POST["name"]) ." WHERE `id` = ". $mysql->quote($_POST["thread"]) .";");
		} else {
			if ($_POST["author"] !== "NULL") {
				$_POST["author"] = $mysql->quote($_POST["author"]);
			}
			$mysql->query("UPDATE `topics` SET `name` = ". $mysql->quote($_POST["name"]) .", `user` = ". $_POST["author"] ." WHERE `id` = ". $mysql->quote($_POST["thread"]) .";");
		}
		
		echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Editace</h4></div><div class="modal-body">
<p>Vlákno bylo upraveno</p></div><div class="modal-footer"><button type="button" id="stopreload" class="btn btn-primary">OK</button></div></div></div>';
		exit;
		
	}
	
	if (has_access("thread_edit_moderator") or ($page["author"] == $_SESSION["id"] and isset($_SESSION["id"]))) {
		
		$users = $mysql->query("SELECT `users`.`id`, `users`.`username` FROM `users` INNER JOIN `roles` ON `users`.`role` = `roles`.`role_id` WHERE `level` >= ". $mysql->quote($page["param2"]) .";");
		
		if ($users->num_rows > 0) {
			$user_list = '<option value="NULL">(bez moderátora)</option>';
			while ($user = $users->fetch_assoc()) {
				$select = null;
				if ($user["id"] == $thread["user"]) {
					$select = ' selected="selected"';
				}
				$user_list .= '<option value="'. $user["id"] .'"'. $select .'>'. $user["username"] .'</option>';
			}
		}
		
		$mod_form = '<br>Moderátor: <select name="author" class="form-control">'. $user_list .'</select>';
	}
	
	echo '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h4 class="modal-title">Editace</h4></div><div class="modal-body">
<p><form>Název: <input type="text" name="name" class="form-control" value="'. $thread["name"] .'">'. $mod_form .'</form></p></div><div class="modal-footer">
<button type="button" id="stop" class="btn btn-primary">Zrušit</button><button type="button" class="btn btn-default" id="editthread">Upravit</button>
<input type="hidden" id="target" value="'. $_POST["thread"] .'"></div></div></div>';
	exit;

}