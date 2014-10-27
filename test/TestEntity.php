<?php

class TestEntityX extends Entity {
	protected function getType() {
		return 'testdummy';
	}

	public function getMoney() { return $this->getData('money'); }
	public function setMoney($value) { $this->setData('money', $value); }
}

class TestEntity {
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

	public function saveAndLoad() {
		$item = new TestEntityX();
		$item->setMoney(9000);
		$item->save();
		if ($item->isNew()) return 'item should not new';

		$clone = new TestEntityX($item->getId());
		if ($clone->getMoney() != 9000) return 'clone should have money';
	}
}