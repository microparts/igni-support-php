<?php declare(strict_types=1);

namespace Microparts\Igni\Support\Middleware;

use Igni\Network\Exception\HttpException;
use Igni\Network\Http\Response;
use Microparts\Igni\Support\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * Class ErrorHandlerMiddleware
 *
 * @package App
 */
final class ErrorHandlerMiddleware implements MiddlewareInterface
{
    /**
     * @see MiddlewareInterface::process
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $next
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
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

        return $response;
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
                'code'        => $code,
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
