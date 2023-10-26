<?php

namespace PaymentApi\Controller;

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

    public function __construct(ContainerInterface $container, PaymentsRepository $paymentsRepository)
    {
        parent::__construct($container);
        $this->paymentsRepository = $paymentsRepository;
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
}
