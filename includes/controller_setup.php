<?php

  define( "DEBUG_MODE", true );
  $err_type = ( DEBUG_MODE ) ? E_ALL : 0 ;
  error_reporting( $err_type );
  
  define( "SITE_ROOT", realpath(dirname(__FILE__)."/..")."/" );
  if( !defined( "INCLUDES_PATH" ) ) define( "INCLUDES_PATH", realpath(dirname(__FILE__))."/");

  $includes = array(
    "constants.php",
    "SQLConnection.php",
    "Model.php",
    "Controller.php",
    "common_functions.php",
    "configuration.php",
  );
  
  foreach ( $includes as $inc_file )
    if( is_file( INCLUDES_PATH.$inc_file ) )
      include_once( INCLUDES_PATH.$inc_file );
    elseif( DEBUG_MODE )
      print "File not found: ".INCLUDES_PATH.$inc_file."<br />";
      
  unset($includes,$inc_file);
  
?>
