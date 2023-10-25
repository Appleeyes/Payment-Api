<?php

namespace PaymentApi\Controller;


use PaymentApi\Model\customers;
use PaymentApi\Repository\CustomersRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @OA\Info(
 *     title="Payment API",
 *     version="1.0",
 *     description="API for managing payment customers.",
 * )
 */

/**
 * @OA\Server(
 *     url="http://localhost:4000",
 *     description="Payment API Server"
 * )
 */

final class CustomersController extends A_Controller
{
    private CustomersRepository $customersRepository;

    public function __construct(ContainerInterface $container, CustomersRepository $customersRepository)
    {
        parent::__construct($container);
        $this->customersRepository = $customersRepository;
    }

    /**
     * @OA\Get(
     *     path="/v1/customers",
     *     tags={"Customers"},
     *     summary="Retrieve a list of customers",
     *     operationId="getCustomers",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Customers list retrieved."),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="firstName", type="string"),
     *                 @OA\Property(property="lastName", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="isActive", type="boolean")
     *             ))
     *         )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No customers found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No customers found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        $customers = $this->customersRepository->findAll();

        if (empty($customers)) {
            $this->logger->info('No customer found.', ['status_code' => 404]);
            $data = ['message' => 'No customer found'];
            $statusCode = 404;
        } else {
            $responseData = [];
            foreach ($customers as $customer) {
                $responseData[] = [
                    'id' => $customer->getId(),
                    'firstName' => $customer->getFirstName(),
                    'lastName' => $customer->getLastName(),
                    'email' => $customer->getEmail(),
                    'isActive' => $customer->getIsActive(),
                ];
            }

            $this->logger->info('Customer list retrieved.', ['status_code' => 200]);
            $data = $responseData;
            $statusCode = 200;
        }

        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }


    /**
     * @OA\Post(
     *     path="/v1/customers",
     *     tags={"Customers"},
     *     summary="Create a new customer",
     *     operationId="createCustomer",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="firstName", type="string", example="John"),
     *             @OA\Property(property="lastName", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", example="JohnDoe@email.com"),
     *             @OA\Property(property="isActive", type="boolean", example="true")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Customer created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Customer created successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid data")
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Error creating customer",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error creating customer")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function createAction(Request $request, Response $response): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $contentType = $request->getHeaderLine('Content-Type');

        if (empty($parsedBody)) {
            $jsonBody = json_decode($request->getBody()->getContents(), true);
            if (!empty($jsonBody)) {
                $data = $jsonBody;
            } else {
                $this->logger->info('Invalid Data.', ['statusCode' => 400]);
                $response->getBody()->write(json_encode(['message' => 'Invalid data']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        } else {
            $data = $parsedBody;
        }

        if (empty($data['firstName']) || empty($data['lastName']) || empty($data['email'])) {
            $this->logger->info('Invalid Data.', ['statusCode' => 400]);
            $response->getBody()->write(json_encode(['message' => 'Invalid data']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $customer = new Customers();
        $customer->setFirstName($data['firstName']);
        $customer->setLastName($data['lastName']);
        $customer->setEmail($data['email']);
        $customer->setIsActive('isActive');

        try {
            $this->customersRepository->store($customer);

            $this->logger->info('Customer created.', ['customer_id' => $customer->getId()]);

            $response->getBody()->write(json_encode(['message' => 'Customer created successfully', 'customer_id' => $customer->getId()]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $this->logger->error('Error creating customer: ' . $e->getMessage());
            $response->getBody()->write(json_encode(['message' => 'Error creating customer']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }



    
}
