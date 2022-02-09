<?php

namespace Tests\Unit\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ExternalToken;
use App\Services\Responses\ServiceResponse;
use App\Services\VExpensesComunicationService;

class VExpensesComunicationTest extends BaseTestCase
{
    /**
     * @var VExpensesComunicationService
     */
    protected $vExpensesComunicationService;

    public function setUp(): void
    {
        parent::setUp();

        $this->vExpensesComunicationService = app(VExpensesComunicationService::class);
    }

    /**
     * Retorna sucesso ao criar um token de acesso para a comunicação com
     * o VExpenses
     */
    public function testReturnSuccessWhenStoreAccessTokenToVExpenses()
    {
        $user = factory(User::class)->create();

        $createAccessTokenResponse = $this->vExpensesComunicationService
            ->tokenToAccessVexpenses($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $createAccessTokenResponse);
        $this->assertInstanceOf(ExternalToken::class, $createAccessTokenResponse->data);
        $this->assertNotFalse($createAccessTokenResponse->success);
        $this->assertNotNull($createAccessTokenResponse->data);
        $this->assertEquals($createAccessTokenResponse->data->user_id, $user->id);
        $this->assertLessThan($createAccessTokenResponse->data->expires_at, Carbon::now());
    }

    /**
     * Retorna erro ao tentar criar um token de acesso para a comunicação com
     * o VExpenses com um usuário que não existe
     */
    public function testReturnErrorWhenUserDoesntExistsAndTryStoreAccessTokenToVExpenses()
    {
        $createAccessTokenResponse = $this->vExpensesComunicationService
            ->tokenToAccessVexpenses($this->faker->uuid);

        $this->assertInstanceOf(ServiceResponse::class, $createAccessTokenResponse);
        $this->assertNotTrue($createAccessTokenResponse->success);
        $this->assertNull($createAccessTokenResponse->data);
        $this->assertHasInternalError($createAccessTokenResponse, 3);
    }
}
