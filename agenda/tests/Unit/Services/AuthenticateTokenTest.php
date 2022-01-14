<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\AuthenticateTokenService;
use App\Services\Responses\ServiceResponse;
use Carbon\Carbon;

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

    public function testReturnErrorWhenStoreTokenToUserDoesntExists()
    {
        $createTokenResponse = $this->authenticateTokenService->storeToken(
            $this->faker->uuid
        );

        $this->assertInstanceOf(ServiceResponse::class, $createTokenResponse);
        $this->assertNotTrue($createTokenResponse->success);
        $this->assertNull($createTokenResponse->data);
        $this->assertHasInternalError($createTokenResponse, 3);
    }

    public function testReturnSuccessWhenClearTokenUser()
    {
        $user = factory(User::class)->create();

        $clearTokenResponse = $this->authenticateTokenService->clearToken(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $clearTokenResponse);
        $this->assertTrue($clearTokenResponse->success);
        $this->assertNull($clearTokenResponse->data);
    }

    public function testReturnErrorWhenClearTokenToUserDoesntExists()
    {
        $clearTokenResponse = $this->authenticateTokenService->clearToken(
            $this->faker->uuid
        );

        $this->assertInstanceOf(ServiceResponse::class, $clearTokenResponse);
        $this->assertFalse($clearTokenResponse->success);
        $this->assertNull($clearTokenResponse->data);
        $this->assertHasInternalError($clearTokenResponse, 3);
    }

    public function testReturnSuccessWhenFindToken()
    {
        $user = factory(User::class)->create();

        $createTokenResponse = $this->authenticateTokenService->storeToken(
            $user->id
        );

        $findTokenResponse = $this->authenticateTokenService->findToken(
            $createTokenResponse->data->token
        );

        $this->assertInstanceOf(ServiceResponse::class, $findTokenResponse);
        $this->assertTrue($findTokenResponse->success);
        $this->assertNotNull($findTokenResponse->data);
        $this->assertEquals($findTokenResponse->data->user_id, $user->id);
    }
}
