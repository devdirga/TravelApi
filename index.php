<?php

error_reporting(E_ALL);

use Phalcon\Mvc\Application;

try {
    define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR);

    $config = require APP_PATH . 'protected/Config/config.php';

    $loader = new \Phalcon\Loader();

    $loader->registerNamespaces(['Travel' => 'protected/Controllers', 'Travel\Libraries' => 'protected/Libraries/', 'Lcobucci\JWT' => 'protected/Libraries/Lcobucci/JWT'])->register();

    $loader->registerClasses(['Services' => APP_PATH . 'protected/Services.php']);

    $application = new Application(new Services($config));

    echo $application->handle(!empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null)->getContent();
} catch (Exception $e) {
    define("APP_RC", "99");
    define("APP_RD", $e->getMessage());
}