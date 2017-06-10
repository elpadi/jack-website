<?php
namespace Website\Action\Cart;

use Website\Shop\Cart;

class AddItem {

	protected function send($response) {
		return $response->withHeader('Content-type', 'application/json')->write(json_encode($this->data));
	}

	public function run($request, $response, $args) {
		global $app;
		$count = Cart::instance()->addItem($_POST['id'], $_POST['variant_id']);
		$this->data = [
			'success' => $count > 0,
			'count' => $count,
		];
		$this->send($response);
	}

}
