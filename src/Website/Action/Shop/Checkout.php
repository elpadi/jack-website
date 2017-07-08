<?php
namespace Website\Action\Shop;

use SquareConnect\ApiException;
use Jack\Action\Page;
use Website\App;
use Website\Shop\Orders;

class Checkout extends Page {

	protected function templatePath() {
		return 'store/checkout';
	}

	protected function assets() {
		return [
			'css' => ['store/base','store/checkout'],
			'js' => ['store/store','store/checkout'],
		];
	}

	protected function metaTitle() {
		return 'Order Checkout | Jack Magazine Shop';
	}

	protected function api($response) {
		$checkout = App::$container['checkout'];
		try {
			$api_response = $checkout->request();
			$errors = $api_response->getErrors();
			if (!empty($errors)) {
				$this->data['response'] = ['errors' => F\invoke($errors, 'getDetail')];
			}
			else {
				Orders::saveCheckout($api_response->getCheckout(), App::$container['cart']);
				$this->data['response'] = ['checkout_url' => $api_response->getCheckout()->getCheckoutPageUrl()];
			}
		}
		catch (ApiException $e) {
			$this->data['response'] = $e->getResponseBody();
		}
		return parent::api($response);
	}

}
