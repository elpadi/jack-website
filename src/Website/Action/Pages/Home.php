<?php
namespace Website\Action\Pages;

use Functional as F;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Website\Pages;
use Website\Models;

class Home extends Intro {

	protected function assets() {
		return [
			'css' => ['layouts/full-width','pages/intro','pages/home','pages/models'],
			'js' => ['pages/intro','pages/models'],
		];
	}

	protected function api($response) {
		unset($this->data['content']);
		unset($this->data['homepage']->content);
		return parent::api($response);
	}

	protected function fetchPageData() {
		parent::fetchPageData();
		$this->shortcodes->addHandler('jbpc_models', ['\\Website\\Pages', 'modelsShortcode']);
		$this->data['homepage'] = Pages::getHomePage();
		$this->data['content'] = $this->data['homepage']->content;
	}

}
