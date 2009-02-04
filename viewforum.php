<?php
require_once 'includes/controller_setup.php';

class ViewForumController implements Controller {
	public function execute($databaseConnection, $config, &$requestData) {
		$requestData['content'] = CONTENT_VIEW_FORUM;
		
		if (!isset($_GET['forumid']) || !ctype_digit($_GET['forumid'])) {
			throw new InvalidRequestSyntaxException();
		}

		$requestData['forum'] = new Forum($databaseConnection, (int) $_GET['forumid']);
		
		$requestData['page'] = 1;
		if (isset($_GET['page']) && ctype_digit($_GET['page'])) {
			$requestData['page'] = (int) $_GET['page'];
			if ($requestData['page'] < 1 || $requestData['page'] > $requestData['forum']->getNumberOfTopics) {
				redirect("viewforum.php?forumid=" . $requestData['forum']->id);
				unset($requestData['content']);
				return;
			}
		}
	}
}

$controller = new ViewForumController();

include 'includes/ControllerExecutor.php';
?>
