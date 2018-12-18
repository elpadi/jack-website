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

	protected function setupApiData($count=-1) {
		extract($_POST, \EXTR_SKIP);
		$cart = App::$container['cart'];
		$data = [
			'success' => true,
			'item_count' => $count,
			'subtotal' => $cart->getSubtotal(),
			'shipping' => $cart->getShipping(),
			'cart_count' => $cart->getItemCount(),
			'discount' => $cart->getItems()['discount'],
		];
		$this->data = isset($variant_id) ? array_merge(compact('id', 'variant_id'), $data) : $data;
	}

	public function removeDiscount($request, $response, $args) {
		App::$container['cart']->removeDiscount();
		$this->setupApiData();
		return $this->api($response);
	}

	public function discount($request, $response, $args) {
		if (!isset($_POST['discount_code']) || empty($_POST['discount_code'])) {
			throw new \InvalidArgumentException("Discount code not found.", 404);
		}
		extract($_POST, \EXTR_SKIP);
		App::$container['cart']->discount(strtoupper($discount_code));
		$this->setupApiData();
		return $this->api($response);
	}

	public function addItem($request, $response, $args) {
		extract($_POST, \EXTR_SKIP);
		App::$container['cart']->addItem($id, $variant_id);
		$this->setupApiData(1);
		return $this->api($response);
	}

	public function updateItem($request, $response, $args) {
		extract($_POST, \EXTR_SKIP);
		App::$container['cart']->updateItem($id, $variant_id, (int)$count);
		$this->setupApiData((int)$count);
		return $this->api($response);
	}

	public function cart($request, $response, $args) {
		return $this->page($request, $response, $args);
	}

}
