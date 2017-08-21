<?php
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
$app->get('/archive', \Controller\HomeController::class . ':showArchivePage');
$app->get('/mark', \Controller\CommentsController::class . ':markBestAnswer');
$app->post('/edit', \Controller\HomeController::class . ':edit');
$app->post('/archive', \Controller\HomeController::class . ':archive');
$app->run();