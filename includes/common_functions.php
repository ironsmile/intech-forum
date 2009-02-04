<?php
require_once 'Model.php';

function getForums($databaseConnection, $forUpdate = false) {
	$result = array();
	
	$forumsRows = $databaseConnection->selectAndLock("* FROM forums ORDER BY id", $forUpdate);
	while ($forumRow = $databaseConnection->fetchRow($forumsRows)) {
		$result[] = new Forum($databaseConnection, $forumRow['id'], $forUpdate, $forumRow);
	}
	
	return $result;
}

function redirect($location) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: $location");
  exit("redirected");
}

function get_url($show_query = true, $show_port = false){
    $protocol = (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $port = ($show_port || $_SERVER['SERVER_PORT']!=80) ? ":{$_SERVER['SERVER_PORT']}" : '';
    $file = $_SERVER['SCRIPT_NAME'];
    $query = ( $show_query and $_SERVER['QUERY_STRING'] ) ? "?{$_SERVER['QUERY_STRING']}" : '';
    return $protocol.$host.$port.$file.$query;
}

function hash_string($string){
  return md5(HASH_SALT.$string);
}

function anti_bot_str($input){
  return substr(hash_string($input),0,ANTI_BOT_TXT_LEN);
}

function h($s){ return htmlspecialchars($s); }

function user_logged(){
  return isset($_SESSION['user_id']) and is_numeric($_SESSION['user_id']);
}

function get_gravatar_src($email, $size = 100){
  return GRAVATAR_URL."?gravatar_id=".md5( strtolower($email) ).
  "&default=".urlencode(DEFAULT_USER_PIC)."&size=".$size;
}

?>
