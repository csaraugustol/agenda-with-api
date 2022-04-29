<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\ChangePassword;
use App\Models\AuthenticateToken;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Params\User\RegisterUserServiceParams;

class UserTest extends BaseTestCase
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    public function setUp(): void
    {
        parent::setUp();

        $this->userService = app(UserServiceInterface::class);
    }

    /**
     * Testa o método Register na UserService retornando sucesso ao realizar
     * um registro de um novo usuário
     */
    public function testReturnSuccessWhenRegisterUser()
    {
        $registerUserResponse = $this->userService->register(
            new RegisterUserServiceParams(
                $this->faker->name,
                $this->faker->email,
                $this->faker->password
            )
        );

        $this->assertInstanceOf(ServiceResponse::class, $registerUserResponse);
        $this->assertInstanceOf(User::class, $registerUserResponse->data);
        $this->assertTrue($registerUserResponse->success);
        $this->assertNotNull($registerUserResponse->data);
    }

    /**
     * Testa o método Login na UserService retornando sucesso ao tentar realizar
     * o login do usuário
     */
    public function testReturnSuccessWhenTryLogin()
    {
        $password = $this->faker->password;

        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        $loginResponse = $this->userService->login(
            $user->email,
            $password
        );

        $this->assertInstanceOf(ServiceResponse::class, $loginResponse);
        $this->assertTrue($loginResponse->success);
        $this->assertNotNull($loginResponse->data);
        $this->assertNotEmpty($user->authenticateTokens);
    }

     /**
     * Testa o método Login na UserService retornando erro ao tentar efetuar
     * login com um usuário que não existe
     */
    public function testReturnErrorWhenTryLoginAndUserDoesntExist()
    {
        $password = $this->faker->password;

        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        $user->delete();

        $loginResponse = $this->userService->login(
            $user->email,
            $password
        );

        $this->assertInstanceOf(ServiceResponse::class, $loginResponse);
        $this->assertFalse($loginResponse->success);
        $this->assertNull($loginResponse->data);
        $this->assertHasInternalError($loginResponse, 1);
    }

     /**
     * Testa o método Login na UserService retornando erro ao tentar realizar
     * login com um e-mail incorreto
     */
    public function testReturnErrorWhenTryLoginWhereEmailIsIncorrect()
    {
        $password = $this->faker->password;

        factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        $loginResponse = $this->userService->login(
            $this->faker->email,
            $password
        );

        $this->assertInstanceOf(ServiceResponse::class, $loginResponse);
        $this->assertFalse($loginResponse->success);
        $this->assertNull($loginResponse->data);
        $this->assertHasInternalError($loginResponse, 1);
    }

    /**
     * Testa o método Login na UserService retornando erro ao tentar realizar
     * login com uma senha incorreta
     */
    public function testReturnErrorWhenTryLoginWherePasswordIsIncorrect()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt($this->faker->password)
        ]);

        $loginResponse = $this->userService->login(
            $user->email,
            $this->faker->password
        );

        $this->assertInstanceOf(ServiceResponse::class, $loginResponse);
        $this->assertFalse($loginResponse->success);
        $this->assertNull($loginResponse->data);
        $this->assertHasInternalError($loginResponse, 1);
    }

    /**
     * Testa o catch, quando ocorre um erro no parametro passado no método para
     * registrar um usuário no sistema
     */
    public function testTryCatchWhenTryRegisterUser()
    {
        $registerUserServiceParams = $this->prophesize(RegisterUserServiceParams::class)->reveal();
        $registerUserResponse = $this->userService->register(
            $registerUserServiceParams
        );

        $this->assertInstanceOf(ServiceResponse::class, $registerUserResponse);
        $this->assertFalse($registerUserResponse->success);
        $this->assertIsArray($registerUserResponse->data);
        $this->assertInstanceOf(RegisterUserServiceParams::class, $registerUserResponse->data['params']);
    }

    /**
     * Testa o método Logout na UserService retornando sucesso ao tentar realizar
     * o logout do usuário
     */
    public function testReturnSuccessWhenTryLogout()
    {
        $user = factory(User::class)->create();

        factory(AuthenticateToken::class)->create([
            'user_id' => $user->id
        ]);

        $logoutResponse = $this->userService->logout($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $logoutResponse);
        $this->assertTrue($logoutResponse->success);
        $this->assertNull($logoutResponse->data);
        $this->assertEmpty($user->authenticateTokens);
    }

    /**
     * Testa o método Logout na UserService retornando erro ao tentar realizar
     * o logout com dados de um usuário que não existe
     */
    public function testReturnErrorWhenTryLogoutAndUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $logoutResponse = $this->userService->logout($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $logoutResponse);
        $this->assertFalse($logoutResponse->success);
        $this->assertNull($logoutResponse->data);
        $this->assertHasInternalError($logoutResponse, 3);
    }

    /**
     * Testa o método Logout na UserService retornando erro ao tentar realizar
     * o logout com um id de usuário inválido
     */
    public function testReturnErrorWhenTryLogoutAndIdIsIncorrect()
    {
        $logoutResponse = $this->userService->logout($this->faker->uuid);

        $this->assertInstanceOf(ServiceResponse::class, $logoutResponse);
        $this->assertNotTrue($logoutResponse->success);
        $this->assertNull($logoutResponse->data);
        $this->assertHasInternalError($logoutResponse, 3);
    }

    /**
     * Testa o método FindByEmail na UserService retornando sucesso ao tentar
     * buscar um usuário pelo seu e-mail
     */
    public function testReturnSuccessWhenFindByEmail()
    {
        $user = factory(User::class)->create();

        $findEmailResponse = $this->userService->findByEmail($user->email);

        $this->assertInstanceOf(ServiceResponse::class, $findEmailResponse);
        $this->assertIsObject($findEmailResponse->data);
        $this->assertTrue($findEmailResponse->success);
        $this->assertNotNull($findEmailResponse->data);
    }

    /**
     * Testa o método FindByEmail na UserService retornando erro ao tentar
     * buscar um usuário com um email que não existe
     */
    public function testReturnErrorWhenEmailDoestExists()
    {
        $findEmailResponse = $this->userService->findByEmail(
            $this->faker->email
        );

        $this->assertInstanceOf(ServiceResponse::class, $findEmailResponse);
        $this->assertNotFalse($findEmailResponse->success);
        $this->assertNull($findEmailResponse->data);
        $this->assertHasInternalError($findEmailResponse, 3);
    }

    /**
     * Testa o método Update na UserService retornando sucesso ao tentar
     * realizar a atualização de dados do usuário
     */
    public function testReturnSuccessWhenUpdateUser()
    {
        $user = factory(User::class)->create();

        $array = ['name' => $this->faker->name];

        $updateUserResponse = $this->userService->update($array, $user->id);

        $this->assertInstanceOf(ServiceResponse::class, $updateUserResponse);
        $this->assertTrue($updateUserResponse->success);
        $this->assertNotNull($updateUserResponse->data);
        $this->assertNotEquals($updateUserResponse->data->name, $user->name);
        $this->assertEquals($updateUserResponse->data->name, $array['name']);
    }

    /**
     * Testa o método Update na UserService retornando erro ao tentar realizar
     * a atualização de dados de um usuário que não existe
     */
    public function testUpdateReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $updateUserResponse = $this->userService->update(
            [],
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateUserResponse);
        $this->assertFalse($updateUserResponse->success);
        $this->assertNull($updateUserResponse->data);
        $this->assertHasInternalError($updateUserResponse, 3);
    }

    /**
     * Testa o método Find na UserService retornando sucesso ao tentar realizar
     * a busca de um usuário pelo seu id
     */
    public function testReturnSuccessWhenFindUser()
    {
        $user = factory(User::class)->create();

        $findUserResponse = $this->userService->find($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $findUserResponse);
        $this->assertInstanceOf(User::class, $findUserResponse->data);
        $this->assertIsBool($findUserResponse->success);
        $this->assertTrue($findUserResponse->success);
        $this->assertNotNull($findUserResponse->data);
    }

    /**
     * Testa o método Find na UserService retornando erro ao tentar realizar
     * a busca de um usuário que não existe
     */
    public function testFindReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $findUserResponse = $this->userService->find($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $findUserResponse);
        $this->assertIsBool($findUserResponse->success);
        $this->assertTrue($findUserResponse->success);
        $this->assertNull($findUserResponse->data);
        $this->assertHasInternalError($findUserResponse, 3);
    }

    /**
     * Testa o método TokenToChangePassword na UserService retornando sucesso
     * ao criar um token para a atualização de senha do usuário
     */
    public function testReturnSuccessWhenCreateTokenToChangePassword()
    {
        $user = factory(User::class)->create();

        factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $tokenToChangePasswordResponse = $this->userService->tokenToChangePassword(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $tokenToChangePasswordResponse);
        $this->assertTrue($tokenToChangePasswordResponse->success);
        $this->assertNotNull($tokenToChangePasswordResponse->data);
    }

    /**
     * Testa o método TokenToChangePassword na UserService retornando erro
     * ao tentar criar um token para a atualização de senha para um usuário
     * que não existe
     */
    public function testTokenToChangePasswordReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $tokenToChangePasswordResponse = $this->userService->tokenToChangePassword(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $tokenToChangePasswordResponse);
        $this->assertFalse($tokenToChangePasswordResponse->success);
        $this->assertNull($tokenToChangePasswordResponse->data);
        $this->assertHasInternalError($tokenToChangePasswordResponse, 3);
    }

    /**
     * Testa o método ChangePassword na UserService retornando sucesso ao
     * realizar a atualização de senha do usuário
     */
    public function testReturnSuccessWhenChangePassword()
    {
        $password = $this->faker->password;

        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $changePasswordResponse = $this->userService->changePassword(
            $user->id,
            $password,
            $this->faker->password
        );

        $this->assertInstanceOf(ServiceResponse::class, $changePasswordResponse);
        $this->assertTrue($changePasswordResponse->success);
        $this->assertIsObject($changePasswordResponse->data);
        $this->assertNotNull($changePasswordResponse->data);
        $this->assertNotNull($changePasswordResponse->data->changePasswords);
    }

    /**
     * Testa o método ChangePassword na UserService retornando erro ao tentar
     * realizar a atualização de senha de um usuário que não existe
     */
    public function testChangePasswordReturnErrorWhenUserDoesntExist()
    {
        $password = $this->faker->password;

        $user = factory(User::class)->create([
            'password' => bcrypt($password)
        ]);

        $user->delete();

        $changePasswordResponse = $this->userService->changePassword(
            $user->id,
            $password,
            $this->faker->password
        );

        $this->assertInstanceOf(ServiceResponse::class, $changePasswordResponse);
        $this->assertFalse($changePasswordResponse->success);
        $this->assertIsBool($changePasswordResponse->success);
        $this->assertNull($changePasswordResponse->data);
        $this->assertHasInternalError($changePasswordResponse, 3);
    }

    /**
     * Testa o método ChangePassword na UserService retornando erro ao tentar
     * realizar a atualização de senha de um usuário informando a senha atual
     * de forma incorreta
     */
    public function testChangePasswordReturnErrorWhenCurrentPasswordIsIncorrect()
    {
        $user = factory(User::class)->create();

        $changePasswordResponse = $this->userService->changePassword(
            $user->id,
            $this->faker->password,
            $this->faker->password
        );

        $this->assertInstanceOf(ServiceResponse::class, $changePasswordResponse);
        $this->assertFalse($changePasswordResponse->success);
        $this->assertIsBool($changePasswordResponse->success);
        $this->assertNull($changePasswordResponse->data);
        $this->assertHasInternalError($changePasswordResponse, 20);
    }
}
