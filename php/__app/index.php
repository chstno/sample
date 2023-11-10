<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/Core/Autoloader.php';
require_once __DIR__ . '/Core/helpers.php';
require_once __DIR__ . '/Core/api.php';

/**
 * @Notice: The project is written with an older syntax-standard
 * (and, to be honest, parameter declarations in the constructor seems to me weird)
 */



try {
    $request = request();
    \Core\Routing\Router::handle($request);
} catch (\Core\Exception\RouteNotFoundException $e) {
    redirect('/404');
} catch (Throwable $e) {
    throw $e;
}