<?php

abstract class Entity {
	private $db = null;

	private $id = null;
	private $values = array();
	private $attributeIds = array();

	abstract protected function getType();

	public function __construct($id = null) {
		$this->db = Db::getInstance();

		if ($id) {
			$attributes = $this->db->searchAttribute($id);
			foreach ($attributes as $attribute) {
				if ($attribute->attr == 'id') {
					$this->id = $id;
				} else {
					$this->values[$attribute->attr] = $attribute->val;
					$this->attributeIds[$attribute->attr] = $attribute->id;
				}
			}
		}
	}

	protected function getData($key) {
		return isset($this->values[$key]) ? $this->values[$key] : null;
	}

	protected function setData($key, $value) {
		$this->values[$key] = $value;
	}

	public function getId() {
		return $this->id;
	}

	public function isNew() {
		return $this->id === null;
	}

	public function save() {
		$type = $this->getType();

		if ($this->isNew()) {
			$id = $this->db->generateId();
			$this->id = $id;
			$this->db->updateAttribute($id, $id, $type, 'id', $id);
		} else {
			$id = $this->id;
		}

		$copyValues = $this->values;
		foreach ($this->attributeIds as $key => $aid) {
			$this->db->updateAttribute($aid, $id, $type, $key, $copyValues[$key]);
			unset($copyValues[$key]);
		}

		foreach ($copyValues as $key => $value) {
			$this->db->insertAttribute($id, $type, $key, $value);
			$this->attributeIds[$key] = $this->db->getLastId();
		}
	}

	public function delete() {
		$this->db->deleteAttribute($this->id);
		foreach ($this->attributeIds as $aid) {
			$this->db->deleteAttribute($aid);
		}
		$this->id = null;
	}
}