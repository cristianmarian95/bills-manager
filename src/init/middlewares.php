<?php
$container['SetupMiddleware'] = function($c){
    return new \App\Middlewares\SetupMiddleware($c);
};
$container['AccountMiddleware'] = function($c){
    return new \App\Middlewares\AccountMiddleware($c);
};