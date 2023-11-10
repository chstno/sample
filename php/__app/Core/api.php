<?php


use Controllers\IndexController;
use Core\Routing\Router;

$indexController = instance(IndexController::class);

Router::get('/', [$indexController, 'index']);
Router::get('/test/{userId}', [$indexController, 'test']);
Router::get('/anotherTest/{anotherVal}/{someId}', [$indexController, 'otherTest']);
Router::any('/404', [$indexController, 'error404']);