<?php
	include_once("includes/common.include.php");
	
	
	$con = new MySQLConnection("127.0.0.1","php_scripts","botove","intech_forum");
	
	$fld = new DatabaseObject($con,"TEST",0,array("field1","field2","integer"));
	
	
?>