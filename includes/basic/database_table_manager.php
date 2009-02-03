<?php

class database_manager{
	
	var $fieldnames;
	var $primary_key;
	var $table_name;
	var $feed_str;
// 	var $update = false;
	
	function database_manager($table_name,$primary_key,$resourse_string,$record_id=0){
		$this->load_resourse( $table_name, $primary_key, $resourse_string );
		$this->load_record( $record_id );
	}
	
	function load_resourse($table_name, $primary_key, $resourse_string)
	{
		$this->table_name = $table_name;
		$this->primary_key = $primary_key;
		$this->feed_str = $resourse_string;
		$this->fieldnames = array();
		$res_arr = array_merge( array($primary_key), explode(";",$resourse_string) );
		foreach( $res_arr as $property ){
			$this->fieldnames[] = $property;
			$this->$property = "";
		}
	}
	
	function load_record( $record_id=0 )
	{
		global $sql_cls;
		$sql = &$sql_cls;
		
		$pk = $this->primary_key;
		$this->$pk = $sql->escape($record_id);
		if( !$record_id ) return;
		
		$sql->query = "SELECT `".implode("`, `",$this->fieldnames)."`
		FROM `{$this->table_name}`
		WHERE `$pk` = ".$this->$pk."
		LIMIT 1;";
		if ( !$sql->exec_sql() ) return;
		$row = $sql->fetch_row();
		
		foreach( $this->fieldnames as $key ){
			$this->$key = $row[$key];
		}

	}
	
	function del()
	{
		global $sql_cls;
		$sql = &$sql_cls;
		$pk = $this->primary_key;
		if( !$this->$pk ) return;
		
		$sql->exec_sql("DELETE FROM `{$this->table_name}`
		WHERE `$pk` = ".$this->$pk." ;");
		
		return $this->$pk;
	}
	
	//
	//	returns the id of the saved record
	//
	function save()
	{
		global $sql_cls;
		$sql = &$sql_cls;
		$pk = $this->primary_key;
		
		$sql->exec_sql("SELECT `".$pk."` FROM `".$this->table_name."` 
				WHERE `".$pk."` = ".$sql->escape($this->$pk)." ;");

		$res = array();
		if( !$sql->fetch_row() )
		{
			$this->$pk = ( $this->$pk ) ? $this->$pk : "NULL";
			
			foreach( $this->fieldnames as $prperty ){
				$res[] = $sql->input_value($this->$prperty);
			}
			
			$sql->exec_sql("INSERT INTO
			`".$this->table_name."` (`".implode("`, `",$this->fieldnames)."`) VALUES 
			(".implode(", ",$res).") ;");
			
// 			print $sql->query . "<br />";
			
			$sql->exec_sql("SELECT LAST_INSERT_ID() as lid ;");
			$row = $sql->fetch_row();
					
// 			$this->$pk = $sql->last_insert_id;
			$this->$pk = $row['lid'];
		}
		else
		{
			foreach( $this->fieldnames as $prperty ){
				$res[] = "`".$prperty."` = '".$sql->escape($this->$prperty)."'";
			}
			
			$sql->exec_sql("UPDATE `".$this->table_name."` SET
			". implode(", ",$res)."
			WHERE `".$pk."` = ".$sql->escape($this->$pk)." ;");
			
		}
		

		return $this->$pk;
	}

	//
	//	Returns the id of the new row
	//
	function cpy()
	{
		$pk = $this->primary_key;
		$old_id = $this->$pk;
		$this->$pk = 0;
		
		// saved!
		$new_id = $this->save();
		
		// getting them back
		$this->$pk = $old_id;
		return $new_id;
	}
	
	//
	//	array of database objects :)
	//	
	//
	function getArrayBy( $where = "", $order_by = "", $limit = "" ){
		global $sql_cls;
		$sql = &$sql_cls;
		$pk = $this->primary_key;
		
		$arr = array();
		$ret = array();
		$sql->query = "SELECT `$pk` FROM `".$this->table_name."` ";
		
		if( !empty($where) and $where ) $sql->query .= "WHERE $where ";
		
		if( !empty($order_by) and $order_by ) $sql->query .= "ORDER BY $order_by ";
		
		if( !empty($limit) and $limit ) $sql->query .= "LIMIT $limit ";
		
		$sql->query .= ";";
		$sql->exec_sql();
		while( $row = $sql->fetch_row() ){
			$arr[] = $row[$pk];
		}
		
		$cls = isset($this->classname) ? $this->classname : self;
		foreach( $arr as $id ){
			$ret[] = new $cls($id);
		}
		
		return $ret;
	}
	
	
	function getRange( $from, $to ){
		$pk = $this->primary_key;
		return getArrayBy( "`$pk` >= $from AND `$pk` < $to" );
	}
	
};

?>