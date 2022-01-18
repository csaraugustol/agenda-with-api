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
    }

    public function testReturnSuccessWhenTryLogoutAndUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $logoutResponse = $this->userService->logout($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $logoutResponse);
        $this->assertFalse($logoutResponse->success);
        $this->assertNull($logoutResponse->data);
        $this->assertHasInternalError($logoutResponse, 3);
    }

    public function testReturnSuccessWhenTryLogoutAndIdIsIncorrect()
    {
        $logoutResponse = $this->userService->logout($this->faker->uuid);

        $this->assertInstanceOf(ServiceResponse::class, $logoutResponse);
        $this->assertNotTrue($logoutResponse->success);
        $this->assertNull($logoutResponse->data);
        $this->assertHasInternalError($logoutResponse, 3);
    }

    public function testReturnSuccessWhenFindByEmail()
    {
        $user = factory(User::class)->create();

        $findEmailResponse = $this->userService->findByEmail($user->email);

        $this->assertInstanceOf(ServiceResponse::class, $findEmailResponse);
        $this->assertIsObject($findEmailResponse->data);
        $this->assertTrue($findEmailResponse->success);
        $this->assertNotNull($findEmailResponse->data);
    }

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

    public function testReturnSuccessWhenCreateTokenToChangePassword()
    {
        $user = factory(User::class)->create();

        factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $findUserResponse = $this->userService->tokenToChangePassword($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $findUserResponse);
        $this->assertTrue($findUserResponse->success);
        $this->assertNotNull($findUserResponse->data);
    }

    public function testTokenToChangePasswordReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $findUserResponse = $this->userService->tokenToChangePassword($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $findUserResponse);
        $this->assertFalse($findUserResponse->success);
        $this->assertNull($findUserResponse->data);
        $this->assertHasInternalError($findUserResponse, 3);
    }

    public function testReturnSuccessWhenChangePassword()
    {
        $password = $this->faker->password;

        $user = factory(User::class)->create([
            'password' => bcrypt($password)
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
    }

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
