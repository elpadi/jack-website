<?php
namespace Website\Action\ImageManager;

class List extends Index {

	protected function templatePath() {
		throw new \BadMethodCallException("This method must not be called.");
	}

	protected function fetchData($args) {
		return ['a'];
	}

}
