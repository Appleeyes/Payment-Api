<?php

namespace PaymentApi\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

/**
 * JwtMiddleware
 */
class JwtMiddleware
{    
    /**
     * Method __invoke
     *
     * @param Request $request [explicite description]
     * @param RequestHandler $handler [explicite description]
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $SecretKey = $_ENV['JWT_SECRET_KEY'];

        $header = apache_request_headers();
        if (isset($header['Authorization'])) {
            $token = $header['Authorization'];

            try {
                $decoded = JWT::decode($token, new Key($SecretKey, 'HS256'));
            } catch (\Exception $e) {
                return new SlimResponse(401);
            }
        } else {
            return new SlimResponse(401);
        }

        return $handler->handle($request);
    }
}