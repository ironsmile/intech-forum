<?php

class language_handler{

	var $str;

	function language_handler( $string = "" ){
		$this->load_str( $string );
	}
	
	function load_str( $string ){
		$this->str = $string;
	}
	
	function translate($lang_id){
		$d_id = $this->get_dict_id();
		if ( $d_id == -1 ) return "";
		
		global $sql_cls;
		$sql = $sql_cls;
		
		$sql->query = "SELECT `text` FROM `langs_trans` WHERE `dict_id` = $d_id AND `lang_id` = $lang_id ;";
		$sql->exec_sql();
		return ( $row = $sql->fetch_row() ) ?  $row['text'] : $this->str;
	}
	
	function get_dict_id(){
		if ( !isset($this->str) or empty($this->str) ) return -1;
		
		global $sql_cls;
		$sql = $sql_cls;
		
		$sql->query = "SELECT `id` FROM `langs_dict` WHERE `text` = '".$sql->escape($this->str)."' ; ";
		$sql->exec_sql();
		if( $row = $sql->fetch_row() ){
			return $row['id'];
		}
		else{
			$sql->query = "INSERT INTO `langs_dict`( `text` ) VALUES ( '".$sql->escape($this->str)."' ) ; ";
			$sql->exec_sql();
			return $sql->last_insert_id;
		}
	}
};



/**
*
*	The main function. It will translate the string given!
*
*/
function __( $string ){
	$user_lang_id = get_user_language();
	if( get_option("default_language_id") == $user_lang_id ) return $string;
	
	$lng = new language_handler( $string );
	return $lng->translate( $user_lang_id );
}


?>