<?php

function show_page_fields($type, $pg, &$purifier) {
	
	switch ($type) {
		
		case 1:
			return null;
			
		case 2:
			return '<tr><td>Přístup psaní:</td><td><input type="text" name="access2" class="form-control" value="'. restore_value($pg["param1"], $purifier->purify($_POST["access2"])) .'"></td></tr>';
		
		case 3:
			return '<tr><td>Přístup psaní:</td><td><input type="text" name="access2" class="form-control" value="'. restore_value($pg["param1"], $purifier->purify($_POST["access2"])) .'"></td></tr>
<tr><td>Přístup vytváření:</td><td><input type="text" name="access3" class="form-control" value="'. restore_value($pg["param2"], $purifier->purify($_POST["access3"])) .'"></td></tr>';
	
	}
	
}

function validate_page_fields($type) {

	switch ($type) {
		
		case 3:
			if (!validate_length($_POST["access3"], 1, 4) or !is_numeric($_POST["access3"]) or $_POST["access3"] < 0) {
				$message .= 'Přístup musí obsahovat 1 až 4 kladná čísla!<br>';
			}
		
		case 2:
			if (!validate_length($_POST["access2"], 1, 4) or !is_numeric($_POST["access2"]) or $_POST["access2"] < 0) {
				$message .= 'Přístup musí obsahovat 1 až 4 kladná čísla!<br>';
			}	
		
	}
	
	return $message;

}

function mysql_page_fields($type, &$mysql) {
	
	switch ($type) {
		
		case 3:
			return ", `param1` = ". $mysql->quote($_POST["access2"]) .", `param2` = ". $mysql->quote($_POST["access3"]);	
		
		case 2:
			return ", `param1` = ". $mysql->quote($_POST["access2"]);	
		
		default:
			return null;
		
	}
	
}