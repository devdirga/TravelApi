<?php

$router = new Phalcon\Mvc\Router(false);

$router->removeExtraSlashes(true);

$router->add('/app/:controller', [
    'namespace'  => 'Travel\App',
    'controller' => 1
]);

$router->add('/flight/:controller', [
    'namespace'  => 'Travel\Flight',
    'controller' => 1
]);

$router->add('/flight/home/:controller/:action', [
    'namespace' => 'Travel\Flight',
    'controller' => 1,
    'action' => 2
]);

$router->add('/hotel/:controller', [
    'namespace'  => 'Travel\Hotel',
    'controller' => 1
]);

$router->add('/pelni/:controller', [
    'namespace'  => 'Travel\Pelni',
    'controller' => 1
]);

$router->add('/tour/:controller', [
    'namespace'  => 'Travel\Tour',
    'controller' => 1
]);

$router->add('/train/:controller', [
    'namespace'  => 'Travel\Train',
    'controller' => 1
]);

$router->add('/travelbus/:controller', [
    'namespace'  => 'Travel\TravelBus',
    'controller' => 1
]);

$router->add('/wisata/:controller', [
    'namespace'  => 'Travel\Wisata',
    'controller' => 1
]);

$router->add('/jadipergi/:controller', [
    'namespace'  => 'Travel\Jadipergi',
    'controller' => 1
]);

$router->add('/umroh/:controller', [
    'namespace'  => 'Travel\Umroh',
    'controller' => 1
]);

return $router;