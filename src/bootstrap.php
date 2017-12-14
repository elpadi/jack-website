<?php
use Website\App;

global $app;
define('WEBSITE_DIR', dirname(__DIR__));

require(WEBSITE_DIR.'/vendor/autoload.php');
require(PUBLIC_ROOT_DIR.'/admin/bootstrap.php');

$env = new Dotenv\Dotenv(WEBSITE_DIR);
$env->load();

$app = new App();
