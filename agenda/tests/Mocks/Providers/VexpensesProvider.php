<?php

namespace Tests\Mocks\Providers;

use Carbon\Carbon;
use Tests\Mocks\Providers\BaseProvider;

class VexpensesProvider extends BaseProvider
{
    /**
     * Dados de membros retornando a resposta da API VExpenses
     *
     * Retorna sucesso ao realizar a busca pelos membros
     *
     * @param string $route
     *
     * @return object
     */
    public function getMockReturnAllMembers(): object
    {
        return (object) [
            'status_code' => 200,
            'response'    => (object)[
                'id'                      => $this->faker->numberBetween(1, 100),
                'integration_id'          => $this->faker->numberBetween(1, 100),
                'external_id'             => $this->faker->uuid,
                'company_id'              => $this->faker->numberBetween(1, 999),
                'role_id'                 => $this->faker->numberBetween(1, 999),
                'approval_flow_id'        => $this->faker->numberBetween(1, 999),
                'expense_limit_policy_id' => $this->faker->numberBetween(1, 999),
                'user_type'               => $this->faker->randomElement(['ADMINISTRADOR', 'USUARIO']),
                'name'                    => $this->faker->name,
                'email'                   => $this->faker->email,
                'cpf'                     => $this->faker->cpf,
                'phone1'                  => $this->faker->phoneNumber,
                'phone2'                  => $this->faker->phoneNumber,
                'birth_date'              => $this->faker->regexify('[0-9]{8}'),
                'bank'                    => $this->faker->bank,
                'agency'                  => $this->faker->randomNumber(5),
                'account'                 => $this->faker->randomNumber(10),
                'parameters'              => [
                    'field1' => $this->faker->word,
                    'field2' => $this->faker->word,
                ],
                'confirmed'               => true,
                'active'                  => true,
                'created_at'              => Carbon::createFromFormat('Y-m-d', $this->faker->date()),
                'updated_at'              => Carbon::createFromFormat('Y-m-d', $this->faker->date()),
            ]
        ];
    }

    /**
     * Retorna erro quando o CEP informado é inválido
     */
    public function getMockResponseErrorAPIViaCep(): object
    {
        return (object) [
            'status_code' => 200,
            'response'   => (object) [
                'erro' => true,
            ]
        ];
    }

    /**
     * Retorna erro quando o formato do CEP que foi informado é inválido
     */
    public function getMockResponseWhenRequestError()
    {
        return (object) [
            'status_code' => 400,
            'response'    => null,
        ];
    }
}
