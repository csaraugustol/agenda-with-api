<?php

namespace Tests\Feature;

use App\Models\User;

class ExternalTokenTest extends BaseTestCase
{
    /**
     * O usuário solicitante da request
     * @var User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $tokenResponse = $this->generateUserAndToken();

        $this->user = $tokenResponse->user;
        $this->withHeaders(['Authorization' => $tokenResponse->token]);
    }

    /**
     * Retorna sucesso ao realizar a criação de um token para acessar a
     * integração com o VExpenses
     */
    public function testReturnSuccessGenerateNewTokenToAccessVExpenses()
    {
        $response = $this->get(route('vexpenses.generate-access-token'));

        $response->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('vexpenses.generate-access-token'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'token'      => $response['data']['token'],
                    'expires_at' => $response['data']['expires_at'],
                    'system'     => $response['data']['system']
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar realizar a criação de um token de acesso ao
     * VExpenses com um usuário não autorizado
     */
    public function testReturnErrorWhenGenerateNewTokenToAccessVExpensesAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->get(route('vexpenses.generate-access-token'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.generate-access-token'),
                'method'  => 'GET',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ])
            ->assertUnauthorized();
    }
}
