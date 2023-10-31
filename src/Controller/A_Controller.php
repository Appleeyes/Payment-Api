<?php

namespace PaymentApi\Controller;

use Monolog\Logger;
use Psr\Container\ContainerInterface;


abstract class A_Controller
{
    protected Logger $logger;

    public function __construct(protected ContainerInterface $container)
    {
        $this->logger = $container->get(Logger::class);
    }

}