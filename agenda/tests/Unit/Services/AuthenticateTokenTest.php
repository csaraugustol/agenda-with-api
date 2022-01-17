<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\AuthenticateToken;
use App\Services\AuthenticateTokenService;
use App\Services\Responses\ServiceResponse;

class AuthenticateTokenTest extends BaseTestCase
{
    /**
     * @var AutheticateTokenService
     */
    protected $authenticateTokenService;

    public function setUp(): void
    {
        parent::setUp();

        $this->authenticateTokenService = app(AuthenticateTokenService::class);
    }

    /**
     * Testa o método StoreToken na service AuthenticateToken retornando sucesso
     * ao criar um novo token para o usuário
     */
    public function testReturnSuccessWhenStoreToken()
    {
        $user = factory(User::class)->create();

        $createTokenResponse = $this->authenticateTokenService->storeToken(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $createTokenResponse);
        $this->assertTrue($createTokenResponse->success);
        $this->assertNotNull($createTokenResponse->data);
        $this->assertLessThan($createTokenResponse->data->expires_at, Carbon::now());
    }

    /**
     * Testa o método StoreToken na service AuthenticateToken retornando erro
     * informando um usuário que não existe
     */
    public function testReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $createTokenResponse = $this->authenticateTokenService->storeToken(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $createTokenResponse);
        $this->assertNotTrue($createTokenResponse->success);
        $this->assertNull($createTokenResponse->data);
        $this->assertHasInternalError($createTokenResponse, 3);
    }

    /**
     * Testa o método ClearToken na service AuthenticateToken retornando sucesso
     * ao limpar tokens existentes do usuário
     */
    public function testReturnSuccessWhenClearTokenUser()
    {
        $user = factory(User::class)->create();

        factory(AuthenticateToken::class, 5)->create([
            'user_id' => $user->id
        ]);

        $clearTokenResponse = $this->authenticateTokenService->clearToken(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $clearTokenResponse);
        $this->assertTrue($clearTokenResponse->success);
        $this->assertNull($clearTokenResponse->data);
        $this->assertEmpty($user->authenticateTokens);
    }

    /**
     * Testa o método ClearToken na service AuthenticateToken retornando erro
     * ao tentar limpar tokens de um usuário que não existe
     */
    public function testReturnErrorWhenClearTokenToUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $clearTokenResponse = $this->authenticateTokenService->clearToken(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $clearTokenResponse);
        $this->assertFalse($clearTokenResponse->success);
        $this->assertNull($clearTokenResponse->data);
        $this->assertHasInternalError($clearTokenResponse, 3);
    }

    /**
     * Testa o método FindToken na service AuthenticateToken retornando sucesso
     * na busca do token do usuário
     */
    public function testReturnSuccessWhenFindToken()
    {
        $user = factory(User::class)->create();

        $authenticateToken = factory(AuthenticateToken::class)->create([
            'user_id' => $user->id
        ]);

        $findTokenResponse = $this->authenticateTokenService->findToken(
            $authenticateToken->token
        );

        $this->assertInstanceOf(ServiceResponse::class, $findTokenResponse);
        $this->assertTrue($findTokenResponse->success);
        $this->assertNotNull($findTokenResponse->data);
        $this->assertEquals($findTokenResponse->data->user_id, $user->id);
    }

    /**
     * Testa o método FindToken na service AuthenticateToken retornando erro
     * ao buscar um token que não existe
     */
    public function testReturnErrorWhenTokenDoesntExists()
    {
        $findTokenResponse = $this->authenticateTokenService->findToken(
            $this->faker->sha1
        );

        $this->assertInstanceOf(ServiceResponse::class, $findTokenResponse);
        $this->assertNotFalse($findTokenResponse->success);
        $this->assertIsBool($findTokenResponse->success);
        $this->assertNull($findTokenResponse->data);
        $this->assertHasInternalError($findTokenResponse, 5);
    }
}
