<?php
namespace Website\Shop\Square;

abstract class Variant {

	protected $raw;

	public function __construct($variant, $item) {
		$this->raw = compact('variant','item');
	}

	public function getItemId() {
		return $this->raw['item']->id;
	}

	public function getVariantId() {
		return $this->raw['variant']->id;
	}

	public function getItemTitle() {
		return $this->raw['item']->item_data->name;
	}

	public function getPrice() {
		return DEBUG ? 1 : $this->raw['variant']->item_variation_data->price_money->amount / 100;
	}

	public function getImageSrc() {
		return $this->raw['item']->item_data->image_url;
	}

}
