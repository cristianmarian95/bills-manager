<?php
session_start();
if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}
require_once __DIR__ . '/src/vendor/autoload.php';
$app = new \Slim\App(require_once  __DIR__ . '/src/configs/config.php');
require_once __DIR__ . '/src/init/bootstrap.php';
require_once __DIR__ . '/src/init/controllers.php';
require_once __DIR__ . '/src/init/middlewares.php';
require_once __DIR__ . '/src/init/routes.php';
$app->run();

