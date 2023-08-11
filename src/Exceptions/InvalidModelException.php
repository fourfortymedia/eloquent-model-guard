<?php

namespace FourFortyMedia\EloquentModelGuard\Exceptions;

use Exception;

/**
 *
 */
class InvalidModelException extends Exception{
    /**
     * Construct the exception. Note: The message is NOT binary safe.
     * @link https://php.net/manual/en/exception.construct.php
     * @param string         $message  [optional] The Exception message to throw.
     * @param int            $code     [optional] The Exception code.
     * @param null|Throwable $previous [optional] The previous throwable used for the exception chaining.
     */
    public function __construct(protected array|string $messages, int $code = 0, ?Throwable $previous = null)
    {
        $this->messages = is_string($messages) ? [$messages] : $this->messages;

        parent::__construct(is_string($messages) ? $messages : "Error occurred while validating model", $code, $previous);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
      return $this->messages;
    }

}