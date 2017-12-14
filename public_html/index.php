<?php
global $app;

$jack_dir = isset($_ENV['JACK_DIR']) ? $_ENV['JACK_DIR'] : dirname(dirname(__DIR__)).'/common';
$app_dir = isset($_ENV['APP_DIR']) ? $_ENV['APP_DIR'] : dirname(__DIR__);
define('PUBLIC_ROOT_DIR', __DIR__);
define('PUBLIC_ROOT', '/' . (isset($_ENV['APP_PUBLIC_DIR']) ? $_ENV['APP_PUBLIC_DIR'].'/' : ''));

require(realpath($jack_dir).'/src/bootstrap.php');
require(realpath($app_dir).'/src/bootstrap.php');

$app->run();
