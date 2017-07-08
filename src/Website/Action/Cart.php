<?php
namespace Website\Action;

use Functional as F;
use Jack\Action\Page;
use Website\Shop\Square;
use Website\App;

class Cart extends Page {

	protected function assets() {
		return [
			'css' => ['store/base','store/cart'],
			'js' => ['store/store'],
		];
	}

	protected function templatePath() {
		return 'store/cart';
	}

	protected function setupApiData($count) {
		extract($_POST, \EXTR_SKIP);
		$cart = App::$container['cart'];
		$this->data = array_merge(compact('id', 'variant_id'), [
			'success' => true,
			'item_count' => $count,
			'subtotal' => $cart->getSubtotal(),
			'shipping' => $cart->getShipping(),
			'cart_count' => $cart->getItemCount(),
		]);
	}

	public function addItem($request, $response, $args) {
		extract($_POST, \EXTR_SKIP);
		App::$container['cart']->addItem($id, $variant_id);
		$this->setupApiData(1);
		return $this->api($response);
	}

	public function updateItem($request, $response, $args) {
		extract($_POST, \EXTR_SKIP);
		App::$container['cart']->updateItem($id, $variant_id, $count);
		$this->setupApiData($count);
		return $this->api($response);
	}

	public function cart($request, $response, $args) {
		return $this->page($request, $response, $args);
	}

}
