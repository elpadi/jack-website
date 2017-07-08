<?php
namespace Website\Action\Shop;

use Jack\Action\Page;
use Website\App;
use Website\Shop\Square\Store;
use Website\Shop\Orders;

class Order extends Page {

	protected function templatePath() {
		return 'store/order';
	}

	protected function assets() {
		return [
			'css' => ['store/base','store/order'],
			'js' => ['store/store','store/order'],
		];
	}

	protected function metaTitle() {
		return 'Order Confirmation | Jack Magazine Shop';
	}

	protected function fetchData($args) {
		App::$container['cart']->clear();
		App::$container['checkout']->storage->clear();
		extract($_GET);
		if (!isset($transactionId) || empty($transactionId) || !isset($referenceId) || empty($referenceId)) {
			$this->data['success'] = FALSE;
			$this->data['message'] = 'Missing order details.';
		}
		else {
			$result = Store::verifyTransaction($transactionId, $referenceId);
			$this->data['success'] = $result[0];
			$this->data['message'] = $result[1];
		}
		if ($this->data['success']) {
			Orders::verify($referenceId, $transactionId);
		}
	}

}
