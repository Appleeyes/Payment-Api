<?php

use Dotenv\Dotenv;
use PaymentApi\Middleware\ErrorHandler;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/container.php';


$dotenv = Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->safeLoad();

$app = AppFactory::create(container: $container);

$app->get('/v1/methods', '\PaymentApi\Controller\MethodsController:indexAction');

$handler = new ErrorHandler($app);

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($handler);
$app->run();
