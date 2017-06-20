<?php
namespace Website\Action\Cart;

use Website\Shop\Cart;

class UpdateItem {

	protected function send($response) {
		return $response->withHeader('Content-type', 'application/json')->write(json_encode($this->data));
	}

	public function run($request, $response, $args) {
		global $app;
		$count = Cart::instance()->updateItem($_POST['id'], $_POST['variant_id'], $_POST['count']);
		$this->data = ['success' => true];
		$this->send($response);
	}

}
