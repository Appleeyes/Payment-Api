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



}
