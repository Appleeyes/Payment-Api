<?php

namespace PaymentApi\Controller;

use Laminas\Diactoros\Response\JsonResponse;
use PaymentApi\Exception\DatabaseException;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class MethodsController
{
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
            $pdo = new PDO('', '', '');

            // throw new DatabaseException('Database Exception message: Error!', 500);
        
        return new JsonResponse(['message' => 'test'], 200);
    }
}

