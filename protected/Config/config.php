<?php

use Phalcon\Config;

return new Config(
    [
        'app' => [
            'jwtIssuer' => 'http://api.Travel.co.id',
            'jwtAudience' => 'TravelB2B Client',
            'jwtId' => '8d73mng89ed',
            'jwtNotBefore' => time() + 60,
            'jwtExpired' => time() + 3600,
            'jwtSignKey' => 'usUB7266Hyhjw86HS6wU6727783bbyejwuvsHHJGSku8',
            'ipAddress' => '10.0.0.20',
            'port' => '21080',
            'path' => '/FMSSWeb/mpin1',
            'cdn' => 'https://static.scash.bz/Travel/',
            'defaultToken' => 'travel',
            'publicUris' => [
                'app/sign_in',
                'app/request_key',
                'app/ping',
                'app/data_pax',
                'flight/flight_elastic'
            ]
        ],
        'umroh' => [
            'domainUmroh' => 'http://10.0.0.53:88',
            'path' => '/UmrohCore',
            'port' => 88
        ],
        'flightElastic' => [
            'host' => 'http://10.0.0.34',
            'path' => '/LionAPIBiller/?',
            'port' => 25080
        ],
        'db' => [
            'adapter'     => 'Postgresql',
            'host'        => '10.0.0.20',
            'username'    => 'fmss',
            'password'    => 'rahasia',
            'dbname'      => 'fmss',
            'schema'      => 'fmss'
        ],
        'dbmysql' => [
            'adapter'     => 'mysql',
            'host'        => '10.0.0.20',
            'username'    => 'root',
            'password'    => 'mboklom098',
            'dbname'      => 'Travel_mobile',
        ],
        'application' => [
            'controllersDir' => APP_PATH . 'app/controllers/',
            'librariesDir'     => APP_PATH . 'app/libraries/',
            'baseUri'        => '/',
        ],
        'trainOnlineBook' => [
            'host' => '10.0.0.38',
            'port' => '14000',
            'path' => 'InterfaceKai',
            'inquiry' => 'InterfaceInquiryDesktop.php',
            'payment' => 'InterfacePaymentDesktop.php',
            'via' => 'DESKTOP'
        ],

        'pdfGenerator' => [
            'host' => '10.0.0.46',
            'port' => '80',
            'path' => 'imagegen',
            'pathimage' => 'http://10.0.0.46/imagegen/struk/',
            'pathpdf' => 'http://10.0.0.46/pdfgen/struk_kereta/',
        ],

        'path_assets' => APP_PATH . 'protected/Asset/Img/',

        'logo' => 'Travel.bmp'
    ]
);