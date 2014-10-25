<?php

class TestDb {
	public function __construct() {
		Db::init(array(
			'dsn' => 'sqlite:aduh.db',
			'user' => '', 'pass' => '',
			'table' => 'eav',
		));
	}

	public function __destruct() {
		Db::close();
		unlink('aduh.db');
	}

	public function insertAttributeInserted() {
		$db = Db::getInstance();
		$prevCount = count($db->searchAttribute());

		$db->insertAttribute(100, 'Abrakadabra', 'Lampu', 'Hijau');
		$nowCount = count($db->searchAttribute());
		if ($nowCount != $prevCount+1) return false;
	}

	public function updateAttributeUpdated() {
		$db = Db::getInstance();
		$id = $db->generateId();
		$db->updateAttribute($id, 200, 'Lorem', 'Ipsum', 'Dolor Sit Amet');

		$attrib = $db->getAttribute($id);
		if ($attrib->type != 'Lorem') return false;
	}
}