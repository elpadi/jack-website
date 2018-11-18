<?php
namespace Website\Action;

use Jack\Action\Page;
use Website\Shop\Square\Store;

class Shop extends Page {

	public function ApiLocation($request, $response, $args) {
		Store::configure();
		$api = new \SquareConnect\Api\LocationsApi();
		var_dump($api->listLocations());
		exit(0);
	}

}
