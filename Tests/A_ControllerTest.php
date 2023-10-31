<?php

namespace PaymentApi\Tests;

use DI\Container;
use Doctrine\ORM\EntityManager;
use Mockery;
use Monolog\Logger;
use PaymentApi\Controller\CustomersController;
use PaymentApi\Repository\CustomersRepository;
use PaymentApi\Repository\CustomersRepositoryDoctrine;
use PHPUnit\Framework\TestCase;

class A_ControllerTest extends TestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        $container = new Container();
        $container->set(EntityManager::class, function (Container $c) {
            return Mockery::mock('Doctrine\ORM\EntityManager');
        });

        $container->set(CustomersRepository::class, function (Container $c) {
            $entitymanager = $c->get(EntityManager::class);
            return new CustomersRepositoryDoctrine($entitymanager);
        });

        $container->set(Logger::class, function (Container $c) {
            return Mockery::mock('Monolog\Logger');
        });

        $this->container = $container;
    }

    public function testCreateInstanceOfCustomersController()
    {
        $repository = $this->container->get(CustomersRepository::class);
        $abstractControllerObject = new CustomersController($this->container, $repository);
        $this->assertInstanceOf('PaymentApi\Controller\CustomersController', $abstractControllerObject);
    }
}
