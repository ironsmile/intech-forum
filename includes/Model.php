<?php
require_once 'SQLConnection.php';

class NonexistentDatabaseEntryException extends InvalidArgumentException {
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}

class ReadOnlyDatabaseFieldException extends InvalidArgumentException {
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}

class NonexistentDatabaseFieldException extends InvalidArgumentException {
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}

class DatabaseObject {
	protected $databaseConnection;
	protected $table;
	protected $id;
	private $fieldToData;
	private $stringFields;
	private $updateableFields;
	private $updatedFields;
	protected $forUpdate;
	
	private function getDataInDatabaseFormat($field) {
		if (in_array($field, $this->stringFields)) {
			return getStringInDatabaseFormat($this->fieldToData[$field]);
		} else {
			return $this->fieldToData[$field];
		}
	}
	
	protected static function getStringInDatabaseFormat($string) {
		return "'" . $this->databaseConnection->escapeString($string) . "'";
	}

	public function __construct($databaseConnection, $table, $id, $updateableFields, $forUpdate = false, $fieldToData = null) {
		$this->databaseConnection = $databaseConnection;
		$this->table = $table;
		$this->id = $id;
		if (is_string($this->id)) {
			$this->id = "'" . $databaseConnection->escapeString($this->id) . "'";
		}
		$this->updateableFields = $updateableFields;
		$this->updatedFields = array();
		$this->forUpdate = $forUpdate;

		if (empty($fieldToData)) {
			$this->fieldToData = $databaseConnection->fetchRow($databaseConnection->selectAndLock("* FROM $this->table WHERE id = $this->id"), $this->forUpdate);
			
			if (empty($this->fieldToData)) {
				throw new NonexistentDatabaseEntryException("Nonexistent entry: table: $table, id: $id");
			}
		} else {
			$this->fieldToData = $fieldToData;
		}
		
		foreach ($this->fieldToData as $field => $data) {
			if (is_string($data)) {
				$this->stringFields[] = $field; 
			}
		}
		
		foreach ($this->updateableFields as $updateableField) {
			if (!array_key_exists($updateableField, $this->fieldToData)) {
				throw new NonexistentDatabaseFieldException();
			}
		}
	}
	
	public function __set($name, $value) {
		if (!array_key_exists($name, $this->fieldToData)) {
			throw new NonexistentDatabaseFieldException();
		} else if (!$this->forUpdate || !in_array($name, $this->updateableFields)) {
			throw new ReadOnlyDatabaseFieldException();
		}

		$this->fieldToData[$name] = $value;
		if (!in_array($name, $this->updatedFields)) {
			$this->updatedFields[] = $name;
		}
	}
	
	public function __get($name) {
		if (!array_key_exists($name, $this->fieldToData)) {
			throw new NonexistentDatabaseFieldException();
		}

		return $this->fieldToData[$name];
	}
	
	public function __isset($name) {
		return isset($this->fieldToData[$name]);
	}
	
	public function __unset($name) {
		throw new LogicException();
	}
	
	public function delete() {
		if (!$this->forUpdate) {
			throw new LogicException();
		}
		$this->$databaseConnection->sendQuery("DELETE FROM $this->table WHERE id = $id;");
	}
	
	public function save() {
		if (!$this->forUpdate) {
			throw new LogicException();
		}
		if (empty($this->updatedFields)) {
			return;
		}

		$updates = array();
		foreach ($this->updatedFields as $updatedField) {
			$updates[] = $updatedField . " = " . $this->getDataInDatabaseFormat($updatedField);
		}
		
		$this->$databaseConnection->sendQuery("UPDATE $this->table SET " . implode(", ", $updates) . "WHERE id = $this->$id;");
	}
	
	public static function getInsertStatement($table, $fieldToData, $stringFields) {
		foreach($fieldToData as $field => $data) {
			if (in_array($field, $stringFields)) {
				$fieldToData[$field] = DatabaseObject::getStringInDatabaseFormat($data);
			}
		}
		$databaseConnection->sendQuery("INSERT INTO forums (name, description) VALUES (." . $this->getStringInDatabaseFormat($name) . ", " . $this->getStringInDatabaseFormat($description). ");");
		return "INSERT INTO $table (" . implode(", ", array_keys($fieldToData)) . ") VALUES (" . implode(", ", array_values($fieldToData)) . ");";
	}
}

class Forum extends DatabaseObject {
	private $numberOfTopics;
	private $numberOfPosts;
	private $numberOfPages;
	private $pageToTopics;
	private $lastPost;

