<?php

namespace PaymentApi\Controller;

use PaymentApi\Model\Payments;
use PaymentApi\Repository\CustomersRepository;
use PaymentApi\Repository\MethodsRepository;
use PaymentApi\Repository\PaymentsRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @OA\Info(
 *     title="Payment API",
 *     version="1.0",
 *     description="API for managing payments transactions.",
 * )
 */

/**
 * @OA\Server(
 *     url="http://localhost:4000",
 *     description="Payment API Server"
 * )
 */
final class PaymentsController extends A_Controller
{
    private PaymentsRepository $paymentsRepository;
    private CustomersRepository $customersRepository;
    private MethodsRepository $methodsRepository;

    public function __construct(
        ContainerInterface $container,
        PaymentsRepository $paymentsRepository,
        CustomersRepository $customerRepository,
        MethodsRepository $methodsRepository
    ) {
        parent::__construct($container);
        $this->paymentsRepository = $paymentsRepository;
        $this->customersRepository = $customerRepository;
        $this->methodsRepository = $methodsRepository;
    }

    /**
     * @OA\Get(
     *     path="/v1/payments",
     *     tags={"Payments"},
     *     summary="Retrieve a list of payments transactions",
     *     operationId="getPayments",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payments transactions list retrieved."),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="customer_id", type="integer"),
     *                 @OA\Property(property="method_id", type="integer"),
     *                 @OA\Property(property="amount", type="float"),
     *                 @OA\Property(property="payment_date", type="date")
     *             ))
     *         )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No payments transactions found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No payments transactions found")
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
        $payments = $this->paymentsRepository->findAll();

        if (empty($payments)) {
            $this->logger->info('No payment transaction found.', ['status_code' => 404]);
            $data = ['message' => 'No payment transaction found'];
            $statusCode = 404;
        } else {
            $responseData = [];
            foreach ($payments as $payment) {
                $responseData[] = [
                    'id' => $payment->getId(),
                    'customer_id' => $payment->getCustomer()->getId(),
                    'method_id' => $payment->getPaymentMethod()->getId(),
                    'amount' => $payment->getAmount(),
                    'payment_date' => $payment->getPaymentDate()->format('Y-m-d H:i:s'),
                ];
            }

            $this->logger->info('Payments transaction list retrieved.', ['status_code' => 200]);
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
     *     path="/v1/payments",
     *     tags={"Payments"},
     *     summary="Create a new payment transaction",
     *     operationId="createPayment",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="customer_id", type="integer"),
     *             @OA\Property(property="method_id", type="integer"),
     *             @OA\Property(property="amount", type="number", format="float"),
     *             @OA\Property(property="payment_date", type="string", format="date"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Payment transaction created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment transaction created successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Invalid data provided",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid data provided."),
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Invalid Customer or Payment method ID",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid customer or payment method ID."),
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

        if (!$data || empty($data['customer_id']) || empty($data['method_id']) || empty($data['amount']) || empty($data['payment_date'])) {
            $this->logger->info('Invalid Data.', ['statusCode' => 400]);
            $response->getBody()->write(json_encode(['message' => 'Invalid data']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $customer = $this->customersRepository->findById($data['customer_id']);
        $method = $this->methodsRepository->findById($data['method_id']);

        if (!$customer) {
            $this->logger->info('Invalid Customer ID.', ['statusCode' => 400]);
            $response->getBody()->write(json_encode(['message' => 'Invalid customer ID']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        } else if (!$method) {
            $this->logger->info('Invalid payment method ID.', ['statusCode' => 400]);
            $response->getBody()->write(json_encode(['message' => 'Invalid payment method ID']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $payment = new Payments();
        $payment->setCustomer($customer);
        $payment->setPaymentMethod($method);
        $payment->setAmount($data['amount']);
        $payment->setPaymentDate(new \DateTime($data['payment_date']));

        try {
            $this->paymentsRepository->store($payment);

            $this->logger->info('Payment transaction created.', ['payment_id' => $payment->getId()]);

            $response->getBody()->write(json_encode(['message' => 'Payment transaction created successfully']));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $this->logger->error('Error creating payment transaction: ' . $e->getMessage());
            $response->getBody()->write(json_encode(['message' => 'Error creating payment transaction']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/payments/{id}",
     *     tags={"Payments"},
     *     summary="Delete a payment transaction by ID",
     *     operationId="removePayment",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the payment transaction to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Payment transaction deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment transaction deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Payment transaction not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment transaction not found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function removeAction(Request $request, Response $response, array $args): ResponseInterface
    {
        $paymentId = (int)$args['id'];

        $payment = $this->paymentsRepository->findById($paymentId);

        if (!$payment) {
            $this->logger->info('Payment transaction not found.', ['status_code' => 404]);
            $response->getBody()->write(json_encode(['message' => 'Payment transaction not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $this->paymentsRepository->remove($payment);
        $this->logger->info('Payment transaction deleted successfully.', ['status_code' => 200]);
        $response->getBody()->write(json_encode(['message' => 'Payment transaction deleted successfully']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}
