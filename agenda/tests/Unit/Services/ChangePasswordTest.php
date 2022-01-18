<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ChangePassword;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\ChangePasswordServiceInterface;

class ChangePasswordTest extends BaseTestCase
{
    /**
     * @var ChangePasswordServiceInterface
     */
    protected $changePasswordService;

    public function setUp(): void
    {
        parent::setUp();

        $this->changePasswordService = app(ChangePasswordServiceInterface::class);
    }

     /**
     * Testa o método NewToken na service ChangePasswordService retornando
     * sucesso ao criar um novo token para permitir o usuário alterar sua senha
     */
    public function testReturnSuccessWhenCreateNewTokenToChangePassword()
    {
        $user = factory(User::class)->create();

        $newTokenResponse = $this->changePasswordService->newToken($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $newTokenResponse);
        $this->assertTrue($newTokenResponse->success);
        $this->assertNotNull($newTokenResponse->data);
        $this->assertLessThan($newTokenResponse->data->expires_at, Carbon::now());
    }

    /**
     * Testa o método NewToken na service ChangePasswordService retornando erro
     * ao tentar criar um token de alteração de senha quando o usuário informado
     * não existe
     */
    public function testChangePasswordReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $newTokenResponse = $this->changePasswordService->newToken($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $newTokenResponse);
        $this->assertFalse($newTokenResponse->success);
        $this->assertNull($newTokenResponse->data);
        $this->assertHasInternalError($newTokenResponse, 3);
    }

    /**
     * Testa o método ClearToken na service ChangePasswordService retornando
     * sucesso ao limpar token existente do usuário
     */
    public function testReturnSuccessWhenClearToken()
    {
        $user = factory(User::class)->create();

        factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $clearTokenResponse = $this->changePasswordService->clearToken(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $clearTokenResponse);
        $this->assertNotFalse($clearTokenResponse->success);
        $this->assertNull($clearTokenResponse->data);
        $this->assertEmpty($user->changePasswords);
    }

    /**
     * Testa o método ClearToken na service ChangePasswordService retornando
     * erro ao tentar limpar o token de um usuário que não existe
     */
    public function testReturnErrorWhenClearTokenAndUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $clearTokenResponse = $this->changePasswordService->clearToken(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $clearTokenResponse);
        $this->assertFalse($clearTokenResponse->success);
        $this->assertNull($clearTokenResponse->data);
        $this->assertHasInternalError($clearTokenResponse, 3);
    }

    /**
     * Testa o método FindToken na service ChangePasswordService retornando
     * sucesso ao buscar um token do usuário
     */
    public function testReturnSuccessWhenFindTokenToChangePassword()
    {
        $user = factory(User::class)->create();

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $findTokenResponse = $this->changePasswordService->findByToken(
            $changePasswordToken->token,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $findTokenResponse);
        $this->assertIsBool($findTokenResponse->success);
        $this->assertTrue($findTokenResponse->success);
        $this->assertNotNull($findTokenResponse->data);
    }

    /**
     * Testa o método FindToken na service ChangePasswordService retornando erro
     * ao tentar buscar um token de um usuário que não existe
     */
    public function testReturnErrorWhenFindTokenAndUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $findTokenResponse = $this->changePasswordService->findByToken(
            $changePasswordToken->token,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $findTokenResponse);
        $this->assertIsBool($findTokenResponse->success);
        $this->assertNotTrue($findTokenResponse->success);
        $this->assertNull($findTokenResponse->data);
        $this->assertHasInternalError($findTokenResponse, 3);
    }

     /**
     * Testa o método FindToken na service ChangePasswordService retornando erro
     * ao tentar buscar um token que não existe
     */
    public function testReturnErrorWhenFindTokenAndTokenDoesntExists()
    {
        $user = factory(User::class)->create();

        $changePasswordToken = factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $changePasswordToken->delete();

        $findTokenResponse = $this->changePasswordService->findByToken(
            $changePasswordToken->token,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $findTokenResponse);
        $this->assertTrue($findTokenResponse->success);
        $this->assertNull($findTokenResponse->data);
        $this->assertHasInternalError($findTokenResponse, 17);
    }
}
