<?php
namespace Website\Shop;

use SquareConnect\Model\Checkout;
use Website\Data\StaticNameCollection;

class Orders extends StaticNameCollection {

	protected static $NAME = 'orders';

	public static function saveCheckout(Checkout $checkout, $cart) {
		static::add([
			'reference_id' => $checkout['order']['reference_id'],
			'transaction_id' => '',
			'checkout_id' => $checkout['id'],
			'date' => $checkout['created_at'],
			'total_amount' => $cart->getShipping() + $cart->getSubtotal()['net'],
			'is_verified' => FALSE,
		]);
	}

	public static function verify($referenceId, $transactionId) {
		static::update(['reference_id' => $referenceId], ['transaction_id' => $transactionId, 'is_verified' => TRUE]);
	}

}
