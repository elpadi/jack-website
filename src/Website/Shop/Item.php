<?php
namespace Website\Shop;

class Item {

	protected $id;

	public function __construct(string $id) {
		$this->id = $id;
	}

}

