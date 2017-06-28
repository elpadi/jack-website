<?php
namespace Website\Shop;

use Functional as F;
use Website\Issues\Issue;

class SquareVariant {

	protected $raw;
	protected $issue;

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
		return $this->raw['variant']->item_variation_data->price_money->amount / 100;
	}

	public function fetchIssue() {
		if ($this->issue) return $this->issue;
		if (!preg_match('/^Issue #([0-9]+) - (.*) - Part ([0-9]+)$/', $this->getItemTitle(), $matches)) return NULL;
		$this->issue = Issue::createByNumber($matches[1], $matches[3]);
		return $this->issue;
	}

}
