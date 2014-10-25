<?php

class Db {
	// Singleton pattern
	private static $instance = null;

	public static function init($config) {
		self::$instance = new self();
		self::$instance->pdo = new PDO($config['dsn'], $config['user'], $config['pass']);
		self::$instance->table = $config['table'];

		$sql = "CREATE TABLE IF NOT EXISTS {$config['table']} (
			id INTEGER NOT NULL CONSTRAINT pid PRIMARY KEY AUTOINCREMENT,
			entity INTEGER NOT NULL,
			type VARCHAR NOT NULL,
			attr VARCHAR NOT NULL,
			val TEXT NULL
		)";
		self::$instance->pdo->exec($sql);
	}

	public static function getInstance() {
		if (self::$instance === null) throw new Exception('Uninitiated');
		return self::$instance;
	}

	public static function close() {
		self::$instance = null;
	}

	private $pdo = null;
	private $table = null;

	private function __construct() {
	}

	// Attribute operations
	public function insertAttribute($entity, $type, $attribute, $value) {
		$sql = "INSERT INTO {$this->table} 
			(entity, type, attr, val) VALUES
			(:entity, :type, :attribute, :value)";
		$statement = $this->pdo->prepare($sql);
		$statement->execute(array(
			':entity' => $entity,
			':type' => $type,
			':attribute' => $attribute,
			':value' => $value,
		));
	}

	public function getLastId() {
		return $this->pdo->lastInsertId();
	}

	public function generateId() {
		$this->insertAttribute('', '', '', '');
		return $this->getLastId();
	}

	public function updateAttribute($id, $entity, $type, $attribute, $value) {
		$sql = "UPDATE {$this->table} SET 
			entity=:entity, type=:type,
			attr=:attribute, val=:value
			WHERE id=:id";
		$statement = $this->pdo->prepare($sql);
		$statement->execute(array(
			':id' => $id,
			':entity' => $entity,
			':type' => $type,
			':attribute' => $attribute,
			':value' => $value,
		));
	}

	public function getAttribute($id, $style = PDO::FETCH_OBJ) {
		$sql = "SELECT * FROM {$this->table}
			WHERE id=:id
			LIMIT 1";
		$statement = $this->pdo->prepare($sql);
		$statement->execute(array(
			':id' => $id,
		));
		return $statement->fetch($style);
	}

	public function deleteAttribute($id) {
		$sql = "DELETE FROM {$this->table}
			WHERE id=:id";
		$statement = $this->pdo->prepare($sql);
		$statement->execute(array(
			':id' => $id,
		));
	}

	public function searchAttribute(
		$entity = null, $type = null, $attribute = null, $value = null,
		$limit = null, $order = null, $style = PDO::FETCH_OBJ
	) {
		$sql = "SELECT * FROM {$this->table}";

		$conditions = array();
		$variables = array();
		if ($entity !== null) {
			$conditions[] = 'entity=:entity';
			$variables[':entity'] = $entity;
		}
		if ($type !== null) {
			$conditions[] = 'type=:type';
			$variables[':type'] = $type;
		}
		if ($attribute !== null) {
			$conditions[] = 'attribute=:attribute';
			$variables[':attribute'] = $attribute;
		}
		if ($value !== null) {
			$conditions[] = 'val=:value';
			$variables[':value'] = $value;
		}
		if (count($conditions) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $conditions);
		}

		if ($order !== null) {
			$sql .= " ORDER BY {$order}";
		}
		if ($limit !== null) {
			$sql .= " LIMIT {$limit}";
		}

		$statement = $this->pdo->prepare($sql);
		$statement->execute($variables);
		return $statement->fetchAll($style);
	}
}