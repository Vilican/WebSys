<?php

require "../config.php";

if (isset($_POST["install"])) {
	
	$conn = new mysqli(_SRV, _USR, _PW, _DB);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$commands = file_get_contents("websys.sql");   
	$conn->multi_query($commands);
	
}

require "install_templ.php";