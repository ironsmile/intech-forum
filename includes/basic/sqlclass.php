<?php
if ( !defined("_SQL_CLASS_LIB") ): // what about some C++ style :)
define("_SQL_CLASS_LIB",1);


class SQLinterface{

	var $_SQLhost;
	var $_SQLuser;
	var $_SQLpass;
	var $_SQLdatabase;
	var $_link;
	var $query;
	var $affected_rows;
	var $rows_count;
	var $last_insert_id;
	var $conn_type;
	var $conn_info;
	
	function SQLinterface($host,$user,$password,$database){
		$this->_SQLhost = $host;
		$this->_SQLuser = $user;
		$this->_SQLpass = $password;
		$this->_SQLdatabase=$database;
		$this->conn_info = "";
		$this->conn_type = "no connection";
		$this->_res = false;
		return $this;
	}
	
	function connect() {}
	function exec_sql($query) {
		if( isset($query) && !empty($query) && $query )
			$this->query = $query;
	}
	function fetch_row() {}
	function error(){
		return ( isset($this->err) ) ? $this->err : "";
	}
	
	function escape( $string ){
		switch( $this->conn_type ){
			case "mysql":
				return mysql_real_escape_string( $string, $this->_link );
			case "mysqli":
				return mysqli_real_escape_string($this->_link, $string );
			default:
				return addslashes($string);
		}
	}
	
	// prepares value for db input and adds slashesh when needed ( value becomes 'value' )
	function input_value( $value ){
		return ( is_numeric($value) or in_array( strtolower($value), array(
  						'now()', 'null', 'unix_timestamp()',
  							) )
					) ? $value : "'".$this->escape($value)."'" ;
	}
};

//
//	My interface to the
//	mysql extension
//	just basic stuff for everyday use
//
class MysqlCon extends SQLinterface{
	
	function MysqlCon($host,$user,$password,$database){
		parent::SQLinterface($host,$user,$password,$database);
		return $this;
	}
	
	function connect(){
		$flag = true;
		if ( $this->_link = mysql_connect($this->_SQLhost,$this->_SQLuser,$this->_SQLpass) ){
			if (!mysql_select_db( $this->_SQLdatabase, $this->_link ))
				$flag = false;
		}
		else $flag = false;
		
		//
		//	for security...
		//
		unset($this->_SQLpass);
		
		if( !$flag ){
			$this->err = mysql_error();
			return false;
		}
		
		$this->conn_info = "MySQL -> " . mysql_get_host_info($this->_link);
		$this->conn_type = "mysql";
		
		return true;
	}
	
	function exec_sql($query = false){
		parent::exec_sql( $query );
		if( !isset($this->query) or empty($this->query) or !$this->_link ) return false;
		$this->_res = mysql_query($this->query, $this->_link);
		$this->affected_rows = mysql_affected_rows( $this->_link );
		$this->rows_count = ($this->_res !== false and $this->_res !== true) ? mysql_num_rows( $this->_res ) : 0;
		$this->last_insert_id = mysql_insert_id( $this->_link );
		if ($this->_res){
			return true;
		}
		else{
			$this->err = mysql_error()."\nSQL Query: ".$this->query;
			return false;
		}
	}
	
	function fetch_row(){
		if( !isset($this->_res) or !$this->_res ) return false;
		$this->row = mysql_fetch_assoc($this->_res);
		return $this->row;
	}
	
};

//
//	An other interface
//	This time for the mysqli
//
class MysqliCon extends SQLinterface{
	
	function MysqliCon($host,$user,$password,$database){
		parent::SQLinterface($host,$user,$password,$database);
		return $this;
	}

	function connect(){
		ob_start();
		$this->_link = new mysqli($this->_SQLhost,$this->_SQLuser,$this->_SQLpass,$this->_SQLdatabase);
		unset( $this->_SQLpass );
		if (mysqli_connect_error()) {
			$this->_link = false;
			$this->err = ob_get_contents();
			ob_end_clean();
			return false;
		}
		$this->conn_info = "MySQLi -> " . $this->_link->host_info;
		$this->conn_type = "mysqli";
		ob_end_clean();
		
		return true;
	}
	
	function exec_sql($query = false){
		parent::exec_sql( $query );
		if( !isset($this->query) or empty($this->query) or !$this->_link ) return false;
		if ( ! $this->_res = $this->_link->query($this->query) ) return false;
		$this->affected_rows = $this->_link->affected_rows;
		$this->rows_count = ($this->_res !== true) ? $this->_res->num_rows : 0 ;
		$this->last_insert_id = $this->_link->insert_id;
		return true;
	}
	
	function error(){
		if( $this->_link )
			return $this->_link->error;
		else
			return parent::error();
	}
	
	function fetch_row(){
		if( !isset($this->_res) or !$this->_res ) return false;
		$this->row = $this->_res->fetch_assoc();
		return $this->row;
	}
};


function get_sql_object($host,$user,$password,$database,$connection_encoding='utf8'){
	$aviable_types = array(
		"MysqliCon" => "mysqli_connect",
		"MysqlCon" => "mysql_connect",
	);
	
	foreach( $aviable_types as $class => $conn_funtion ){
		if( !function_exists( $conn_funtion ) ) continue;
		
		$sql = new $class($host,$user,$password,$database);
		if( !$sql->connect() ){
			if ( defined('DEBUG_MODE') and DEBUG_MODE )
				print $sql->error();
			return false;
		}
		
		if( $connection_encoding !== false ){
			$sql->exec_sql("SET NAMES '$connection_encoding' ;");
		}
		
		return $sql;
	}
	
	return false;
}

endif;
?>