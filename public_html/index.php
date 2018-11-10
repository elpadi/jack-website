<?php
global $app;

$jack_dir = isset($_ENV['JACK_DIR']) ? $_ENV['JACK_DIR'] : dirname(dirname(__DIR__)).'/common';
$app_dir = isset($_ENV['APP_DIR']) ? $_ENV['APP_DIR'] : dirname(__DIR__);
define('PUBLIC_ROOT_DIR', __DIR__);
define('PUBLIC_ROOT', '/' . (isset($_ENV['APP_PUBLIC_DIR']) ? $_ENV['APP_PUBLIC_DIR'].'/' : ''));

// load website bootstrap first, so env vars are defined before jack bootstrap
require(realpath($app_dir).'/src/bootstrap.php');
// jack bootstrap defines the debug constant
require(realpath($jack_dir).'/src/bootstrap.php');

$app = new Website\App();
$app->run();
