<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\Contact;
use App\Models\ChangePassword;

class UserTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
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
        $generateUser = $this->generateUserAndToken();
        $body = [
            'name'             => $this->faker->name,
            'email'            => $generateUser->user->email,
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
        $generateUserAndDecryptedPassword = $this->generateUserAndToken();

        $body = [
            'email'    => $generateUserAndDecryptedPassword->user->email,
            'password' => $generateUserAndDecryptedPassword->password
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
        $generateUser = $this->generateUserAndToken();

        $body = [
            'email'    => $generateUser->user->email,
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
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar logar com um e-mail diferente do cadastrado
     */
    public function testLoginReturnErrorWhenEmailIsIncorrect()
    {
        $decryptedPassword = $this->generateUserAndToken();

        $body = [
            'email'    => $this->faker->email,
            'password' => $decryptedPassword->password
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
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna sucesso ao chamar a rota de atualização de dados do usuário
     * com autenticação
     */
    public function testReturnSuccessWhenUpdatedUserAuthenticated()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $this->patchJson(route('users.update'), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.update'),
                'method'  => 'PATCH',
                'code'    => 200,
                'data'    => [
                    'id'    => $generateUserAndToken->user->id,
                    'name'  => $generateUserAndToken->user->name,
                    'email' => $generateUserAndToken->user->email,
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
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        factory(Contact::class)->create([
            'user_id' => $generateUserAndToken->user->id
        ]);

        $this->get(route('contacts.index'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.index'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    [
                        'id'           => $generateUserAndToken->user->contacts->first()->id,
                        'name'         => $generateUserAndToken->user->contacts->first()->name,
                        'phone_number' => $generateUserAndToken->user->contacts->first()->phones->first()->phone_number
                    ],
                ],
            ], true);
    }

    /**
     * Retorna sucesso ao realizar busca dos contatos do usuário com autenticação
     * pelo nome do contato
     */
    public function testReturnSuccessWhenListAllContactsWithFilterContactName()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $contact = factory(Contact::class)->create([
            'user_id' => $generateUserAndToken->user->id
        ]);

        $body = [
            'filter' => $contact->name,
        ];

        $this->get(route('contacts.index'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.index'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    [
                        'id'           => $generateUserAndToken->user->contacts->first()->id,
                        'name'         => $generateUserAndToken->user->contacts->first()->name,
                        'phone_number' => $generateUserAndToken->user->contacts->first()->phones->first()->phone_number
                    ],
                ],
            ], true);
    }

    /**
     * Retorna sucesso ao realizar busca dos contatos do usuário com autenticação
     * pelo número de telefone do contato
     */
    public function testReturnSuccessWhenListAllContactsWithFilterPhoneNumber()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $contact = factory(Contact::class)->create([
            'user_id' => $generateUserAndToken->user->id
        ]);

        $body = [
            'filter' => $contact->phones->first()->phone_number,
        ];

        $this->get(route('contacts.index'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.index'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    [
                        'id'           => $generateUserAndToken->user->contacts->first()->id,
                        'name'         => $generateUserAndToken->user->contacts->first()->name,
                        'phone_number' => $generateUserAndToken->user->contacts->first()->phones->first()->phone_number
                    ],
                ],
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
        $generateToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateToken->token]);

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
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $this->get(route('users.show'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.show'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'id'    => $generateUserAndToken->user->id,
                    'name'  => $generateUserAndToken->user->name,
                    'email' => $generateUserAndToken->user->email,
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
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $this->get(route('users.change-password'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('users.change-password'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'token' => $generateUserAndToken->user->changePasswords->first()->token,
                ],
            ], true);
    }

    /**
     * Retorna sucesso ao atualizar a senha do usuário
     */
    public function testChangePasswordReturnSuccessWhenChangePassword()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $newPassword = $this->faker->regexify('[A-Z+a-z+0-9]{8,20}');

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $generateUserAndToken->user->id
        ]);

        $body = [
            'current_password'      => $generateUserAndToken->password,
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
                    'id'    => $generateUserAndToken->user->id,
                    'name'  => $generateUserAndToken->user->name,
                    'email' => $generateUserAndToken->user->email,
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar atualizar a senha do usuário após não informar ao
     * menos uma letra maiúscula para a nova senha
     */
    public function testChangePasswordReturnErrorWhenRegexDoesntHasUppercaseLetter()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $newPassword = $this->faker->regexify('[A-Z+0-9]{8,20}');

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $generateUserAndToken->user->id
        ]);

        $body = [
            'current_password'      => $generateUserAndToken->password,
            'new_password'          => $newPassword,
            'confirm_new_password'  => $newPassword,
            'token_update_password' => $changePasswordToken->token,
        ];

        $this->postJson(route('users.change-password'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('users.change-password'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar atualizar a senha do usuário após não informar ao
     * menos uma letra minúscula para a nova senha
     */
    public function testChangePasswordReturnErrorWhenRegexDoesntHasLowercaseLetter()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $newPassword = $this->faker->regexify('[a-z+0-9]{8,20}');

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $generateUserAndToken->user->id
        ]);

        $body = [
            'current_password'      => $generateUserAndToken->password,
            'new_password'          => $newPassword,
            'confirm_new_password'  => $newPassword,
            'token_update_password' => $changePasswordToken->token,
        ];

        $this->postJson(route('users.change-password'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('users.change-password'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar atualizar a senha do usuário após não informar ao
     * menos um número para a nova senha
     */
    public function testChangePasswordReturnErrorWhenRegexDoesntHasNumber()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $newPassword = $this->faker->regexify('[A-Z+a-z]{8,20}');

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $generateUserAndToken->user->id
        ]);

        $body = [
            'current_password'      => $generateUserAndToken->password,
            'new_password'          => $newPassword,
            'confirm_new_password'  => $newPassword,
            'token_update_password' => $changePasswordToken->token,
        ];

        $this->postJson(route('users.change-password'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('users.change-password'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar atualizar a senha do usuário após informar uma
     * senha que seu tamanho é menor do que 8 caracteres
     */
    public function testChangePasswordReturnErrorWhenRegexHasLessEightCaracteres()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $newPassword = $this->faker->regexify('[A-Z+a-z+0-9]{7}');

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $generateUserAndToken->user->id
        ]);

        $body = [
            'current_password'      => $generateUserAndToken->password,
            'new_password'          => $newPassword,
            'confirm_new_password'  => $newPassword,
            'token_update_password' => $changePasswordToken->token,
        ];

        $this->postJson(route('users.change-password'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('users.change-password'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar atualizar a senha do usuário após informar uma
     * senha que seu tamanho é mais do que 20 caracteres
     */
    public function testChangePasswordReturnErrorWhenRegexHasMoreTwentCaracteres()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $newPassword = $this->faker->regexify('[A-Z+a-z+0-9]{21}');

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $generateUserAndToken->user->id
        ]);

        $body = [
            'current_password'      => $generateUserAndToken->password,
            'new_password'          => $newPassword,
            'confirm_new_password'  => $newPassword,
            'token_update_password' => $changePasswordToken->token,
        ];

        $this->postJson(route('users.change-password'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('users.change-password'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao atualizar a senha do usuário quando o token de permissão
     * é inválido
     */
    public function testChangePasswordReturnErrorWhenChangePasswordWithoutToken()
    {
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $newPassword = $this->faker->password;

        $body = [
            'current_password'      => $generateUserAndToken->password,
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
        $generateUserAndToken = $this->generateUserAndToken();

        $this->withHeaders(['Authorization' => $generateUserAndToken->token]);

        $newPassword = $this->faker->password;

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $generateUserAndToken->user->id,
            'expires_at' => Carbon::now()->subMinutes(5)
        ]);

        $body = [
            'current_password'      => $generateUserAndToken->password,
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
