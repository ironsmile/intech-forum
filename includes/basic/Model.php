<?php
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
	private $databaseConnection;
	private $table;
	private $id;
	private $fieldToData;
	private $stringFields;
	private $updateableFields;
	private $updatedFields;
	private $forUpdate;
	
	private function getDataInDatabaseFormat($field) {
		if (in_array($field, $this->stringFields)) {
			return "'" . $this->databaseConnection->escapeString($this->fieldToData[$field]) . "'";
		} else {
			return $this->fieldToData[$field];
		}
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
			$row;
			if ($forUpdate) {
				$row = $databaseConnection->selectForUpdate("* FROM $this->table WHERE id = $this->id");
			} else {
				$row = $databaseConnection->selectForShare("* FROM $this->table WHERE id = $this->id");
			}

			$this->fieldToData = $databaseConnection->fetchRow($row);
			
			if ($this->fieldToData === FALSE) {
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
		
		foreach ($updateableFields as $updateableField) {
			if (!in_array($updateableField, $this->fieldToData)) {
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
}
?>