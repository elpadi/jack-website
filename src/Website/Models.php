<?php
namespace Website;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;

class Models {

	public function __construct() {
	}

	public function fetchAll() {
		return cockpit('collections:find', 'models');
	}

	public function fetchBySlug($slug) {
		$name = ucwords(str_replace('-', ' ', $slug));
		return cockpit('collections:findOne', 'models', compact('name'));
	}

}