	public function __construct($databaseConnection, $id, $forUpdate = false, $fieldToData = null) {
		parent::__construct($databaseConnection, 'forums', $id, array('name', 'description'), $forUpdate, $fieldToData);
		$this->pageToTopics = array();
	}
	
	public static function create($databaseConnection, $name, $description, $forUpdate = false) {
		$fieldToData = array(
			'name' => $name,
			'description' => $description,
		);
		$databaseConnection->sendQuery(DatabaseObject::getInsertStatement('forums', $fieldToData, array('name', 'description')));
		return new Forum($databaseConnection, $databaseConnection->getLastInsertID(), $forUpdate);
	}
	
	public function getNumberOfTopics() {
		if (!isset($this->numberOfTopics)) {
			$res = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM topics WHERE forum_id = $this->id"));
			$this->numberOfTopics = $res[0];
		}
		
		return $this->numberOfTopics;
	}
	
	public function getNumberOfPosts() {
		if (!isset($this->numberOfPosts)) {
			$res = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM posts WHERE forum_id = $this->id"));
			$this->numberOfPosts = $res[0];
		}
		
		return $this->numberOfPosts;
	}
	
	public function getNumberOfPages($topicsPerPage) {
		if (!isset($this->numberOfPages)) {
			$numberOfTopicsRow = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM topics WHERE forum_id = $this->id"));
			$this->numberOfPages = ceil($numberOfTopicsRow[0] / ((float) $topicsPerPage)); 
		}
		
		return $this->numberOfPages;
	}
	
	public function getPageLink($page) {
		return "viewforum.php?forumid=$this->id&page=$page";
	}
	
	public function getPostsOnPage($page, $topicsPerPage, $forUpdate = false) {
		if (!isset($this->pageToTopics[$page])) {
			$this->pageToTopics[$page] = array();
			$topicsRows = $this->databaseConnection->selectAndLock("* FROM topics WHERE forum_id = $this->id ORDER BY last_updated, id DESC LIMIT $topicsPerPage OFFSET" . ($page - 1) * $topicsPerPage . ";");
			while ($topicRow = $this->databaseConnection->fetchRow($topicsRows)) {
				$this->pageToTopics[$page][] = new Topic($databaseConnection, $topicRow['id'], $forUpdate, $topicRow);
			}
			if (empty($this->pageToTopics[$page])) {
				throw new NonexistentDatabaseEntryException();
			}
		}
		
		return $this->pageToPosts[$page];
	}
	
	public function getLastPost() {
		if (!isset($this->lastPost)) {
			$lastPostRow = $this->databaseConnection->fetchRow($this->databaseConnection->selectAndLock("* FROM posts WHERE forum_id = $this->id ORDER BY posted_time, id DESC LIMIT 1"));
			if (empty($lastPosRow)) {
				$this->lastPost = FALSE;
			} else {
				$this->lastPost = new Post($this->databaseConnection, $lastPostRow['id']);
			}
		}
		
		return $this->lastPost;
	}
	
	public function getLink() {
		return "viewforum.php?forumid=$this->id";
	}
}

class Topic extends DatabaseObject {
	private $numberOfPosts;
	private $numberOfPages;
	private $pageToPosts;

	public function __construct($databaseConnection, $id, $forUpdate = false, $fieldToData = null) {
		parent::__construct($databaseConnection, 'topics', $id, array('title', 'views_count'), $forUpdate, $fieldToData);
		$this->pageToPosts = array();
	}
	
	public static function create($databaseConnection, $forum, $poster, $title, $forUpdate = false) {
		$fieldToData = array(
			'forum_id' => $forum->id,
			'poster_id' => $poster->id,
			'title' => $title,
		);
		$databaseConnection->sendQuery(DatabaseObject::getInsertStatement('topics', $fieldToData, array('title')));
		return new User($databaseConnection, $databaseConnection->getLastInsertID(), $forUpdate);
	}
	
	public function getForum() {
		return new Forum($this->databaseConnection, $this->forum_id);
	}
	
	public function getNumberOfPosts() {
		if (!isset($this->numberOfPosts)) {
			$res = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM posts WHERE topic_id = $this->id"));
			$this->numberOfPosts = $res[0];
		}
		
		return $this->numberOfPosts;
	}
	
	public function getNumberOfPages($postsPerPage) {
		if (!isset($this->numberOfPages)) {
			$numberOfPostsRow = $this->databaseConnection->fetchRowFields($this->databaseConnection->selectAndLock("COUNT(*) FROM posts WHERE topic_id = $this->id"));
			$this->numberOfPages = ceil($numberOfPostsRow[0] / ((float) $postsPerPage)); 
		}
		
		return $this->numberOfPages;
	}
	
