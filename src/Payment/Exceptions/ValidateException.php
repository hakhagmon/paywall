<?php

namespace App\Payment\Exceptions;

use Exception;

class ValidateException extends Exception
{
    private array $errors;

    public function __construct(array $responseData)
    {
        $this->errors = $responseData;
        parent::__construct('validate data exception', 400, null);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
