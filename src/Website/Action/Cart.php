<?php
namespace Website\Action;

use Functional as F;
use Jack\Action\Page;
use Website\Shop\Square;
use Website\App;

class Cart extends Page {

	protected function setupApiData($count) {
		extract($_POST, \EXTR_SKIP);
		$this->data = array_merge(compact('id', 'variant_id'), [
			'success' => true,
			'item_count' => $count,
			'cart_count' => App::$container['cart']->getItems()->getItemCount(),
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
		$this->data['cart'] = \Website\App::$container['cart'];
		$this->data['catalog'] = Square::getCatalog();
	}

	protected function finalize($response) {
		global $app;
		return $this->data['cart']->getItems()->getItemCount()
			? parent::finalize($response)
			: $response->withRedirect($app->routeLookup('storefront'));
	}

	public function cart($request, $response, $args) {
		return $this->page($request, $response, $args);
	}

}
