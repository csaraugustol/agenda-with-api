<?php

namespace App\Services\Responses;

use Exception;
use App\Services\Responses\InternalError;

class ServiceResponse
{
    /**
     * @var bool
     */
    public $success;

    /**
     * @var string
     */
    public $message;

    /**
     * @var mixed
     */
    public $data;

    /**
     * lista de codigos de erros interno (Regra de negócio)
     * @var array<InternalError>
     */
    public $internalErrors;

    /**
     * @param bool        $success
     * @param string|null $message
     * @param mixed       $data
     * @param array       $internalErrors
     */
    public function __construct(
        bool $success,
        string $message,
        $data = null,
        array $internalErrors = []
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->data    = $data;
        $this->internalErrors = $internalErrors;

        if (count($internalErrors) && !$internalErrors[0] instanceof InternalError) {
            throw new Exception('Error inserido não é do tipo InternalError');
        }
    }

    /**
     * Retorna um array tendo as propiedades da classe
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data'    => $this->data,
            'internalErrors' => array_map(function ($internalError) {
                return $internalError->toArray();
            }, $this->internalErrors),
        ];
    }
}
