<?php

namespace Tests\Feature;

use App\Models\User;

class VexpensesTest extends BaseTestCase
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
     * Retorna sucesso ao realizar a vinculação de um token para acessar a
     * integração com o VExpenses
     */
    public function testReturnSuccessGenerateNewtokenToAccess()
    {
        $body = [
            'token' => $this->faker->sha1
        ];

        $response = $this->postJson(route('vexpenses.access-token'), $body);

        $response->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('vexpenses.access-token'),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'token'      => $response['data']['token'],
                    'system'     => $response['data']['system']
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar realizar a vinculação de um token para acessar a
     * integração com o VExpenses mas esse token não é informado
     */
    public function testReturnErrorTokenToAccessDoesntIsInformed()
    {
        $this->postJson(route('vexpenses.access-token'), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.access-token'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar realizar a vinculação de um token de acesso ao
     * VExpenses com um usuário não autorizado
     */
    public function testReturnErrorWhenGenerateNewtokenToAccessAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->get(route('vexpenses.access-token'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.access-token'),
                'method'  => 'GET',
                'code'    => 401,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 6
                    ],
                ],
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertUnauthorized();
    }
}
