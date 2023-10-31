<?php

use PaymentApi\Controller\CustomersController;
use PaymentApi\Repository\CustomersRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;
use PaymentApi\Model\Customers;
use PaymentApi\Tests\A_ControllerTest;
use Psr\Http\Message\StreamInterface;

/**
 * CustomersControllerTest
 */
class CustomersControllerTest extends A_ControllerTest
{    
    /**
     * Method testIndexAction
     *
     * @return void
     */
    public function testIndexAction()
    {
        $customersRepository = $this->createMock(CustomersRepository::class);
        $customersRepository->method('findAll')->willReturn([]);

        $logger = $this->createMock(Logger::class);
        $logger->expects($this->once())
            ->method('info')
            ->with('No customer found.', ['status_code' => 404]);

        $this->container->set(Logger::class, $logger);

        $request = $this->createMock(ServerRequestInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $stream = $this->createMock(StreamInterface::class);

        $stream->expects($this->once())
            ->method('write')
            ->with(json_encode(['message' => 'No customer found']));

        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $response->expects($this->once())
            ->method('withHeader')
            ->willReturnSelf();

        $response->expects($this->once())
            ->method('withStatus')
            ->willReturnSelf();

        $controller = new CustomersController($this->container, $customersRepository);

        $result = $controller->indexAction($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
    
    /**
     * Method testCreateAction
     *
     * @return void
     */
    public function testCreateAction()
    {
        $customersRepository = Mockery::mock(CustomersRepository::class);

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('info')
        ->with('Customer created.', ['customer_id' => 1]);
        $logger->shouldReceive('error')
        ->with(Mockery::type('string'));

        $this->container->set(Logger::class, $logger);

        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getParsedBody')
        ->andReturn([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john@example.com',
            'isActive' => true,
        ]);
        $request->shouldReceive('getBody')
        ->andReturnSelf();
        $request->shouldReceive('getHeaderLine')
        ->with('Content-Type')
        ->andReturn('application/json');

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')
        ->andReturnSelf();
        $response->shouldReceive('write')
        ->with(Mockery::type('string'));
        $response->shouldReceive('getStatusCode')
        ->andReturn(200);
        $response->shouldReceive('withHeader')
        ->andReturnSelf();
        $response->shouldReceive('withStatus')
            ->andReturnSelf();

        $customer = new Customers();
        $customer->setId(1);
        $customer->setFirstName('John');
        $customer->setLastName('Doe');
        $customer->setEmail('john@example.com');
        $customer->setIsActive(true);

        $customersRepository->shouldReceive('store')
        ->with($customer)
            ->andThrow(new \Exception('Mocked exception message'));

        $controller = new CustomersController($this->container, $customersRepository);

        $result = $controller->createAction($request, $response);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
    
    /**
     * Method testRemoveAction
     *
     * @return void
     */
    public function testRemoveAction()
    {
        $customersRepository = Mockery::mock(CustomersRepository::class);

        $customersRepository->shouldReceive('findById')
        ->with(1)
        ->andReturn(new Customers());

        $customersRepository->shouldReceive('remove')
        ->with(Mockery::type(Customers::class));

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('info')
        ->with('Customer deleted.', ['statusCode' => 200]);

        $this->container->set(Logger::class, $logger);

        $request = Mockery::mock(ServerRequestInterface::class);
        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')
        ->andReturnSelf();
        $response->shouldReceive('write')
        ->with(Mockery::type('string'));
        $response->shouldReceive('withStatus')
        ->andReturnSelf();
        $response->shouldReceive('withHeader')
        ->andReturnSelf();
        $response->shouldReceive('getStatusCode')
        ->andReturn(200);

        $controller = new CustomersController($this->container, $customersRepository);

        $result = $controller->removeAction($request, $response, ['id' => 1]);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
    
    /**
     * Method testDeactivateAction
     *
     * @return void
     */
    public function testDeactivateAction()
    {
        $customersRepository = Mockery::mock(CustomersRepository::class);

        $customerId = 1;

        $customer = Mockery::mock(Customers::class);

        $customersRepository->shouldReceive('findById')
        ->with($customerId)
            ->andReturn($customer);

        $customer->shouldReceive('setIsActive')
        ->with(false);

        $customersRepository->shouldReceive('update')
        ->with($customer);

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('info')
        ->with('Customer Deactivated.', ['statusCode' => 200]);

        $this->container->set(Logger::class, $logger);

        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getAttribute')
        ->with('id')
        ->andReturn($customerId);

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')
        ->andReturnSelf();
        $response->shouldReceive('write')
        ->with(Mockery::type('string'));
        $response->shouldReceive('withStatus')
        ->andReturnSelf();
        $response->shouldReceive('withHeader')
        ->andReturnSelf();
        $response->shouldReceive('getStatusCode')
        ->andReturn(200);

        $controller = new CustomersController($this->container, $customersRepository);

        $result = $controller->deactivateAction($request, $response, ['id' => $customerId]);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
    
    /**
     * Method testReactivateAction
     *
     * @return void
     */
    public function testReactivateAction()
    {
        $customersRepository = Mockery::mock(CustomersRepository::class);

        $customerId = 1;

        $customer = Mockery::mock(Customers::class);

        $customersRepository->shouldReceive('findById')
        ->with($customerId)
            ->andReturn($customer);

        $customer->shouldReceive('setIsActive')
        ->with(true);

        $customersRepository->shouldReceive('update')
        ->with($customer);

        $logger = Mockery::mock(Logger::class);
        $logger->shouldReceive('info')
        ->with('Customer Reactivated.', ['statusCode' => 200]);

        $this->container->set(Logger::class, $logger);

        $request = Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getAttribute')
        ->with('id')
            ->andReturn($customerId);

        $response = Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')
        ->andReturnSelf();
        $response->shouldReceive('write')
        ->with(Mockery::type('string'));
        $response->shouldReceive('withStatus')
        ->andReturnSelf();
        $response->shouldReceive('withHeader')
        ->andReturnSelf();
        $response->shouldReceive('getStatusCode')
        ->andReturn(200);

        $controller = new CustomersController($this->container, $customersRepository);

        $result = $controller->reactivateAction($request, $response, ['id' => $customerId]);

        $this->assertInstanceOf(ResponseInterface::class, $result);
    }
}
