<?php

function var_info( $variable ){
	return (DEBUG_MODE) ? "<pre>".print_r($variable,true)."</pre>" : "" ;
}

function get_url($show_query = true, $show_port = false){
    $protocol = (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $port = ($show_port || $_SERVER['SERVER_PORT']!=80) ? ":{$_SERVER['SERVER_PORT']}" : '';
    $file = $_SERVER['SCRIPT_NAME'];
    $query = ( $show_query and $_SERVER['QUERY_STRING'] ) ? "?{$_SERVER['QUERY_STRING']}" : '';
    return $protocol.$host.$port.$file.$query;
}

function get_option( $opt_name ){
	global $sql_fnc;
	$sql = $sql_fnc;
	
	$sql->query = "SELECT `option_value` as ov FROM `options` WHERE `option_name` = '$opt_name' ;";
	$sql->exec_sql();
	return ( $row = $sql->fetch_row() ) ? $row['ov'] : "" ;
}

function full_date( $time = "" ){
	if( $time == "" ) $time = time();
	return date(full_time_format(),$time);
}

function full_time_format(){
	return get_option("time_format")." ".get_option("date_format");
}

if( !function_exists("_") ):
function _( $str ){ return $str; }
endif;

function hash_string($string){
	return md5(HASH_SALT.$string);
}

function anti_bot_str($input){
	return substr(hash_string($input),0,ANTI_BOT_TXT_LEN);
}

function h($s){ return htmlspecialchars($s); }

function is_logged(){
	return isset($_SESSION['user_id']) and is_numeric($_SESSION['user_id']);
}

	

?>