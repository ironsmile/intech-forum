<?php
	define( "DEBUG_MODE", true );
	
	$err_type = ( DEBUG_MODE ) ? E_ALL : 0 ;
	error_reporting( $err_type );
	
	define( "SITE_ROOT", realpath(dirname(__FILE__)."/..")."/" );
	
	if( !defined( "INCLUDES_PATH" ) )
		define( "INCLUDES_PATH", realpath(dirname(__FILE__))."/");
	
	$includes = array(
		"db_settings.php",
		"config.php",
		"basic/sqlclass.php",
		"basic/SQLConnection.php",
		"basic/Model.php",
		"basic/common_funcs.php",
		"basic/template_class.php",
		"basic/database_table_manager.php",
		"basic/class_factory.php",
// 		"basic/translator.php",
		"basic/select_class.php",
// 		"classes/comment_form_renderer.php",
	);
	
	foreach ( $includes as $inc_file )
		if( is_file( INCLUDES_PATH.$inc_file ) )
			include_once( INCLUDES_PATH.$inc_file );
		elseif( DEBUG_MODE )
			print "File not found: ".INCLUDES_PATH.$inc_file."<br />";
			
	unset($includes,$inc_file);
	
	$sql = get_sql_object($MySQLhost,$MySQLuser,$MySQLpass,$MySQLdatabase);

	// specially reserved for use in functions and classes:
	$sql_fnc = get_sql_object($MySQLhost,$MySQLuser,$MySQLpass,$MySQLdatabase);
	$sql_cls = get_sql_object($MySQLhost,$MySQLuser,$MySQLpass,$MySQLdatabase);
	
// 	create_classes_for_all_tables(); //class for all databases with primary keys
// 	create_database_classes(); // creates classes specified in class_factory table from the database
	create_database_models();
	
	session_start();
?>
