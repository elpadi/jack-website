<?php
namespace Website\Shop;

use Functional as F;
use SquareConnect\Configuration;
use SquareConnect\Api\LocationsApi;
use SquareConnect\Api\CatalogApi;

class Square {

	protected $location;

	public static function getCatalog() {
		$catalog = new SquareCatalog();
		$catalog->hydrateFromApi(static::fetchCatalog());
		return $catalog;
	}
	
	protected static function fetchCatalog() {
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

	protected static function fetchLocation() {
		$cache = JACK_DIR.'/cache/square/location.json';
		if (is_readable($cache) && ($location = json_decode(file_get_contents($cache)))) {
			return $location;
		}
		$locations_api = new LocationsApi();
		$locations = $locations_api->listLocations()->getLocations();
		file_put_contents($cache, (string)($locations[0]));
		return $locations[0];
	}

	protected static function configure() {
		$access_token = getenv('SQUARE_ACCESS_TOKEN');
		$app_id = getenv('SQUARE_APP_ID');
		if (!$access_token || !$app_id) {
			throw new \Exception("Missing square configuration options.");
		}
		Configuration::getDefaultConfiguration()->setAccessToken($access_token);
	}

	public static function key($itemId, $variantId) {
		return "$itemId $variantId";
	}

	protected function __construct() {
		static::configure();
	}

	public static function instance() {
		$class = static::getClass();
		return new $class();
	}

	protected static function getClass() {
		return __CLASS__;
	}

}
