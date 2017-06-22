<?php
namespace Website\Action\Cart;

use Website\Shop\Cart;

class UpdateItem {

	protected function send($response) {
		return $response->withHeader('Content-type', 'application/json')->write(json_encode($this->data));
	}

	public function run($request, $response, $args) {
		global $app;
		extract($_POST, \EXTR_SKIP);
		Cart::instance()->updateItem($id, $variant_id, $count);
		$this->data = array_merge(compact('id', 'variant_id'), [
			'success' => true,
			'item_count' => $count,
			'cart_count' => Cart::instance()->itemCount()
		]);
		$this->send($response);
	}

}
