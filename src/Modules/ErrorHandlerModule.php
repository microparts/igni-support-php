<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 */

namespace Microparts\Igni\Support\Modules;

use Igni\Application\Http\MiddlewareAggregator;
use Igni\Application\Providers\MiddlewareProvider;
use Igni\Network\Exception\HttpException;
use Igni\Network\Http\Response;
use Microparts\Igni\Support\Validation\ValidationException;
use PDOException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;

class ErrorHandlerModule implements MiddlewareProvider
{
    /**
     * @param \Igni\Application\Http\MiddlewareAggregator|\Igni\Application\HttpApplication $aggregate
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function provideMiddleware(MiddlewareAggregator $aggregate): void
    {
        /** @var \Illuminate\Container\Container $container */
        $container = $aggregate->getContainer();
        $logger = $container->get(LoggerInterface::class);

        $aggregate->use(function (ServerRequestInterface $request, callable $next) use ($logger) {
            return $this->handle($request, $next, $logger);
        });
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @param \Psr\Log\LoggerInterface $logger
     * @return \Igni\Network\Http\Response
     */
    private function handle(ServerRequestInterface $request, callable $next, LoggerInterface $logger)
    {
        try {
            $response = $next($request);

        } catch (PDOException $e) {
            $this->logError($logger, LogLevel::CRITICAL, $e);
            return $this->format('db', 'Database error.');
        } catch (Throwable $e) {
            $this->logError($logger, LogLevel::ERROR, $e);
            if ($e instanceof HttpException) {
                $response = $e->toResponse();
                return $this->format($e->getCode(), $response->getBody()->getContents(), $response->getStatusCode());
            } elseif ($e instanceof ValidationException) {
                return $this->validationFormat($e);
            } else {
                return $this->format($e->getCode(), $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return $response;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $level
     * @param \Throwable $e
     */
    private function logError(LoggerInterface $logger, string $level, Throwable $e)
    {
        $format = '[%s] %s, file: %s, line: %d';
        $logger->log($level, sprintf($format, $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine()));
    }

    /**
     * @param $code
     * @param null|string $message
     * @param int|null $statusCode
     * @return \Igni\Network\Http\Response
     */
    private function format($code, ?string $message, ?int $statusCode = 500)
    {
        $array = [
            'error' => [
                'code'        => (string) $code,
                'message'     => $message,
                'status_code' => $statusCode,
            ]
        ];

        return Response::asJson($array, $statusCode);
    }

    /**
     * @param \Microparts\Igni\Support\Validation\ValidationException $exception
     * @return \Igni\Network\Http\Response
     */
    private function validationFormat(ValidationException $exception)
    {
        $validation = [];
        foreach ($exception->getErrors()->toArray() as $key => $error) {
            $validation[$key] = array_values($error);
        }

        $array = [
            'error' => [
                'code'        => $exception->getCode(),
                'message'     => $exception->getMessage(),
                'status_code' => $exception->getStatusCode(),
                'validation'  => $validation
            ]
        ];

        return Response::asJson($array, $exception->getStatusCode());
    }
}
