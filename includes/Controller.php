<?php
interface Controller {
	public function execute($databaseConnection, $config, &$requestData);
}
?>
