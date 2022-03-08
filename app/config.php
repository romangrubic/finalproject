<?php


switch ($_SERVER['SERVER_ADDR']) {
    // Development or local
    case '127.0.0.1':
        $dev = true;
        $url = 'http://romangrubic.com/';
        $database = [
            'server' => 'localhost',
            'database' => 'shop',
            'user' => 'edunova',
            'password' => 'edunova'
        ];
        break;
    // Production
    case '46.101.238.150':
        $dev = false;
        $url = 'https://mojatrgovina.online/';
        $database = [
            'server' => 'localhost',
            'database' => 'shop',
            'user' => 'edunova',
            'password' => 'edunova'
        ];
        break;

    default:
        echo 'Not valid server adress.';
        break;
}

return [
    'dev' => $dev,
    'url' => $url,
    'appName' => 'Moja Trgovina',
    'database' => $database
];
