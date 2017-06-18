<?php

class DB {

    protected static $connection;

    public function connect($server, $user, $pass, $db) {
		self::$connection = new mysqli($server, $user, $pass, $db);
        if(self::$connection->connect_error !== null) {
            return false;
        }
        return self::$connection;
    }

    public function query($query) {
        $result = self::$connection->query($query);
        return $result;
    }

    public function select($query) {
        $rows = array();
        $result = $this->query($query);
        if($result === false) {
            return false;
        }
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function error() {
        return self::$connection->error;
    }

    public function quote($value) {
        return "'" . self::$connection->real_escape_string($value) . "'";
    }
	
	public function insert_id() {
		return self::$connection->insert_id;
	}
	
	public function update_activity($user_id) {
		self::$connection->query("UPDATE `users` SET `lastact`= NOW() WHERE `id` = '". self::$connection->real_escape_string($user_id) ."';");
	}
	
}

function throw_error($error_message) {
	require "template/core_error.php";
	exit;
}

$mysql = new DB();
if (!$mysql->connect(_SRV, _USR, _PW, _DB)) {
	throw_error("Připojení k databázi se nepodařilo.<br>Prosím zkontrolujte nastavení v config.php");
}