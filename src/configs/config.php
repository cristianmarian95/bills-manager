<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'renderer' => [
            'template_path' => __DIR__ . '/../template', //Path to twig template
        ],
        'database' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'billing_manager',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
    ],
];