<?php

class TestDb {
	public function __construct() {
		Db::init(array(
			'dsn' => 'sqlite::memory:',
			'user' => '', 'pass' => '',
			'table' => 'eav',
		));
	}

	public function __destruct() {
		Db::close();
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
		$db->updateAttribute($id, 150, 'Lorem', 'Ipsum', 'Dolor Sit Amet');

		$attrib = $db->getAttribute($id);
		if ($attrib->type != 'Lorem') return false;
	}

	public function searchAttributeCorrectResult() {
		$db = Db::getInstance();
		$db->insertAttribute(200, 'book', 'title', 'Amon');
		$db->insertAttribute(200, 'book', 'author', 'Dagon');
		$db->insertAttribute(201, 'book', 'title', 'Blood');
		$db->insertAttribute(201, 'book', 'author', 'Alucard');
		$db->insertAttribute(201, 'book', 'year', '1500');

		$result = $db->searchAttribute(200);
		if (count($result) !== 2) return false;
	}

	public function deleteAttributeDeleted() {
		$db = Db::getInstance();
		$db->insertAttribute(250, 'del', 'delthis', 'Please');
		$id = $db->getLastId();

		$prevCount = count($db->searchAttribute());
		$db->deleteAttribute($id);

		$nowCount = count($db->searchAttribute());
		if ($nowCount != $prevCount-1) return false;
	}
}