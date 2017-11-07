<?php

return [
    // comment out the following one lines when deployed to production
    'install'=>true,
    //
    'salt'=>'{SALT}',
    'images'=>require(__DIR__ . '/images.php'),
    'widgets'=>require(__DIR__ . '/widgets.php'),
    'siteName' => 'YuCMS',
    'adminEmail' => 'your@example.com',
];
