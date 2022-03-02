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
    case '185.62.73.200':
        $dev = false;
        $url = 'https://mojatrgovina.online/';
        $database = [
            'server' => 'localhost',
            'database' => 'mojatrgo_shop',
            'user' => 'mojatrgo_user',
            'password' => 'R6h}[)N?jt]j'
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
