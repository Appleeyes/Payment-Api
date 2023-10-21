<?php

namespace PaymentApi\Controller;

use DI\Container;
use Laminas\Diactoros\Response\JsonResponse;
use Monolog\Logger;
use PaymentApi\Exception\DatabaseException;
use PaymentApi\Model\Methods;
use PaymentApi\Repository\MethodsRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MethodsController extends A_Controller
{
    private MethodsRepository $methodsRepository;

    public function __construct(ContainerInterface $container, MethodsRepository $methodsRepository)
    {
        parent::__construct($container);
        $this->methodsRepository = $methodsRepository;
    }
        
    /**
     * Method indexAction
     *
     * @param Request $request [explicite description]
     * @param Response $response [explicite description]
     *
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
     * Method createAction
     *
     * @param Request $request [explicite description]
     * @param Response $response [explicite description]
     *
     * @return ResponseInterface
     */
    public function createAction(Request $request, Response $response): ResponseInterface
    {
        $data = $request->getParsedBody();

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
     * Method removeAction
     *
     * @param Request $request [explicite description]
     * @param Response $response [explicite description]
     * @param $args $args [explicite description]
     *
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
     * Method deactivateAction
     *
     * @param Request $request [explicite description]
     * @param Response $response [explicite description]
     * @param $args $args [explicite description]
     *
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
}

