<?php
/**
 * Created by Roquie.
 * E-mail: roquie0@gmail.com
 * GitHub: Roquie
 * Date: 25/10/2018
 */

namespace Microparts\Igni\Support\Validation;


use Rakit\Validation\ErrorBag;

class ValidationException extends \Exception
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var \Throwable
     */
    private $previous;

    /**
     * @var ErrorBag
     */
    private $errors;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     *
     * @link https://php.net/manual/en/exception.construct.php
     * @param ErrorBag $errors
     * @param int $statusCode
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param \Throwable $previous [optional] The previous throwable used for the exception chaining.
     * @since 5.1.0
     */
    public function __construct(ErrorBag $errors, $statusCode = 422, $message = 'Validation error.', $code = 0, \Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->code = $code;
        $this->previous = $previous;
        $this->errors = $errors;
    }

    /**
     * @return ErrorBag
     */
    public function getErrors(): ErrorBag
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
