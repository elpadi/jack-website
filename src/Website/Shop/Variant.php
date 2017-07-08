<?php
namespace Website\Shop;

use Website\Issues\Issues;

class Variant extends Square\Variant {

	protected $cartCount = 0;

	public function getCartSubtotal() {
		return $this->getPrice() * $this->cartCount;
	}

	public function setCartCount(int $cartCount) {
		$this->cartCount = $cartCount;
	}

	public function getCartCount() {
		return $this->cartCount;
	}

}
