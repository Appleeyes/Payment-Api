<?php 

namespace PaymentApi\Middleware;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Psr7\Request;
use Throwable;

final class ErrorHandler
{
    public function __construct(private App $app)
    {
        
    }

    public function __invoke(Request $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails, ?LoggerInterface $logger = null)
    {
        $logger?->error($exception->getMessage());
        if($exception instanceof ORMException)
        {
            $payload = [];
            $statusCode = 500;
        }elseif($exception instanceof OptimisticLockException)
        {
            $payload = [];
            $statusCode = 500;
        }


        $payload = [];
        $statusCode = 404;
        $response = $this->app->getResponseFactory()->createResponse();
        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE)
        );
        return $response->withStatus($statusCode);
    }
}