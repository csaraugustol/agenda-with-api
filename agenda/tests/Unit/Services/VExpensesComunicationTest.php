<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ExternalToken;
use App\Services\ExternalTokenService;
use App\Services\Responses\ServiceResponse;

class VExpensesComunicationTest extends BaseTestCase
{
    /**
     * @var ExternalTokenService
     */
    protected $externalTokenService;

    public function setUp(): void
    {
        parent::setUp();

        $this->externalTokenService = app(ExternalTokenService::class);
    }

    /**
     * Retorna sucesso ao criar um token para o usuário acessar a integração
     * com o sistema VExpenses
     */
    public function testReturnSuccessWhenStoreTokenToAccess()
    {
        $user = factory(User::class)->create();

        $createTokenResponse = $this->externalTokenService->storeToken(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $createTokenResponse);
        $this->assertTrue($createTokenResponse->success);
        $this->assertIsBool($createTokenResponse->success);
        $this->assertNotNull($createTokenResponse->data);
        $this->assertEquals($createTokenResponse->data->user_id, $user->id);
        $this->assertLessThan($createTokenResponse->data->expires_at, Carbon::now());
    }

    /**
     * Retorna erro ao tentar criar um token para um usuário que não existe
     * para acessar a integração com o sistema VExpensess
     */
    public function testReturnErrorWhenUserDoesntExists()
    {
        $createTokenResponse = $this->externalTokenService->storeToken(
            $this->faker->uuid
        );

        $this->assertInstanceOf(ServiceResponse::class, $createTokenResponse);
        $this->assertNotTrue($createTokenResponse->success);
        $this->assertNull($createTokenResponse->data);
        $this->assertHasInternalError($createTokenResponse, 3);
    }

    /**
     * Retorna sucesso ao limpar os tokens existentes do usuário
     */
    public function testReturnSuccessWhenClearTokenUser()
    {
        $user = factory(User::class)->create();

        factory(ExternalToken::class, 3)->create([
            'user_id' => $user->id
        ]);

        $clearTokenResponse = $this->externalTokenService->clearToken(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $clearTokenResponse);
        $this->assertTrue($clearTokenResponse->success);
        $this->assertNull($clearTokenResponse->data);
        $this->assertEmpty($user->externalTokens);
    }

    /**
     * Retorna erro ao tentar limpar tokens de um usuário que não existe
     */
    public function testReturnErrorWhenClearTokenToUserDoesntExists()
    {
        $clearTokenResponse = $this->authenticateTokenService->clearToken(
            $this->faker->uuid
        );

        $this->assertInstanceOf(ServiceResponse::class, $clearTokenResponse);
        $this->assertNotTrue($clearTokenResponse->success);
        $this->assertNull($clearTokenResponse->data);
        $this->assertHasInternalError($clearTokenResponse, 3);
    }
}
