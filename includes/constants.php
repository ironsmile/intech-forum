<?php

$constants = array(
  "CONTENT_MESSAGE",
  "CONTENT_INDEX",
  "CONTENT_REGISTER",
  "CONTENT_LOGIN",
  );

for( $ii = 0, $count = count($constants); $ii < $count; $ii++ )
  define($constants[$ii], $ii);

unset($constants);

$contentTypeToFile = array(
	CONTENT_MESSAGE => 'error.php',
	CONTENT_INDEX => 'index.php',
  CONTENT_REGISTER => 'register.php',
  CONTENT_LOGIN => 'login.php',
);

define("MESSAGE_ERROR_SERVER", 1);
define("MESSAGE_ERROR_INVALID_REQUEST_SYNTAX", 2);
define("MESSAGE_ERROR_NONEXISTENT_REQUESTED_RESOURCE", 3);


define( "SITE_HTTP_ROOT", "http://d/forum_test/" );

define( "MODULES_DIR", INCLUDES_PATH."libs/" );

define( "TEXTILE_FILE", MODULES_DIR."textile/classTextile.php" );

define( "GRAVATAR_URL", "http://www.gravatar.com/avatar.php" );

define( "DEFAULT_USER_PIC", SITE_HTTP_ROOT."images/default_user_pic.png" );

// do not change once the forum is installed!
define( "HASH_SALT", "slonko pilotira samoleti slonko trenira 4ernite bareti" );

define( "ANTI_BOT_TXT_LEN", 6 );

?>
