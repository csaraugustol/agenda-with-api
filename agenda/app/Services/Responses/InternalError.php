<?php

namespace App\Services\Responses;

class InternalError
{
    /**
     * Código interno do erro
     * @var int|null
     */
    public $code;

    /**
     * Mensagem de retorno com tradução
     * @var string
     */
    public $message;

    /**
     * @param string   $message
     * @param int|null $code
     */
    public function __construct(
        string $message,
        int $code = null
    ) {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * Retorna os parametros dessa classe em array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'code'    => $this->code,
            'message' => $this->message,
        ];
    }
}
