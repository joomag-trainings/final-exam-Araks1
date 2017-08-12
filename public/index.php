<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';
require '../config/settings.php';

$app = new \Slim\App();
$container = $app->getContainer();
$settings = $container->get('settings');
$settings->replace($config);
$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};

$container['view'] = new \Slim\Views\PhpRenderer("../src/view/");
$container[\Controller\AuthController::class] = function ($container) {

    return new \Controller\AuthController($container);

};
$app->get('/register',\Controller\AuthController::class.':showRegPage');
$app->post('/register',\Controller\AuthController::class.':getRegisterParams');
$app->get('/login',\Controller\AuthController::class.':showLoginPage');

$app->run();
