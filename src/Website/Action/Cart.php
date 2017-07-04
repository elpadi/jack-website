<?php
namespace Website\Action;

use Functional as F;
use Jack\Action\Page;
use Website\Shop\Square;
use Website\App;

class Cart extends Page {

	protected function assets() {
		return [
			'css' => ['store/cart'],
			'js' => ['store/store'],
		];
	}

	protected function templatePath() {
		return 'store/cart';
	}

	protected function setupApiData($count) {
		extract($_POST, \EXTR_SKIP);
		$cartItems = App::$container['cart']->getItems();
		$this->data = array_merge(compact('id', 'variant_id'), [
			'success' => true,
			'item_count' => $count,
			'subtotal' => $cartItems->getSubtotal(),
			'shipping' => $cartItems->getShipping(),
			'cart_count' => $cartItems->getItemCount(),
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

	protected function fetchPageData() {
		parent::fetchPageData();
		$this->data['cart'] = App::$container['cart'];
		$this->data['catalog'] = Square::getCatalog();
	}

	protected function finalize($response) {
		return $this->data['cart']->getItems()->getItemCount()
			? parent::finalize($response)
			: App::redirect(App::routeUrl('storefront'));
	}

	public function cart($request, $response, $args) {
		return $this->page($request, $response, $args);
	}

}
