<?php
$app->group('', function() use ($app){

    $app->group('/account', function() use ($app) {
        $app->get('/login', 'MainController:login')->setName('login');
        $app->get('/register', 'MainController:register')->setName('register');
        $app->get('/recover', 'MainController:recover')->setName('recover');
        $app->get('/module/recover[/{key}]', 'MainController:changePassword')->setName('changePassword');
        $app->post('/action/login', 'ActionController:actionLogin')->setName('actionLogin');
        $app->post('/action/register', 'ActionController:actionRegister')->setName('actionRegister');
        $app->post('/action/recover/create', 'ActionController:actionRecover')->setName('actionRecover');
        $app->post('/action/recover/change', 'ActionController:actionChangePassword')->setName('actionChangePassword');
    })->add('AccountMiddleware:ifLog');


    $app->group('', function() use ($app) {
        $app->get('/', 'MainController:index')->setName('index');
        $app->get('/bill/details[/{number}]', 'MainController:billingDetails')->setName('billingDetails');
        $app->get('/pay[/{number}]', 'MainController:billingPay')->setName('billingPay');
        $app->get('/transactions', 'MainController:myTransactions')->setName('myTransactions');
        $app->get('/read', 'MainController:getReading')->setName('getReading');
        $app->get('/profile', 'MainController:myProfile')->setName('myProfile');
        $app->get('/logout', 'MainController:logout')->setName('logout');
        $app->post('/action/billing/pay', 'ActionController:makePayment')->setName('makePayment');
        $app->post('/action/make/read', 'ActionController:makeRead')->setName('makeRead');
        $app->post('/action/profile/update/password', 'ActionController:changeProfilePassword')->setName('changeProfilePassword');
        $app->post('/action/profile/update', 'ActionController:changeProfile')->setName('changeProfile');

        $app->group('/acp', function()  use ($app) {
            $app->get('/', 'AdminController:index')->setName('adminIndex');
            $app->get('/users', 'AdminController:getUsers')->setName('getUsers');
            $app->group('/user', function() use ($app) {
               $app->get('/profile[/{id}]', 'AdminController:getUser')->setName('getUser');
               $app->get('/bills[/{id}]', 'AdminController:getBills')->setName('getBills');
               $app->get('/bill[/{id}/{number}]', 'AdminController:getBill')->setName('getBill');
            });
            $app->group('/action', function() use ($app) {
                $app->get('/active[/{id}]', 'AdminController:activeUser')->setName('activeUser');
                $app->get('/info[/{id}]', 'AdminController:getUser')->setName('getUser');
                $app->get('/delete[/{id}]', 'AdminController:deleteUser')->setName('deleteUser');
                $app->get('/set/admin[/{id}]', 'AdminController:setAdmin')->setName('setAdmin');
                $app->get('/set/user[/{id}]', 'AdminController:delAdmin')->setName('delAdmin');
            });
            $app->get('/prices', 'AdminController:getPrices')->setName('getPrices');
            $app->post('/action/update/price', 'ActionController:updatePrices')->setName('setPrices');
        })->add('AccountMiddleware:isAdmin');

    })->add('AccountMiddleware:isLog');

})->add('SetupMiddleware:setup');