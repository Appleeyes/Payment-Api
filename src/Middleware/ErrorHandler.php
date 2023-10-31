<?php 

namespace PaymentApi\Middleware;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Monolog\Logger;
use PaymentApi\Exception\A_Exception;
use PaymentApi\Exception\DatabaseException;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Request;
use Throwable;

/**
 * ErrorHandler
 */
final class ErrorHandler
{
    private Logger $logger;
    
    /**
     * Method __construct
     *
     * @param private $app [explicite description]
     *
     * @return void
     */
    public function __construct(private App $app)
    {
        $this->logger = $this->app->getContainer()->get(Logger::class);
    }
    
    /**
     * Method __invoke
     *
     * @param Request $request [explicite description]
     * @param Throwable $exception [explicite description]
     * @param bool $displayErrorDetails [explicite description]
     * @param bool $logErrors [explicite description]
     * @param bool $logErrorDetails [explicite description]
     * @param ?LoggerInterface $logger [explicite description]
     *
     * @return void
     */
    public function __invoke(Request $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails, ?LoggerInterface $logger = null)
    {
        $logger?->error($exception->getMessage());
        $statusCode = 500;

        if($exception instanceof ORMException || $exception instanceof HttpNotFoundException || $exception instanceof \PDOException)
        {
            $this->logger->debug($exception->getMessage());
            $statusCode = 500;
        } else if($exception instanceof A_Exception)
        {
            $this->logger->alert($exception->getMessage());
            $statusCode = $exception->getCode();
        }

        $payload = [
            'message' => $exception->getMessage()
        ];

        if ($displayErrorDetails) {
            $payload['details'] = $exception->getMessage();
            $payload['trace'] = $exception->getTrace();
        }

        $response = $this->app->getResponseFactory()->createResponse();
        $response->getBody()->write(
            json_encode($payload, JSON_UNESCAPED_UNICODE)
        );
        return $response->withStatus($statusCode);
    }
}