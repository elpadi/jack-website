<?php
namespace Website;

class App extends \Jack\App {

	public static function createTemplate() {
		return new Template();
	}	
	
}
