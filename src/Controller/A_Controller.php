<?php

namespace PaymentApi\Controller;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class A_Controller
{
    protected Logger $logger;

    public function __construct(protected ContainerInterface $container)
    {
        $this->logger = $container->get(Logger::class);
    }

}