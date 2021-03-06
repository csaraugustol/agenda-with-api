<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Contact;
use App\Models\ExternalToken;
use App\Services\Responses\ServiceResponse;
use Tests\Mocks\Providers\VexpensesProvider;
use App\Services\Contracts\VexpensesServiceInterface;

class VexpensesTest extends BaseTestCase
{
    /**
     * @var VexpensesServiceInterface
     */
    protected $vexpensesService;

    /**
     * @var VexpensesProvider
     */
    protected $vexpensesProvider;

    public function setUp(): void
    {
        parent::setUp();

        $this->vexpensesService = app(VexpensesServiceInterface::class);
        $this->vexpensesProvider = app(VexpensesProvider::class);
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

    /**
     * Retorna sucesso ao listar membros da API
     */
    public function testReturSuccessWhenListMembers()
    {
        $user = factory(User::class)->create();

        factory(ExternalToken::class)->create([
            'user_id' => $user->id,
        ]);

        $mockMembersResponse = $this->vexpensesProvider->getMockReturnAllMembers();

        $this->vexpensesProvider->setMockRequest(
            $this->vexpensesService,
            $mockMembersResponse->status_code,
            $mockMembersResponse->response,
        );

        $listMembersResponse = $this->vexpensesService->findAllTeamMembers($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $listMembersResponse);
        $this->assertTrue($listMembersResponse->success);
        $this->assertNotNull($listMembersResponse->data);
        $this->assertEquals(
            $listMembersResponse->data->first()->name,
            $mockMembersResponse->response->data[0]->name
        );
    }

    /**
     * Retorna erro ao tentar uma resposta da API sem token
     */
    public function testReturErrorWhenUserDoesntHasExternalToken()
    {
        $user = factory(User::class)->create();

        $listMembersResponse = $this->vexpensesService->findAllTeamMembers($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $listMembersResponse);
        $this->assertFalse($listMembersResponse->success);
        $this->assertNull($listMembersResponse->data);
        $this->assertHasInternalError($listMembersResponse, 24);
    }

    /**
     * Retorna erro ao tentar acessar a lista de memebros vinda da a APÌ com um
     * usuário que não existe
     */
    public function testReturErrorWhenFindAllMembersAndUserDoesntExists()
    {
        $listMembersResponse = $this->vexpensesService->findAllTeamMembers(
            $this->faker->uuid
        );

        $this->assertInstanceOf(ServiceResponse::class, $listMembersResponse);
        $this->assertNotTrue($listMembersResponse->success);
        $this->assertNull($listMembersResponse->data);
        $this->assertHasInternalError($listMembersResponse, 3);
    }

    /**
     * Retorna erro ao tentar lista os membros do Vexpenses quando o token de
     * integração do usuário não é do Vexpenses
     */
    public function testReturnErrorWhenTryListMembersAndExternalTokenDoesntIsVexpenses()
    {
        $user = factory(User::class)->create();

        factory(ExternalToken::class)->create([
            'user_id' => $user->id,
            'system'  => $this->faker->word
        ]);

        $listMembersResponse = $this->vexpensesService->findAllTeamMembers($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $listMembersResponse);
        $this->assertNotTrue($listMembersResponse->success);
        $this->assertNull($listMembersResponse->data);
        $this->assertHasInternalError($listMembersResponse, 24);
    }

    /**
     * Retorna sucesso ao listar apenas os membros da API que possuem telefone.
     * Foram criados dois contatos e o sucesso deve retornar apenas um, fazendo
     * a filtragem e removendo o membro que não possui telefone
     */
    public function testReturSuccessWhenListMembersAndFilterJustMemberWithPhone()
    {
        $user = factory(User::class)->create();

        factory(ExternalToken::class)->create([
            'user_id' => $user->id,
        ]);

        $mockMembersResponse = $this->vexpensesProvider
            ->getMockReturnMemberWithPhoneAndOtherMemberWithoutPhone();

        $this->vexpensesProvider->setMockRequest(
            $this->vexpensesService,
            $mockMembersResponse->status_code,
            $mockMembersResponse->response,
        );

        $listMembersResponse = $this->vexpensesService->findAllTeamMembers($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $listMembersResponse);
        $this->assertTrue($listMembersResponse->success);
        $this->assertNotNull($listMembersResponse->data);
        $this->assertEquals(1, count($listMembersResponse->data));
        $this->assertEquals(
            $listMembersResponse->data->first()->phones[0],
            $mockMembersResponse->response->data[0]->phone1
        );
    }

    /**
     * Retorna sucesso ao lista um membro que não possui integração
     */
    public function testReturSuccessWhenListMemberWithoutIntegration()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create();

        factory(ExternalToken::class)->create([
            'user_id' => $user->id,
        ]);

        $mockMembersResponse = $this->vexpensesProvider->getMockReturnAllMembers();

        $this->vexpensesProvider->setMockRequest(
            $this->vexpensesService,
            $mockMembersResponse->status_code,
            $mockMembersResponse->response,
        );

        $listMembersResponse = $this->vexpensesService->findAllTeamMembers($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $listMembersResponse);
        $this->assertTrue($listMembersResponse->success);
        $this->assertNotNull($listMembersResponse->data);
        $this->assertNotEquals($listMembersResponse->data->first()->external_id, $contact->external_id);
    }

    /**
     * Retorna sucesso ao lista um membro que já possui alguma integração
     */
    public function testReturSuccessWhenListMemberWithIntegration()
    {
        $externalId = $this->faker->numberBetween(1, 100);
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id'     => $user->id,
            'external_id' => $externalId,
        ]);

        factory(ExternalToken::class)->create([
            'user_id' => $user->id,
        ]);

        $mockMembersResponse = $this->vexpensesProvider
            ->getMockReturnAllMembers($contact->external_id);

        $this->vexpensesProvider->setMockRequest(
            $this->vexpensesService,
            $mockMembersResponse->status_code,
            $mockMembersResponse->response,
        );

        $listMembersResponse = $this->vexpensesService->findAllTeamMembers($user->id);

        $this->assertInstanceOf(ServiceResponse::class, $listMembersResponse);
        $this->assertTrue($listMembersResponse->success);
        $this->assertNotNull($listMembersResponse->data);
        $this->assertTrue($listMembersResponse->data->first()->integrated);
    }
}
