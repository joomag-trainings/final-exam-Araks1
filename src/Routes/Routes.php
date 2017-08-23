<?php
$app->get('/register', \Controller\AuthController::class . ':showRegPage');
$app->post('/register', \Controller\AuthController::class . ':registerUsers');


$app->get('/login', \Controller\AuthController::class . ':showLoginPage');
$app->post('/login', \Controller\AuthController::class . ':loginUsers');
$app->get('/signout', \Controller\AuthController::class . ':signOut');

$app->get('/home', \Controller\HomeController::class . ':showHomePage');

$app->group('/discussion', function () use ($app) {

    $app->get('', \Controller\HomeController::class . ':showEachDiscussionPage');
    $app->get('/create', \Controller\HomeController::class . ':showDiscussionCreatePage');
    $app->post('/create', \Controller\HomeController::class . ':createNewDiscussion');
    $app->get('/archive', \Controller\HomeController::class . ':showArchivePage');
    $app->post('/archive', \Controller\HomeController::class . ':archiveDiscussion');
    $app->post('/edit', \Controller\HomeController::class . ':editDiscussion');

});

$app->group('/comments', function () use ($app) {
    $app->post('/add', \Controller\CommentsController::class . ':addComment');
    $app->get('/delete', \Controller\CommentsController::class . ':deleteComment');
    $app->get('/mark', \Controller\CommentsController::class . ':markBestAnswer');

});

$app->get('/error', \Controller\ErrorController::class . ':notFound');
$app->run();