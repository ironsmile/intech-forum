<?php
/*
*
*	functions for building classes!
*	@Function create_classes_for_all_tables
*			creates classes for all databases with primary keys
*			extending the database_manager class
*	
*	@Function create_database_classes
*			Scans `class_factory` database table and
*			build classes corresponding to it extending
*			the database_manager class
*			
*	@Function create_database_models
*			Parses models.php and creates corresponding classes
*			extending database_manager
*/

function create_classes_for_all_tables(){ // skips tables without primary keys!
	global $sql_fnc,$MySQLdatabase;
	$sql = $sql_fnc;
	
	$output = "";
	
	$sql->query = "SHOW TABLES;";
	$sql->exec_sql();
	
	$tables = array();
	while( $row = $sql->fetch_row() ){
		$tables[] = $row['Tables_in_'.$MySQLdatabase];
	}

	foreach( $tables as $table ){
		$sql->query = "DESC $table";
		$sql->exec_sql();
		
		$pk = "";
		$fields = array();
		
		while( $row = $sql->fetch_row() ){
			if( $row['Key'] == "PRI" )
				$pk = $row['Field'];
			else
				$fields[] = $row['Field'];
		}
		
		if ( empty($pk) ) continue;
		
		$output .= get_eval_database_class_str($table,$table,$pk,implode(";",$fields));
	}
	
// 	var_info( $output );
	
	eval($output);
	
	return $tables;
}
	
	
//
//	Scans `class_factory` table and
//	build classes corresponding to it extending
//	the database_manager class
//
function create_database_classes(){
	global $sql_fnc;
	$sql = $sql_fnc;
	
	$sql->query = "SELECT `primary_key`, `fields`, `class_name`, `table_name` FROM `class_factory` ;";
	if( !$sql->exec_sql() ){
		print $sql->error();
	}
	
	$classes = array();
	
	$eval_str = "";
	while( $row = $sql->fetch_row() ){
		$classes[] = $row['class_name'];
		$eval_str .= get_eval_database_class_str($row['class_name'],$row['table_name'],$row['primary_key'],$row['fields']);
	}
	
	if( eval($eval_str) === false and DEBUG_MODE )
		print "eval error!:<br />\n$eval_str";

	return $classes;
}


// almost the same as the above one!
function create_database_models(){

	include_once(SPYC_FILE);
	if( !class_exists("Spyc") ){
		if( DEBUG_MODE ) print "Spyc file not found! <br />";
		return false;
	}
	
	$models = Spyc::YAMLLoad( INCLUDES_PATH.'models.yaml' );

	$classes = array();
	$eval_str = "";
	foreach( $models as $class_name => $args ){
		$classes[] = $class_name;
		$flds = implode(";",split(", ",$args['fields']));
		$eval_str .= get_eval_database_class_str($class_name,$args['table_name'],$args['primary_key'],$flds);
	}
	
	if( eval($eval_str) === false and DEBUG_MODE )
		print "eval error!:<pre>$eval_str</pre>";

	return $classes;
}

//
//	To be evaled :)
//
function get_eval_database_class_str( $class_name, $table_name, $primary_key, $fields ){
	return "
		class ".$class_name." extends database_manager{
		var \$classname = \"".$class_name."\";
			function ".$class_name."( \$record_id = 0 ){
				parent::database_manager('".$table_name."','".$primary_key."','".$fields."',\$record_id);
			}
		};
		";
}

?>
