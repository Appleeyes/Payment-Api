<?php

use Dotenv\Dotenv;
use PaymentApi\Middleware\ErrorHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Swagger\scan;


require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/container.php';


$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->safeLoad();

$app = AppFactory::createFromContainer(container: $container);

function getBasePath()
{
    $requestUri = $_SERVER['REQUEST_URI'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    return substr($requestUri, 0, strpos($requestUri, $scriptName));
}

$app->get('/api-docs', function (Request $request, Response $response) {
    // Include the Swagger UI HTML page
    include __DIR__ . '/swagger-ui/dist/index.html';
    return $response;
});

$app->group('/v1/methods', function (RouteCollectorProxy $group) {
    $group->get('', '\PaymentApi\Controller\MethodsController:indexAction');
    $group->post('', '\PaymentApi\Controller\MethodsController:createAction');
    $group->delete('/{id:[0-9]+}', '\PaymentApi\Controller\MethodsController:removeAction');
    $group->get('/deactivate/{id:[0-9]+}', '\PaymentApi\Controller\MethodsController:deactivateAction');
    $group->get('/reactivate/{id:[0-9]+}', '\PaymentApi\Controller\MethodsController:reactivateAction');
    $group->put('/{id:[0-9]+}', '\PaymentApi\Controller\MethodsController:updateAction');
});
$handler = new ErrorHandler($app);

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($handler);
$app->run();

