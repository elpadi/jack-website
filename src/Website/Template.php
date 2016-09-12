<?php
namespace Website;

class Template extends \Jack\Template {

	protected static function getTemplateDir() {
		return WEBSITE_DIR.'/templates';
	}

	public function __construct() {
		global $app;
		parent::__construct();
		$this->twig->addFunction(new \Twig_SimpleFunction('cockpit_opening_form_tag', function($form) {
			ob_start();
			cockpit('forms:open', $form);
			return ob_get_clean();
		}));
		$this->twig->addFunction(new \Twig_SimpleFunction('svg', function($name, $text) {
			return file_get_contents(sprintf('%s/assets/svg/%s.svg', PUBLIC_ROOT_DIR, $name));
		}));
	}
	
}
