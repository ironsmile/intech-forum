<?php
class SQLException extends Exception {
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}

interface SQLConnection {
	public function sendQuery($query);
	public function fetchRow($rows);
	public function fetchRowFields($rows);
	public function selectAndLock($select, $forUpdate = false);
	public function getLastInsertID();
	public function escapeString($string);
	public function close();
}

class MySQLConnection implements SQLConnection {
	private $linkID;

	public function __construct($server, $username, $password, $database) {
		$this->linkID = mysql_connect($server, $username, $password);
		if ($this->linkID === FALSE) {
			throw new SQLException(mysql_error());
		}
		if (!mysql_select_db($database, $this->linkID)) {
			throw new SQLException(mysql_error());
		}
	}

	public function sendQuery($query) {
		$result = mysql_query($query, $this->linkID);
		if ($result === FALSE) {
			throw new SQLException(mysql_error() . "\n Full Query: " . $query);
		}
		
		return $result;
	}

	public function fetchRow($rows) {
		return mysql_fetch_assoc($rows);
	}
	
	public function fetchRowFields($rows) {
		return mysql_fetch_row($rows);
	}
	
	public function selectAndLock($select, $forUpdate = false) {
		if ($forUpdate) {
			return $this->sendQuery("SELECT " . $select . " FOR UPDATE");
		} else {
			return $this->sendQuery("SELECT " . $select . " LOCK IN SHARE MODE");
		}
	}
	
	public function getLastInsertID() {
		return mysql_insert_id($this->linkID);
	}
	
	public function escapeString($string) {
		return mysql_real_escape_string($string, $this->linkID);
	}

	public function close() {
		if (!mysql_close($this->linkID)) {
			throw new SQLException(mysql_error());
		}
	}
}
?>
