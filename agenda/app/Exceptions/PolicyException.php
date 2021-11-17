<?php

namespace App\Exceptions;

use Exception;

/**
 * Exception que trata validação de dados
 * com base nas políticas internas
 */
class PolicyException extends Exception
{
    public function __construct(string $message, $code)
    {
        parent::__construct($message, $code);
    }
}
