<?php

define("USER_ADMIN",0);
define("USER_MODERATOR",1);
define("USER_NORMAL",2);

function is_admin( $user_type ){
	return $user_type == USER_ADMIN;
}

?>
