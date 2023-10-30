<?php

use DI\Container;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use PaymentApi\Controller\CustomersController;
use PaymentApi\Model\Customers;
use PaymentApi\Repository\CustomersRepository;
use PaymentApi\Repository\CustomersRepositoryDoctrine;
use PaymentApiTests\A_ControllerTest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CustomersControllerTest extends A_ControllerTest
{
    private $repository;
    private $logger;
    private $controller;
    private $container;
    
    /**
     * Method setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $repository = $this->container->get(CustomersRepository::class);
        $this->controller = new CustomersController($this->container, $repository);

        $this->repository = $repository;
        $this->logger = $this->container->get(Logger::class);
    }
    
    /**
     * Method testIndexAction
     *
     * @return void
     */
    public function testIndexAction()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $expectedData = [
            new Customers(1, 'John', 'Doe', 'johndoe@example.com', true),
            new Customers(2, 'Jane', 'Doe', 'janedoe@example.com', false),
        ];

        $this->repository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($expectedData);

        $this->logger
            ->expects($this->once())
            ->method('info');

        $result = $this->controller->indexAction($request, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(json_encode($expectedData), $result->getBody()->getContents());
    }
    
    /**
     * Method testCreateAction
     *
     * @return void
     */
    public function testCreateAction()
    {
        $requestData = [
            "firstName" => "John",
            "lastName" => "Doe",
            "email" => "johndoe@example.com",
            "isActive" => true,
        ];

        $request = $this->container->get(ServerRequestInterface::class);
        $response = $this->container->get(ResponseInterface::class);

        $customer = new Customers();
        $this->repository
            ->expects($this->once())
            ->method('store')
            ->willReturn($customer);

        $result = $this->controller->createAction($request, $response);

        $this->assertEquals(200, $result->getStatusCode());

        $expectedResponse = ['message' => 'Customer created successfully', 'customer_id' => $customer->getId()];
        $this->assertEquals(json_encode($expectedResponse), $result->getBody()->getContents());
    }

    
    /**
     * Method testRemoveAction
     *
     * @return void
     */
    public function testRemoveAction()
    {
        $customerId = 123;

        $request = $this->createMock(Request::class);
        $request
            ->method('getAttribute')
            ->with('id')
            ->willReturn($customerId);

        $response = $this->createMock(Response::class);

        $customer = new Customers();
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($customerId)
            ->willReturn($customer);

        $this->repository
            ->expects($this->once())
            ->method('remove')
            ->with($customer);

        $result = $this->controller->removeAction($request, $response, ['id' => $customerId]);

        $this->assertEquals(200, $result->getStatusCode());

        $expectedResponse = ['message' => 'Customer Deleted'];
        $this->assertEquals(json_encode($expectedResponse), $result->getBody());
    }
    
    /**
     * Method testDeactivateAction
     *
     * @return void
     */
    public function testDeactivateAction()
    {
        $customerId = 123;

        $request = $this->createMock(Request::class);
        $request
            ->method('getAttribute')
            ->with('id')
            ->willReturn($customerId);

        $response = $this->createMock(Response::class);

        $customer = new Customers();
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($customerId)
            ->willReturn($customer);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($customer);

        $result = $this->controller->deactivateAction($request, $response, ['id' => $customerId]);

        $this->assertEquals(200, $result->getStatusCode());

        $expectedResponse = ['message' => 'Customer Deactivated'];
        $this->assertEquals(json_encode($expectedResponse), $result->getBody());
    }

    
    /**
     * Method testReactivateAction
     *
     * @return void
     */
    public function testReactivateAction()
    {
        $customerId = 123;

        $request = $this->createMock(Request::class);
        $request
            ->method('getAttribute')
            ->with('id')
            ->willReturn($customerId);

        $response = $this->createMock(Response::class);

        $customer = new Customers();
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($customerId)
            ->willReturn($customer);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($customer);

        $result = $this->controller->reactivateAction($request, $response, ['id' => $customerId]);

        $this->assertEquals(200, $result->getStatusCode());

        $expectedResponse = ['message' => 'Customer Reactivated'];
        $this->assertEquals(json_encode($expectedResponse), $result->getBody());
    }
    
    /**
     * Method testUpdateAction
     *
     * @return void
     */
    public function testUpdateAction()
    {
        $customerId = 123;

        $request = $this->createMock(Request::class);
        $request
            ->method('getAttribute')
            ->with('id')
            ->willReturn($customerId);

        $updatedCustomerData = [
            'firstName' => 'UpdatedFirstName',
            'lastName' => 'UpdatedLastName',
            'email' => 'updated@email.com',
            'isActive' => true,
        ];
        $request
            ->method('getParsedBody')
            ->willReturn($updatedCustomerData);

        $response = $this->createMock(Response::class);

        $customer = new Customers();
        $this->repository
            ->expects($this->once())
            ->method('findById')
            ->with($customerId)
            ->willReturn($customer);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($customer);

        $result = $this->controller->updateAction($request, $response, ['id' => $customerId]);

        $this->assertEquals(200, $result->getStatusCode());

        $expectedResponse = ['message' => 'Customer Updated'];
        $this->assertEquals(json_encode($expectedResponse), $result->getBody());
    }
}
