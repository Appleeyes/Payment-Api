<?php

namespace PaymentApi\Controller;


use PaymentApi\Model\Methods;
use PaymentApi\Repository\MethodsRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @OA\Info(
 *     title="Payment API",
 *     version="1.0",
 *     description="API for managing payment methods.",
 * )
 */

/**
 * @OA\Server(
 *     url="http://localhost:4000",
 *     description="Payment API Server"
 * )
 */

final class MethodsController extends A_Controller
{
    private MethodsRepository $methodsRepository;

    public function __construct(ContainerInterface $container, MethodsRepository $methodsRepository)
    {
        parent::__construct($container);
        $this->methodsRepository = $methodsRepository;
    }

    /**
     * @OA\Get(
     *     path="/v1/methods",
     *     tags={"Methods"},
     *     summary="Retrieve a list of payment methods",
     *     operationId="getMethods",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Methods list retrieved."),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="isActive", type="boolean")
     *             ))
     *         )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No payment methods found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No payment methods found")
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
        $methods = $this->methodsRepository->findAll();

        if (empty($methods)) {
            $this->logger->info('No payment methods found.', ['status_code' => 404]);
            $data = ['message' => 'No payment methods found'];
            $statusCode = 404;
        } else {
            $responseData = [];
            foreach ($methods as $method) {
                $responseData[] = [
                    'id' => $method->getId(),
                    'name' => $method->getName(),
                    'isActive' => $method->getIsActive(),
                ];
            }

            $this->logger->info('Payment Methods list retrieved.', ['status_code' => 200]);
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
     *     path="/v1/methods",
     *     tags={"Methods"},
     *     summary="Create a new payment method",
     *     operationId="createMethods",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Credit Card"),
     *             @OA\Property(property="isActive", type="boolean", example="true")
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Payment Method created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Method created successfully")
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
     *         description="Error creating payment method",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error creating payment method")
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

        if (!$data || empty($data['name'])) {
            $this->logger->info('Invalid Data.', ['statusCode' => 404]);
            $response->getBody()->write(json_encode(['message' => 'Invalid data']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $method = new Methods();
        $method->setName($data['name']);
        $method->setIsActive('isActive');

        try {
            $this->methodsRepository->store($method);

            $this->logger->info('Payment Method created.', ['method_id' => $method->getId()]);

            $response->getBody()->write(json_encode(['message' => 'Payment Method created successfully', 'method_id' => $method->getId()]));
            return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $this->logger->error('Error creating payment method: ' . $e->getMessage());
            $response->getBody()->write(json_encode(['message' => 'Error payment creating method']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/methods/{id}",
     *     tags={"Methods"},
     *     summary="Delete a payment method",
     *     operationId="removemethods",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payment method to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Payment Method Deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Method Deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Payment Method Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Method Not Found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function removeAction(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        $method = $this->methodsRepository->findById($id);

        if (!$method) {
            $this->logger->info('Payment Method Not Found.', ['statusCode' => 404]);
            $response->getBody()->write(json_encode(['message' => 'Payment Method Not Found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $this->methodsRepository->remove($method);
        $this->logger->info('Payment Method deleted.', ['statusCode' => 200]);
        $response->getBody()->write(json_encode(['message' => 'Payment Method Deleted']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/v1/methods/deactivate/{id}",
     *     tags={"Methods"},
     *     summary="Deactivate a payment method",
     *     operationId="deactivateMethods",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payment method to deactivate",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Payment Method Deactivated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Method Deactivated")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Payment Method Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Method Not Found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function deactivateAction(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        // Retrieve the method by ID
        $method = $this->methodsRepository->findById($id);

        if (!$method) {
            $this->logger->info('Payment Method Not Found.', ['statusCode' => 404]);
            $response->getBody()->write(json_encode(['message' => 'Payment Method Not Found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $method->setIsActive(false);
        $this->methodsRepository->update($method);

        $this->logger->info('Payment Method Deactivated.', ['statusCode' => 200]);
        $response->getBody()->write(json_encode(['message' => 'Payment Method Deactivated']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    /**
     * @OA\Get(
     *     path="/v1/methods/reactivate/{id}",
     *     tags={"Methods"},
     *     summary="Reactivate a payment method",
     *     operationId="reactivateMethods",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payment method to reactivate",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Payment Method Reactivated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Method Reactivated")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Payment Method Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Method Not Found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function reactivateAction(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        // Retrieve the method by ID
        $method = $this->methodsRepository->findById($id);

        if (!$method) {
            $this->logger->info('Payment Method Not Found.', ['statusCode' => 404]);
            $response->getBody()->write(json_encode(['message' => 'Payment Method Not Found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $method->setIsActive(true);
        $this->methodsRepository->update($method);

        $this->logger->info('Payment Method Reactivated.', ['statusCode' => 200]);
        $response->getBody()->write(json_encode(['message' => 'Payment Method Reactivated']));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    /**
     * @OA\Put(
     *     path="/v1/methods/{id}",
     *     tags={"Methods"},
     *     summary="Update a payment method",
     *     operationId="updateMethods",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the payment method to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Credit Card"),
     *             @OA\Property(property="isActive", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Payment Method Updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Method Updated")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Payment Method Not Found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment Method Not Found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function updateAction(Request $request, Response $response, $args): Response
    {
        $id = $args['id'];

        $method = $this->methodsRepository->findById($id);

        if (!$method) {
            $this->logger->info('Payment Method Not Found.', ['statusCode' => 404]);
            $response->getBody()->write(json_encode(['message' => 'Payment Method Not Found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $data = json_decode($request->getBody(), true);

        $method->setName($data['name']);
        $method->setIsActive($data['isActive']);

        $this->methodsRepository->update($method);

        $this->logger->info('Payment Method Updated.', ['statusCode' => 200]);
        $response->getBody()->write(json_encode(['message' => 'Payment Method Updated']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}
