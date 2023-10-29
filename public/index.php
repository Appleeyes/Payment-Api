<?php

use Dotenv\Dotenv;
use PaymentApi\Middleware\ErrorHandler;
use PaymentApi\Middleware\JwtMiddleware;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Psr7\Request;
use Slim\Psr7\Response;


require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/container.php';


$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->safeLoad();

$app = AppFactory::createFromContainer(container: $container);

$app->get('/api-docs', function (Request $request, Response $response) {
    include __DIR__ . '/swagger-ui/dist/index.html';
    return $response;
});



$jwtMiddleware = new JwtMiddleware($_ENV['JWT_SECRET_KEY']);

$app->get('/generate-token', function (Request $request, Response $response) {
    exec('php ' . __DIR__ . '/../config/generateToken.php', $output);
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(implode("\n", $output));
    return $response;
});

$app->group('/v1/methods', function (RouteCollectorProxy $group) {
    $group->get('', '\PaymentApi\Controller\MethodsController:indexAction');
    $group->post('', '\PaymentApi\Controller\MethodsController:createAction');
    $group->delete('/{id:[0-9]+}', '\PaymentApi\Controller\MethodsController:removeAction');
    $group->get('/deactivate/{id:[0-9]+}', '\PaymentApi\Controller\MethodsController:deactivateAction');
    $group->get('/reactivate/{id:[0-9]+}', '\PaymentApi\Controller\MethodsController:reactivateAction');
    $group->put('/{id:[0-9]+}', '\PaymentApi\Controller\MethodsController:updateAction');
})->add($jwtMiddleware);

$app->group('/v1/customers', function (RouteCollectorProxy $group) {
    $group->get('', '\PaymentApi\Controller\CustomersController:indexAction');
    $group->post('', '\PaymentApi\Controller\CustomersController:createAction');
    $group->delete('/{id:[0-9]+}', '\PaymentApi\Controller\CustomersController:removeAction');
    $group->get('/deactivate/{id:[0-9]+}', '\PaymentApi\Controller\CustomersController:deactivateAction');
    $group->get('/reactivate/{id:[0-9]+}', '\PaymentApi\Controller\CustomersController:reactivateAction');
    $group->put('/{id:[0-9]+}', '\PaymentApi\Controller\CustomersController:updateAction');
})->add($jwtMiddleware);

$app->group('/v1/payments', function (RouteCollectorProxy $group) {
    $group->get('', '\PaymentApi\Controller\PaymentsController:indexAction');
    $group->post('', '\PaymentApi\Controller\PaymentsController:createAction');
    $group->delete('/{id:[0-9]+}', '\PaymentApi\Controller\PaymentsController:removeAction');
    $group->put('/{id:[0-9]+}', '\PaymentApi\Controller\PaymentsController:updateAction');
})->add($jwtMiddleware);
$handler = new ErrorHandler($app);

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($handler);
$app->run();

