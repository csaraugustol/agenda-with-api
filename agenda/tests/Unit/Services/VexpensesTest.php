<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\ExternalToken;
use App\Services\VexpensesService;
use App\Services\Responses\ServiceResponse;

class VexpensesTest extends BaseTestCase
{
    /**
     * @var VexpensesService
     */
    protected $vexpensesService;

    public function setUp(): void
    {
        parent::setUp();

        $this->vexpensesService = app(VexpensesService::class);
    }

    /**
     * Retorna sucesso ao criar um token de acesso para a comunicação com
     * o VExpenses
     */
    public function testReturnSuccessWhenStoreAccessTokenToVExpenses()
    {
        $user = factory(User::class)->create();

        $createAccessTokenResponse = $this->vexpensesService
            ->tokenToAccess($this->faker->sha1, $user->id);

        $this->assertInstanceOf(ServiceResponse::class, $createAccessTokenResponse);
        $this->assertInstanceOf(ExternalToken::class, $createAccessTokenResponse->data);
        $this->assertNotFalse($createAccessTokenResponse->success);
        $this->assertNotNull($createAccessTokenResponse->data);
        $this->assertEquals($createAccessTokenResponse->data->user_id, $user->id);
    }

    /**
     * Retorna erro ao tentar criar um token de acesso para a comunicação com
     * o VExpenses com um usuário que não existe
     */
    public function testReturnErrorWhenUserDoesntExistsAndTryStoreAccessTokenToVExpenses()
    {
        $createAccessTokenResponse = $this->vexpensesService
            ->tokenToAccess($this->faker->sha1, $this->faker->uuid);

        $this->assertInstanceOf(ServiceResponse::class, $createAccessTokenResponse);
        $this->assertNotTrue($createAccessTokenResponse->success);
        $this->assertNull($createAccessTokenResponse->data);
        $this->assertHasInternalError($createAccessTokenResponse, 3);
    }
}
