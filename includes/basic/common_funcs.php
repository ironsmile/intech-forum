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

/*
*
*	every menu item is rendered by replacing
*		%title with the title of the menu and
*		%page with the id of the page it has to link
*	in the $seed_str
*	
*	If there's no menu link for the language it uses the default ( 1 )
*
*/
// function render_menu( $seed_str = "<a href='index.php?p=%page'>%title</a>" ){
// 	global $sql_fnc;
// 	$sql = $sql_fnc;
// 	
// 	$sql->query = "SELECT `id`, `title` FROM menu WHERE `parent_id` = 0 ;";
// 	$sql->exec_sql();
// 	
// 	$menus = array();
// 	
// 	while( $row = $sql->fetch_row() ){
// 		$menus[$row['id']] = $row['title'];
// 	}
// 	
// 	$output = "\n<!-- render_menu output:\nseed:$seed_str -->\n";
// 	
// 	foreach( $menus as $menu_id => $title ){
// 		$p_id = get_page_id_by_menu_id($menu_id);
// // 		if( empty($p_id) ) $p_id = get_page_id_by_menu_id($menu_id,get_option("default_language_id"));
// 		if( empty($p_id) ) continue;
// 		$output .= str_replace("%page",$p_id, str_replace("%title", __($title), $seed_str))."\n";
// 	}
// 	
// 	return $output;
// }

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



?>