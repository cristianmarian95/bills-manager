<?php
$container['MainController'] = function($c) {
    return new \App\Controllers\MainController($c);
};
$container['ActionController'] = function($c) {
    return new \App\Controllers\ActionController($c);
};
$container['AdminController'] = function($c) {
    return new \App\Controllers\AdminController($c);
};