<?php
namespace Website\Shop\Square;

use SquareConnect\Configuration;
use SquareConnect\Api\LocationsApi;
use SquareConnect\Api\CatalogApi;
use SquareConnect\Api\TransactionsApi;
use Website\Shop\Orders;

class Store {

	public static function configure() {
		$access_token = getenv('SQUARE_ACCESS_TOKEN');
		$app_id = getenv('SQUARE_APP_ID');
		if (!$access_token || !$app_id) {
			throw new \Exception("Missing square configuration options.");
		}
		Configuration::getDefaultConfiguration()->setAccessToken($access_token);
	}

	public static function fetchCatalogDataFromApi() {
		$cursor = NULL;
		static::configure();
		$api = new CatalogApi();
		$response = $api->listCatalog($cursor, implode(',', ['ITEM']));
		return $response->getObjects();
	}

	public static function fetchCatalogData() {
		$cache = JACK_DIR.'/cache/square/catalog.json';
		if (is_readable($cache) && ($catalog = json_decode(file_get_contents($cache)))) {
			return $catalog;
		}
		if (IS_LOCAL) throw new \BadMethodCallException("Could not fetch a functioning catalog.");
		$catalog = static::fetchCatalogDataFromApi();
		$json = sprintf('[%s]', implode(',', $catalog));
		file_put_contents($cache, $json);
		return json_decode($json);
	}

	public static function verifyTransaction(string $transactionId, string $referenceId) {
		if (IS_LOCAL) return [FALSE, 'Cannot verify an order from localhost.'];
		try {
			$response = static::retrieveTransaction($transactionId);
		}
		catch (\Exception $e) {
			return [FALSE, $e->getMessage()];
		}
		$total = 0;
		foreach ($response['transaction']['tenders'] as $t) {
			$total += $t['amount_money']['amount'];
			if ($t['type'] === 'CARD' && $t['card_details']['status'] !== 'CAPTURED') {
				return [FALSE, 'The credit card payment was not authorized.'];
			}
		}
		$order = Orders::getOne(['reference_id' => $referenceId]);
		if (!$order) {
				return [FALSE, 'The order ID does not match our records.'];
		}
		if ($total != $order->total_amount * 100) {
				return [FALSE, 'The amount charged does not match the order total.'];
		}
		return [TRUE, 'Order confirmed.'];
	}

	protected static function retrieveTransaction(string $transactionId) {
		Store::configure();
		$api = new TransactionsApi();
		return $api->retrieveTransaction(getenv('SQUARE_LOCATION_ID'), $transactionId);
	}

	public static function key($itemId, $variantId) {
		return "$itemId $variantId";
	}

}
