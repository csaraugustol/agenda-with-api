<?php

namespace Tests\Mocks\Providers;

use Tests\Mocks\Providers\BaseProvider;

class ViaCepProvider extends BaseProvider
{
    /**
     * Dados de um endereço retornando a resposta da API ViaCep
     *
     * Retorna sucesso ao realizar a busca por um CEP
     *
     * @param string $postalCode
     * @return object
     */
    public function getMockPostalCode(string $postalCode): object
    {
        return (object) [
            'status_code' => 200,
            'response'    => (object)[
                'logradouro' => $this->faker->streetName,
                'bairro'     => $this->faker->streetSuffix,
                'localidade' => $this->faker->city,
                'uf'         => $this->faker->state,
                'cep'        => $postalCode,
            ]
        ];
    }

    /**
     * Retorna erro e o código de erro quando o CEP informado é inválido
     */
    public function getMockPostalCodeDoesntExists(): object
    {
        return (object) [
            'status_code' => 200,
            'response'    => (object) [
                'code' => 16
            ]
        ];
    }
}
