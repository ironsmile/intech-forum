<?php

$requestData = array();
$databaseRolledback = FALSE;

try {
	$databaseConnection = new MySQLConnection($config['databaseServer'], $config['databaseUsername'], $config['databasePassword'], $config['databaseName']);
	$databaseConnection->sendQuery("START TRANSACTION;");
	
	session_start();
	
	if (user_logged()) {
		$requestData['user'] = new User($databaseConnection,$_SESSION['user_id']);
	}

	$controller->execute($databaseConnection, $config, $requestData);
} catch (InvalidRequestSyntaxException $e) {
	header("HTTP/1.1 400 Bad Request");
	$requestData['content'] = CONTENT_MESSAGE;
	$requestData['message'] = MESSAGE_ERROR_INVALID_REQUEST_SYNTAX;
	$databaseConnection->sendQuery("ROLLBACK;");
	$databaseRolledback = TRUE;
} catch (NonexistentDatabaseFieldException $e) {
	header("HTTP/1.1 404 Not Found");
	$requestData['content'] = CONTENT_MESSAGE;
	$requestData['message'] = MESSAGE_ERROR_NONEXISTENT_REQUESTED_RESOURCE;
	$databaseConnection->sendQuery("ROLLBACK;");
	$databaseRolledback = TRUE;
} catch (Exception $e) {
	header("HTTP/1.1 500 Internal Server Error");
	$requestData['content'] = CONTENT_MESSAGE;
	$requestData['message'] = MESSAGE_ERROR_SERVER;
	$databaseConnection->sendQuery("ROLLBACK;");
	$databaseRolledback = TRUE;
}

if (isset($requestData['content'])) {
	include 'layout.php';
}
if (!$databaseRolledback) {
	$databaseConnection->sendQuery("COMMIT;");
}
$databaseConnection->close();

$_SESSION['back_to'] = get_url();

?>
