<?php

use Dotenv\Dotenv;
use PaymentApi\Controller\MethodsController;
use PaymentApi\Middleware\ErrorHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->safeLoad();

$app = AppFactory::create();

$app->get('/v1/methods', 'MethodsController:indexAction');

$handler = new ErrorHandler($app);

$errorMiddleware = $app->addErrorMiddleware(false, true, true);
$errorMiddleware->setDefaultErrorHandler($handler);
$app->run();
