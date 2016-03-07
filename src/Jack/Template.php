<?php
namespace Jack;

class Template {

	public function __construct() {
		$this->loader = new \Twig_Loader_Filesystem(TEMPLATE_DIR);
		$this->twig = new \Twig_Environment($this->loader, array(
			'debug' => DEBUG,
			'cache' => DEBUG ? false : CACHE_DIR.'/twig',
		));
		$this->twig->addFunction(new \Twig_SimpleFunction('urlFor', ['\Jack\App','routeLookUp']));
	}

	public function render($path, $vars) {
		try {
			$content = $this->twig->render("$path.twig", $vars);
		}
		catch (\Exception $e) {
			var_dump(__FILE__.":".__LINE__." - ".__METHOD__, $e);
			exit(0);
		}
		return $content;
	}

}

