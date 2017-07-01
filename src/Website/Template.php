<?php
namespace Website;

use cebe\markdown\Markdown;
use Website\Issues\Issues;

class Template extends \Jack\Template {

	protected static function getTemplateDir() {
		return WEBSITE_DIR.'/templates';
	}

	protected function extendTwig(\Twig_Environment $twig) {
		parent::extendTwig($twig);
		$twig->addFunction(new \Twig_SimpleFunction('cockpit_opening_form_tag', function($form) {
			ob_start();
			cockpit('forms:open', $form);
			return ob_get_clean();
		}));
		$twig->addFunction(new \Twig_SimpleFunction('cockpit_collection', function($name) {
			return cockpit('collections:find', $name);
		}));
		$twig->addFunction(new \Twig_SimpleFunction('svg', function($name, $text) {
			return file_get_contents(sprintf('%s/assets/svg/%s.svg', PUBLIC_ROOT_DIR, $name));
		}));
		$twig->addFilter(new \Twig_SimpleFilter('md', function($text) {
			return (new Markdown())->parse($text);
		}));
		$twig->addFilter(new \Twig_SimpleFilter('mdp', function($text) {
			return (new Markdown())->parseParagraph($text);
		}));
		$twig->addFilter(new \Twig_SimpleFilter('money', function($n) {
			return '$'.number_format($n, 0);
		}));
	}

	protected function addCommonVariables(&$vars, $path) {
		parent::addCommonVariables($vars, $path);
		if (!isset($vars['issues'])) {
			$issues = new Issues();
			$issues->fetchAll();
			$vars['issues'] = $issues;
		}
	}

}
