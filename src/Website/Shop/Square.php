<?php
namespace Website\Shop;

use Functional as F;
use SquareConnect\Configuration;
use SquareConnect\Api\LocationsApi;
use SquareConnect\Api\CatalogApi;

class Square {

	protected $location;

	protected function __construct() {
		$this->configure();
		$this->location = $this->fetchLocation();
		$this->catalog = $this->fetchCatalog();
	}

	public function getIssueItem($number, $part) {
		$issue = F\first($this->catalog, function($item) use ($number, $part) {
			$title = $item->item_data->name;
			return strpos($title, "Issue #$number") !== FALSE && strpos($title, "Part $part") !== FALSE;
		});
		return $issue;
	}

	protected function fetchCatalog() {
		$cursor = NULL;
		$cache = JACK_DIR.'/cache/square/catalog.json';
		if (is_readable($cache) && ($catalog = json_decode(file_get_contents($cache)))) {
			return $catalog;
		}
		$api = new CatalogApi();
		$response = $api->listCatalog($cursor, implode(',', ['ITEM']));
		$catalog = $response->getObjects();
		file_put_contents($cache, sprintf('[%s]', implode(',', $catalog)));
		return $catalog;
	}

	protected function fetchLocation() {
		$cache = JACK_DIR.'/cache/square/location.json';
		if (is_readable($cache) && ($location = json_decode(file_get_contents($cache)))) {
			return $location;
		}
		$locations_api = new LocationsApi();
		$locations = $locations_api->listLocations()->getLocations();
		file_put_contents($cache, (string)($locations[0]));
		return $locations[0];
	}

	protected function configure() {
		$access_token = getenv('SQUARE_ACCESS_TOKEN');
		$app_id = getenv('SQUARE_APP_ID');
		if (!$access_token || !$app_id) {
			throw new \Exception("Missing square configuration options.");
		}
		Configuration::getDefaultConfiguration()->setAccessToken($access_token);
	}

	public static function instance() {
		$class = static::getClass();
		return new $class();
	}

	protected static function getClass() {
		return __CLASS__;
	}

}
