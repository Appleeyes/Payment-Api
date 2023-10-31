<?php

namespace PaymentApi\Controller;

use Monolog\Logger;
use Psr\Container\ContainerInterface;


/**
 * A_Controller
 */
abstract class A_Controller
{
    protected Logger $logger;
    
    /**
     * Method __construct
     *
     * @param protected $container [explicite description]
     *
     * @return void
     */
    public function __construct(protected ContainerInterface $container)
    {
        $this->logger = $container->get(Logger::class);
    }

}