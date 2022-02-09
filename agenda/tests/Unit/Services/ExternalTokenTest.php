<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ExternalToken;
use App\Services\ExternalTokenService;
use App\Services\Responses\ServiceResponse;

class ExternalTokenTest extends BaseTestCase
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
            $this->faker->sha1,
            $user->id,
            'VEXPENSES',
            false,
            true
        );

        $this->assertInstanceOf(ServiceResponse::class, $createTokenResponse);
        $this->assertTrue($createTokenResponse->success);
        $this->assertIsBool($createTokenResponse->success);
        $this->assertNotNull($createTokenResponse->data);
        $this->assertEquals($createTokenResponse->data->user_id, $user->id);
    }

    /**
     * Retorna erro ao tentar criar um token para um usuário que não existe
     * para acessar a integração com o sistema VExpensess
     */
    public function testReturnErrorWhenUserDoesntExists()
    {
        $createTokenResponse = $this->externalTokenService->storeToken(
            $this->faker->sha1,
            $this->faker->uuid,
            'VEXPENSES',
            false,
            true
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

        $externalToken = factory(ExternalToken::class)->create([
            'user_id' => $user->id
        ]);

        $clearTokenResponse = $this->externalTokenService->clearToken(
            $user->id,
            $externalToken->system
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
        $clearTokenResponse = $this->externalTokenService->clearToken(
            $this->faker->uuid,
            $this->faker->word
        );

        $this->assertInstanceOf(ServiceResponse::class, $clearTokenResponse);
        $this->assertNotTrue($clearTokenResponse->success);
        $this->assertNull($clearTokenResponse->data);
        $this->assertHasInternalError($clearTokenResponse, 3);
    }
}
