<?php
namespace Website\Action\ImageManager;

class Listing extends Index {

	protected function templatePath() {
		throw new \BadMethodCallException("This method must not be called.");
	}

	protected function fetchData($args) {
		$this->data = [
			'meta' => json_decode(file_get_contents(JACK_DIR.'/cache/image-meta.json')),
		];
	}

}
