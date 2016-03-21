<?php
namespace Website;

class Template extends \Jack\Template {

	protected static function getTemplateDir() {
		return WEBSITE_DIR.'/templates';
	}

	public function __construct() {
		parent::__construct();
		$this->twig->addFilter(new \Twig_SimpleFilter('image', ['\Website\App','imageUrl']));
		$this->twig->addFilter(new \Twig_SimpleFilter('url', ['\Website\App','url']));
		$this->twig->addFilter(new \Twig_SimpleFilter('asset_url', ['\Website\App','asset_url']));
	}
	
}
