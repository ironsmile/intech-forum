<?php
require_once 'includes/controller_setup.php';

class IndexController implements Controller {
	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_INDEX;

		$requestData['forums'] = getForums($databaseConnection);
	}
}

$controller = new IndexController();

include 'includes/ControllerExecutor.php';
?>
