<?php declare(strict_types=1);

namespace Microparts\Support\Middleware;

use ErrorException;
use Igni\Network\Exception\HttpException;
use Igni\Network\Http\Response;
use Microparts\Support\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * Class ErrorMiddleware
 *
 * @package App
 */
final class ErrorMiddleware implements MiddlewareInterface
{
    /**
     * @see MiddlewareInterface::process
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $this->setErrorHandler();

        try {
            $response = $next->handle($request);

        } catch (Throwable $exception) {
            if ($exception instanceof HttpException) {
                $response = $exception->toResponse();
                return $this->format($exception->getCode(), $response->getBody()->getContents(), $response->getStatusCode());
            } elseif ($exception instanceof ValidationException) {
                return $this->validationFormat($exception);
            } else {
                return $this->format($exception->getCode(), $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $this->restoreErrorHandler();

        return $response;
    }

    /**
     * @param int|null $code
     * @param null|string $message
     * @param int|null $statusCode
     * @return \Igni\Network\Http\Response
     */
    private function format(?int $code, ?string $message, ?int $statusCode = 500)
    {
        $array = [
            'error' => [
                'code'        => $code,
                'message'     => $message,
                'status_code' => $statusCode,
            ]
        ];

        return Response::asJson($array, $statusCode);
    }


    /**
     * @param \Microparts\Support\Validation\ValidationException $exception
     * @return \Igni\Network\Http\Response
     */
    private function validationFormat(ValidationException $exception)
    {
        $array = [
            'error' => [
                'code'        => $exception->getCode(),
                'message'     => $exception->getMessage(),
                'status_code' => $exception->getStatusCode(),
                'validation'  => $exception->getErrors()->toArray()
            ]
        ];

        return Response::asJson($array, $exception->getStatusCode());
    }

    private function setErrorHandler(): void
    {
        set_error_handler(function (int $number, string $message, string $file, int $line) {

            if ( ! (error_reporting() & $number)) {
                return;
            }

            throw new ErrorException($message, 0, $number, $file, $line);
        });
    }

    private function restoreErrorHandler(): void
    {
        restore_error_handler();
    }
}
