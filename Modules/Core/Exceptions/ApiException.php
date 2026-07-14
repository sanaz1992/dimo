<?php

namespace Modules\Core\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected ?array $errors;

    public function __construct(
        string $message,
        int $status = 400,
        ?array $errors = null
    ) {
        parent::__construct($message, $status);
        $this->errors = $errors;
    }

    public function getStatus(): int
    {
        return $this->getCode();
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