	public function getPageLink($page) {
		return "viewtopic.php?topicid=$this->id&page=$page";
	}
	
	public function getPostsOnPage($page, $postsPerPage, $forUpdate = false) {
		if (!isset($this->pageToPosts[$page])) {
			$this->pageToPosts[$page] = array();
			$postsRows = $this->databaseConnection->selectAndLock("* FROM posts WHERE topic_id = $this->id ORDER BY posted_time, id LIMIT $postsPerPage OFFSET" . ($page - 1) * $postsPerPage . ";");
			while ($postRow = $this->databaseConnection->fetchRow($postsRows)) {
				$this->pageToPosts[$page][] = new Post($databaseConnection, $postRow['id'], $forUpdate, $postRow);
			}
			if (empty($this->pageToPosts[$page])) {
				throw new NonexistentDatabaseEntryException();
			}
		}
		
		return $this->pageToPosts[$page];
	}
	
	public function getLink() {
		return "viewtopic.php?topicid=$this->id";
	}
}

class Post extends DatabaseObject {
	private $isFirst;

	public function __construct($databaseConnection, $id, $forUpdate = false, $fieldToData = null) {
		parent::__construct($databaseConnection, 'posts', $id, array('content', 'last_updated_time'), $forUpdate, $fieldToData);
	}
	
	public static function create($databaseConnection, $topic, $poster, $content, $forUpdate = false) {
		$forum = new Forum($databaseConnection, $topic->forum_id);
		$fieldToData = array(
			'forum_id' => $forum->id,
			'topic_id' => $topic->id,
			'poster_id' => $poster->id,
			'content' => $content,
			'posted_time' => "now()",
			'last_updated_time' => "now()",
		);
		$databaseConnection->sendQuery(DatabaseObject::getInsertStatement('posts', $fieldToData, array('content')));
		return new User($databaseConnection, $databaseConnection->getLastInsertID(), $forUpdate);
	}
	
	public function getForum() {
		return $this->getTopic()->getForum();
	}
	
	public function getTopic() {
		return new Topic($this->databaseConnection, $this->topic_id);
	}
	
	public function isFirstInTheTopic() {
		if (!isset($this->isFirst)) {
			$firstRow = $databaseConnection->fetchRow($databaseConnection->selectAndLock("* FROM posts WHERE topic_id = $this->topic_id ORDER BY posted_time, id LIMIT 1"));
			$this->isFirst = $firstRow['id'] == $this->id;
		}
		
		return $this->isFirst;
	}
	
	public function getLink($postsPerPage) {
		$postsRows = $this->databaseConnection->selectAndLock("id FROM posts ORDER BY posted_time, id");
		$postNumber = 0;
		while ($postRow = $this->databaseConnection->fetchRow($postRows)) {
			$postNumber++;
			if ($postRow['id'] == $this->id) {
				break;
			}
		}
		
		$page = ceil($postNumber / float($postsPerPage));
		
		return "viewtopic.php?topicid=$this->id&page=$page#post$this->id";
	}
}

class User extends DatabaseObject {
	const USER_TYPE = 1;
	const MODERATOR_TYPE = 2;
	const ADMIN_TYPE = 3;

	public function __construct($databaseConnection, $id, $forUpdate = false, $fieldToData = null) {
		parent::__construct($databaseConnection, 'users', $id, array('email', 'type', 'avatar'), $forUpdate, $fieldToData);
	}
	
	public static function exists($databaseConnection, $name) {
		return $databaseConnection->selectAndLock("* FROM users WHERE name = " . DatabaseObject::getStringInDatabaseFormat($name) . ";") !== FALSE;
	}
	
	public function hasModaratingAccessTo($post) {
		return $this->type == User::ADMIN_TYPE || ($this->type == User::MODERATOR_TYPE && $this->databaseConnection->selectAndLock("* FROM moderators_rights WHERE user_id = $this->id AND forum_id = $post->forum_id;") !== FALSE);
	}
	
	public static function create($databaseConnection, $name, $email, $password, $type, $forUpdate = false) {
		$fieldToData = array(
			'name' => $name,
			'email' => $email,
			'type' => $type,
      'password' => $password,
		);
		$databaseConnection->sendQuery(DatabaseObject::getInsertStatement('users', $fieldToData, array('name', 'email','type','password')));
		return new User($databaseConnection, $databaseConnection->getLastInsertID(), $forUpdate);
	}
}

class InvalidRequestSyntaxException extends Exception {
	public function __construct($message = null, $code = 0) {
		parent::__construct($message, $code);
	}
}
?>
