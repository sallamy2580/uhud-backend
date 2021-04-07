<?php

//return [
//
//
//    'driver' => env('MAIL_DRIVER', 'smtp'),
//    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
//    'port' => env('MAIL_PORT', 587),
//    'from' => [
//        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
//        'name' => env('MAIL_FROM_NAME', 'Example'),
//    ],
//    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
//    'username' => env('MAIL_USERNAME'),
//    'password' => env('MAIL_PASSWORD'),
//    'sendmail' => '/usr/sbin/sendmail -bs',
//    'markdown' => [
//        'theme' => 'default',
//
//        'paths' => [
//            resource_path('views/vendor/mail'),
//        ],
//    ],
//    'log_channel' => env('MAIL_LOG_CHANNEL'),
//
//];

return [


    'driver' => env('MAIL_DRIVER=smtp','smtp'),
    'host' => env('MAIL_HOST', 'smtp.googlemail.com'),
    'port' => env('MAIL_PORT', '465'),
    'from' => [
        'address' =>env('MAIL_FROM', 'alzuhud.info@gmail.com'),
        'name' => env('MAIL_NAME', 'alzuhud'),
    ],
    'encryption' => env('MAIL_ENCRYPTION' ,'ssl'),
    'username' => env('MAIL_USERNAME', 'alzuhud.info@gmail.com'),
    'password' => env('MAIL_PASSWORD', 'alzuhud123'),
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
    'log_channel' => env('MAIL_LOG_CHANNEL'),

];
