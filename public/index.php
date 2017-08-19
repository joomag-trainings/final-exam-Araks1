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
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response, '404.phtml')->withStatus(404);
    };
};
$container[\Model\AuthModel::class] = function ($container) {

    return new Model\AuthModel($container);

};
$container[\Model\DiscussionModel::class] = function ($container) {

    return new Model\DiscussionModel($container);

};
$container[\Model\CommentsModel::class] = function ($container) {

    return new Model\CommentsModel($container);

};
$app->get('/register', \Controller\AuthController::class . ':showRegPage');
$app->get('/signout', \Controller\AuthController::class . ':signOut');
$app->post('/register', \Controller\AuthController::class . ':getRegisterParams');
$app->get('/login', \Controller\AuthController::class . ':showLoginPage');
$app->post('/login', \Controller\AuthController::class . ':loginUsers');
$app->get('/home', \Controller\HomeController::class . ':showHomePage');
$app->get('/discussion', \Controller\HomeController::class . ':showCreatePage');
$app->post('/discussion', \Controller\HomeController::class . ':createNewDiscussion');
$app->get('/single', \Controller\HomeController::class . ':showEachDiscussionPage');
$app->post('/post', \Controller\CommentsController::class . ':create');
$app->get('/delete', \Controller\CommentsController::class . ':delete');
$app->get('/error', \Controller\ErrorController::class . ':notFound');
$app->run();