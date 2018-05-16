<?php
$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['database']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function () use ($capsule){
    return $capsule;
};

$container['flash'] = function() {
    return new \Slim\Flash\Messages();
};

$container['validate'] = function (){
    return new \Violin\Violin();
};

$container['session'] = function() {
    return new \App\Helpers\SessionHelper();
};
$container['mail'] = function ($c) {
    return new \App\Helpers\MailHelper($c);
};
$container['function'] = function ($c) {
    return new \App\Helpers\Functions($c);
};
$container['view'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    $view = new \Slim\Views\Twig($settings['template_path']);
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));
    $view->getEnvironment()->addGlobal('url', $c['request']->getUri()->getBaseUrl());
    $view->getEnvironment()->addGlobal('flash', $c['flash']);
    $view->getEnvironment()->addGlobal('function', $c['function']);
    $view->getEnvironment()->addGlobal('session', $c['session']);
    return $view;
};