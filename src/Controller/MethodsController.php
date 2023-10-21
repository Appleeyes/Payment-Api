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
            $this->logger->info('No methods found.', ['status_code' => 404]);
            $data = ['message' => 'No methods found'];
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

            $this->logger->info('Methods list retrieved.', ['status_code' => 200]);
            $data = $responseData;
            $statusCode = 200;
        }

        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}

