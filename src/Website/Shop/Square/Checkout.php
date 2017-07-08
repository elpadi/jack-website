<?php
namespace Website\Shop\Square;

use SquareConnect\Model\CreateOrderRequestLineItem;
use SquareConnect\Model\Money;
use SquareConnect\Model\CreateOrderRequest;
use SquareConnect\Model\CreateCheckoutRequest;
use SquareConnect\Api\CheckoutApi;
use Website\App;
use Website\Shop\Orders;

class Checkout {

	public function __construct($storage, $cart) {
		$this->storage = $storage;
		$this->cart = $cart;
	}

	protected static function generateId() {
		$bytes = random_bytes(16);
		return bin2hex($bytes);
	}

	protected function variantsToLineItems() {
		$lineItems = [];
		foreach ($this->cart->getItems() as $variant) {
			$lineItem = new CreateOrderRequestLineItem();
			$lineItem['name'] = $variant->getItemTitle();
			$lineItem['quantity'] = (string)$variant->getCartCount();
			$lineItem['base_price_money'] = new Money(['amount' => $variant->getPrice() * 100, 'currency' => 'USD']);
			$lineItems[] = $lineItem;
		}
		return $lineItems;
	}

	protected function shippingToLineItem() {
		$lineItem = new CreateOrderRequestLineItem();
		$lineItem['name'] = 'Shipping';
		$lineItem['quantity'] = '1';
		$lineItem['base_price_money'] = new Money(['amount' => $this->cart->getShipping() * 100, 'currency' => 'USD']);
		return $lineItem;
	}

	protected function createOrder() {
		$order = new CreateOrderRequest();
		$order['reference_id'] = $this->getReferenceId();
		$lineItems = $this->variantsToLineItems();
		$lineItems[] = $this->shippingToLineItem();
		$order['line_items'] = $lineItems;
		return $order;
	}

	public function whenLastUpdated() {
		return ($time = $this->storage->get('lastUpdateTimestamp')) ? $time : 0;
	}

	protected function getReferenceId() {
		if ($id = $this->storage->get('reference_id')) return $id;
		$id = static::generateId();
		$this->storage->set('reference_id', $id);
		return $id;
	}

	protected function getIdempotencyKey() {
		if ($this->whenLastUpdated() > $this->cart->whenLastUpdated()) return $this->storage->get('idempotency_key');
		$id = static::generateId();
		$this->storage->set('idempotency_key', $id);
		$this->storage->set('lastUpdateTimestamp', time());
		return $id;
	}

	protected function createRequest() {
		$request = new CreateCheckoutRequest();
		$request['idempotency_key'] = $this->getIdempotencyKey();
		$request['order'] = $this->createOrder();
		$request['ask_for_shipping_address'] = TRUE;
		$request['redirect_url'] = App::canonicalUrl(App::routeUrl('order'));
		return $request;
	}

	public function request() {
		Store::configure();
		$api = new CheckoutApi();
    return $api->createCheckout(getenv('SQUARE_LOCATION_ID'), $this->createRequest());
	}

}
