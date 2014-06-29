<?php

define('PUBLIC_DIR', __DIR__);
require(PUBLIC_DIR.'/config.dirs.php');
require(ROOT_DIR.'/src/bootstrap.php');

ob_start();
$app->run();
ob_end_flush();
