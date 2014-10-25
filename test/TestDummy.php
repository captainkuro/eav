<?php

class TestDummy {
	public function returnTrue() {
		return true;
	}

	public function returnFalse() {
		return false;
	}

	public function twoPlusTwoEqualsFour() {
		if (2 + 2 != 4) return false;
	}

	public function twoPlusTwoEqualsFive() {
		if (2 + 2 != 5) return false;
	}

	public function failWithMessage() {
		return "I want to fail";
	}
}
