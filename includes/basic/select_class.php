<?php


class select{

	var $CSS;
	var $table_name;
	var $value_field;
	var $text_field;
	var $where_clause;
	var $selected_value;
	var $insert_empty_value;
	var $empty_value;
	var $select_name;
	var $events = array();
	
	function select(){}

	function render(){
		global $sql_cls;
		$sql = $sql_cls;
	
		foreach( array(
				"table_name",
				"value_field",
				"text_field",
				"events", ) as $prop ){
			if( !isset($this->$prop) )
				$this->$prop = "";
		}
		
		$output = "<select name='".$this->select_name."'";
		
		if( !empty( $this->events ) ){
			foreach( $this->events as $event => $value ){
				if( strstr( $event, "js_" ) === false ) continue;
				$output .= " ".substr($event,3) . '="'.str_replace('"',"'",$value).'"';
			}
		}
		
		$output .=">";
		if( isset($this->insert_empty_value) and $this->insert_empty_value )
			$output .= "\n\t<option value='".$this->empty_value."'></option>";
		
		$sql->query = "SELECT `".$this->text_field."`,`".$this->value_field."`
		FROM `".$this->table_name."` ";
		if( isset($this->where_clause) and !empty($this->where_clause) ){
			$sql->query .= "WHERE ".$this->where_clause;
		}
		$sql->query .= " ;";
		$sql->exec_sql();
		
// 		print $sql->query;
		
		while( $row = $sql->fetch_row() ){
			$output .= "\n\t<option value='".$row[$this->value_field]."'>".$row[$this->text_field]."</option>";
		}
		
		if( isset( $this->selected_value ) ){
			$output = str_replace("value='".$this->selected_value."'","value='".$this->selected_value."' selected='1'", $output);
		}
		
		return $output."\n</select>";
	}
};


?>