<?php
namespace Website\Shop\Square;

use Functional as F;
use SquareConnect\Api\CatalogApi;

class Catalog extends \ArrayObject {

	public function __construct() {
		parent::__construct([]);
		$this->raw = Store::fetchCatalogData();
	}

}
