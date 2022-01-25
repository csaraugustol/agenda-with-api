<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ChangePassword;
use App\Models\AuthenticateToken;

class UserTest extends BaseTestCase
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
     * Retorna sucesso ao realizar o registro de um novo usuário no sistema
     */
    public function testReturnSuccessWhenRegisterNewUser()
    {
        $password = $this->faker->password;
        $body = [
            'name'             => $this->faker->name,
            'email'            => $this->faker->email,
            'password'         => $password,
            'confirm_password' => $password
        ];

        $this->postJson(route('users.register'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.register'),
                'method'  => 'POST',
                'code'    => 200,
            ], true);
    }

    /**
     * Retorna erro ao tentar registrar um usuário informando senha e senha de
     * confirmação diferentes
     */
    public function testReturnErrorWhenRegisterNewUserWithDifferentPasswords()
    {
        $body = [
            'name'             => $this->faker->name,
            'email'            => $this->faker->email,
            'password'         => $this->faker->password,
            'confirm_password' => $this->faker->password
        ];

        $this->postJson(route('users.register'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('users.register'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => null
            ]);
    }

    /**
     * Retorna erro ao tentar registrar um usuário com um email já cadastrado
     */
    public function testReturnErrorWhenRegisterNewUserWithEqualsEmail()
    {
        $user = factory(User::class)->create();
        $body = [
            'name'             => $this->faker->name,
            'email'            => $user->email,
            'password'         => $this->faker->password,
            'confirm_password' => $this->faker->password
        ];

        $this->postJson(route('users.register'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('users.register'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => null
            ]);
    }

    /**
     * Retorna sucesso ao permitir login no sistema
     */
    public function testLoginReturnSuccess()
    {
        $password = $this->faker->password;
        $user = factory(User::class)->create([
            'password' => bcrypt($password),
        ]);

        $body = [
            'email'    => $user->email,
            'password' => $password
        ];

        $this->postJson(route('users.login'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.login'),
                'method'  => 'POST',
                'code'    => 200,
            ], true);
    }

    /**
     * Retorna erro ao tentar logar com uma senha diferente da cadastrada
     */
    public function testLoginReturnErrorWhenPasswordIsIncorrect()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt($this->faker->password),
        ]);

        $body = [
            'email'    => $user->email,
            'password' => $this->faker->password
        ];

        $this->postJson(route('users.login'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('users.login'),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 1,
                    ],
                ],
            ], true) ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar logar com um e-mail diferente do cadastrado
     */
    public function testLoginReturnErrorWhenEmailIsIncorrect()
    {
        $password = $this->faker->password;
        factory(User::class)->create([
            'password' => bcrypt($password),
        ]);

        $body = [
            'email'    => $this->faker->email,
            'password' => $password
        ];

        $this->postJson(route('users.login'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('users.login'),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 1,
                    ],
                ],
            ], true) ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna sucesso ao chamar a rota de atualização de dados do usuário
     * com autenticação
     */
    public function testReturnSuccessWhenUpdatedUserAuthenticated()
    {
        $this->patchJson(route('users.update'), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.update'),
                'method'  => 'PATCH',
                'code'    => 200,
                'data'    => [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ],
            ], true);
    }

    /**
     * Retorna erro ao chamar a rota de atualização de dados do usuário
     * sem um token devidamente autenticado
     */
    public function testReturnErrorWhenUpdatedUserAuthenticatedAndTryChangePassword()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->patchJson(route('users.update'), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('users.update'),
                'method'  => 'PATCH',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ]);
    }

    /**
     * Retorna sucesso ao realizar busca dos contatos do usuário com autenticação
     */
    public function testReturnSuccessWhenListAllContactsOfUserAndIsAuthenticated()
    {
        $this->get(route('contacts.index'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.index'),
                'method'  => 'GET',
                'code'    => 200,
            ], true);
    }

    /**
     * Retorna erro ao realizar busca dos contatos do usuário sem um token
     * devidamente autenticado
     */
    public function testReturnErrorWhenListAllContactsOfUserAndNotAuthenticated()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->get(route('contacts.index'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.index'),
                'method'  => 'GET',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ]);
    }

    /**
     * Retorna sucesso ao realizar logout do usuário
     */
    public function testReturnSuccessWhenUserTryLogout()
    {
        $this->get(route('users.logout'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.logout'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => null,
            ], true);
    }

    /**
     * Retorna erro ao realizar logout do usuário sem um token
     * devidamente autenticado
     */
    public function testReturnErrorWhenUserTryLogout()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->get(route('users.logout'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('users.logout'),
                'method'  => 'GET',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ]);
    }

    /**
     * Retorna sucesso ao realizar busca de dados do usuário
     */
    public function testShowReturnSuccessWhenFindUser()
    {
        $this->get(route('users.show'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.show'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'email' => $this->user->email,
                ],
            ], true);
    }

    /**
     * Retorna erro ao realizar busca de dados do usuário sem um token
     * devidamente autenticado
     */
    public function testShowReturnErrorWhenFindUser()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->get(route('users.show'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('users.show'),
                'method'  => 'GET',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ]);
    }

    /**
     * Retorna sucesso ao criar um token específico para alteração da senha
     * do usuário
     */
    public function testChangePasswordReturnSuccessWhenCreateToken()
    {
        $this->get(route('users.change-password'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.change-password'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'token' => $this->user->changePasswords->first()->token,
                ],
            ], true);
    }

    /**
     * Retorna sucesso ao atualizar a senha do usuário
     */
    public function testChangePasswordReturnSuccessWhenChangePassword()
    {
        $password = $this->faker->password;
        $newPassword = $this->faker->password;

        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        $authenticateToken = factory(AuthenticateToken::class)->create([
            'user_id' => $user->id
        ]);

        $this->withHeaders(['Authorization' => 'Bearer ' . $authenticateToken->token]);

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $body = [
            'current_password'      => $password,
            'new_password'          => $newPassword,
            'confirm_new_password'  => $newPassword,
            'token_update_password' => $changePasswordToken->token,
        ];

        $this->postJson(route('users.change-password'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.change-password'),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ], true);
    }

    /**
     * Retorna erro ao atualizar a senha do usuário quando o token de permissão
     * é inválido
     */
    public function testChangePasswordReturnErrorWhenChangePasswordWithoutToken()
    {
        $password = $this->faker->password;
        $newPassword = $this->faker->password;

        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        $authenticateToken = factory(AuthenticateToken::class)->create([
            'user_id' => $user->id
        ]);

        $this->withHeaders(['Authorization' => 'Bearer ' . $authenticateToken->token]);

        $body = [
            'current_password'      => $password,
            'new_password'          => $newPassword,
            'confirm_new_password'  => $newPassword,
            'token_update_password' => $this->faker->sha1,
        ];

        $this->postJson(route('users.change-password'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('users.change-password'),
                'method'  => 'POST',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 18
            ]);
    }

    /**
     * Retorna erro ao atualizar a senha do usuário quando o token de permissão
     * já foi expirado
     */
    public function testChangePasswordReturnErrorWhenTokenExpired()
    {
        $password = $this->faker->password;
        $newPassword = $this->faker->password;

        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        $authenticateToken = factory(AuthenticateToken::class)->create([
            'user_id' => $user->id
        ]);

        $this->withHeaders(['Authorization' => 'Bearer ' . $authenticateToken->token]);

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $user->id,
            'expires_at' => Carbon::now()->subMinutes(5)
        ]);

        $body = [
            'current_password'      => $password,
            'new_password'          => $newPassword,
            'confirm_new_password'  => $newPassword,
            'token_update_password' => $changePasswordToken->token,
        ];

        $this->postJson(route('users.change-password'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('users.change-password'),
                'method'  => 'POST',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 19
            ]);
    }
}
