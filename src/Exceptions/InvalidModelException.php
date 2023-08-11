<?php

namespace Fourfortymedia\EloquentModelGuard\Exception;

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
    public function __construct(protected array $messages = [], int $code = 0, ?Throwable $previous = null)
    {
        $class = get_called_class();
        parent::__construct("Error occurred while validating model [$class]", $code, $previous);
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
      return $this->messages;
    }

}