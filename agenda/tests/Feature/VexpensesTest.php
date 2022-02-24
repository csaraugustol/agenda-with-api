<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Contact;
use App\Services\VexpensesService;
use App\Services\Responses\ServiceResponse;
use Tests\Mocks\Providers\VexpensesProvider;

class VexpensesTest extends BaseTestCase
{
    /**
     * O usuário solicitante da request
     * @var User
     */
    protected $user;

    /**
     * @var VexpensesProvider
     */
    protected $vexpensesProvider;

    public function setUp(): void
    {
        parent::setUp();

        $tokenResponse = $this->generateUserAndToken();

        $this->user = $tokenResponse->user;
        $this->withHeaders(['Authorization' => $tokenResponse->token]);
        $this->vexpensesProvider = app(VexpensesProvider::class);
    }

    /**
     * Retorna sucesso ao realizar a vinculação de um token para acessar a
     * integração com o VExpenses
     */
    public function testReturnSuccessGenerateNewtokenToAccess()
    {
        $body = [
            'token' => $this->faker->sha1
        ];

        $this->postJson(route('vexpenses.access-token'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('vexpenses.access-token'),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'token'  => $body['token'],
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar realizar a vinculação de um token para acessar a
     * integração com o VExpenses mas esse token não é informado
     */
    public function testReturnErrorTokenToAccessDoesntIsInformed()
    {
        $this->postJson(route('vexpenses.access-token'), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.access-token'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar realizar a vinculação de um token de acesso ao
     * VExpenses com um usuário não autorizado
     */
    public function testReturnErrorWhenGenerateNewtokenToAccessAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->postJson(route('vexpenses.access-token'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.access-token'),
                'method'  => 'POST',
                'code'    => 401,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 6
                    ],
                ],
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertUnauthorized();
    }

    /**
     * Retorna sucesso ao listar os membros do VExpenses
     */
    public function testReturnSuccessWhenListAllMembers()
    {
        $mockMembersResponse = $this->vexpensesProvider
            ->getMockReturnAllMembers();

        $this->addMockMethod(
            'sendRequest',
            new ServiceResponse(
                true,
                '',
                $mockMembersResponse->response->data
            )
        );

        $this->applyMock(VexpensesService::class);

        $member = $mockMembersResponse->response->data[0];

        $this->get(route('vexpenses.team-members'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('vexpenses.team-members'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    [
                        'external_id' => $member->id,
                        'integrated'  => false,
                        'name'        => $member->name,
                        'email'       => $member->email,
                        'phones'      => [
                            [
                                'phone_number' => $member->phone1
                            ],
                            [
                                'phone_number' => $member->phone2
                            ]
                        ],
                    ]
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar acessar a API com um usuário não autorizado
     */
    public function testReturnErrorWhenUserDoesntUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->get(route('vexpenses.team-members'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.team-members'),
                'method'  => 'GET',
                'code'    => 401,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 6
                    ],
                ],
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertUnauthorized();
    }

    /**
     * Retorna erro quando tenta acessar a lista de membros da API sem informar
     * o ExternalToken da integração
     */
    public function testReturnErrorWhenTryListAllMembersAndDoesntExistsExternalToken()
    {
        $this->get(route('vexpenses.team-members'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.team-members'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 24
                    ],
                ],
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna sucesso ao criar um contato com o membro do VExpenses
     */
    public function testReturnSuccessWhenCreateContactWithMember()
    {
        $externalId = $this->faker->numberBetween(1, 100);

        $mockMembersResponse = $this->vexpensesProvider
            ->getMockReturnAllMembers($externalId);

        $mockData = $mockMembersResponse->response->data[0];

        $this->addMockMethod(
            'sendRequest',
            new ServiceResponse(
                true,
                '',
                $mockData
            )
        );

        $this->applyMock(VexpensesService::class);

        $body = [
            'adresses' => [
                [
                    'street_name'  => $this->faker->streetName,
                    'number'       => $this->faker->buildingNumber,
                    'complement'   => $this->faker->secondaryAddress,
                    'neighborhood' => $this->faker->streetSuffix,
                    'city'         => $this->faker->city,
                    'state'        => $this->faker->stateAbbr,
                    'postal_code'  => $this->faker->regexify('[0-9]{5}-[0-9]{3}'),
                    'country'      => $this->faker->country,
                ],
            ],
        ];

        $response =  $this->postJson(route('vexpenses.store', $externalId), $body);

        $contact = $this->user->contacts->first();

        $response->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('vexpenses.store', $externalId),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'id'          => $contact->id,
                    'user_id'     => $this->user->id,
                    'external_id' => $contact->external_id,
                    'name'        => $contact->name,
                    'phones'      => [
                        [
                            'id'           => $contact->phones->first()->id,
                            'phone_number' => $contact->phones->first()->phone_number
                        ],
                        [
                            'id'           => $contact->phones[1]->id,
                            'phone_number' => $contact->phones[1]->phone_number
                        ],
                    ],
                    'adresses' => [
                        [
                            'id'           => $contact->adresses->first()->id,
                            'street_name'  => $contact->adresses->first()->street_name,
                            'number'       => $contact->adresses->first()->number,
                            'complement'   => $contact->adresses->first()->complement,
                            'neighborhood' => $contact->adresses->first()->neighborhood,
                            'city'         => $contact->adresses->first()->city,
                            'state'        => $contact->adresses->first()->state,
                            'postal_code'  => $contact->adresses->first()->postal_code,
                            'country'      => $contact->adresses->first()->country
                        ],
                    ],
                ]
            ], true);
    }

    /**
     * Retorna erro ao tentar criar um contato com um membro do VExpenses que já
     * está integrado a agenda do usuário
     */
    public function testReturnErrorWhenTryCreateContactWithMemberAndHasContactWithMember()
    {
        $externalId = $this->faker->numberBetween(1, 100);

        factory(Contact::class)->create([
            'user_id'     => $this->user->id,
            'external_id' => $externalId
        ]);

        $body = [
            'adresses' => [
                [
                    'street_name'  => $this->faker->streetName,
                    'number'       => $this->faker->buildingNumber,
                    'complement'   => $this->faker->secondaryAddress,
                    'neighborhood' => $this->faker->streetSuffix,
                    'city'         => $this->faker->city,
                    'state'        => $this->faker->stateAbbr,
                    'postal_code'  => $this->faker->regexify('[0-9]{5}-[0-9]{3}'),
                    'country'      => $this->faker->country,
                ],
            ],
        ];

        $this->postJson(route('vexpenses.store', $externalId), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.store', $externalId),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 30
                    ]
                ]
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar criar um contato com um membro do VExpenses onde seu
     * endereço não é informado
     */
    public function testReturnErrorWhenTryCreateContactWithMemberWithoutAddress()
    {
        $externalId = $this->faker->numberBetween(1, 100);

        $this->postJson(route('vexpenses.store', $externalId), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.store', $externalId),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar criar um contato com um membro do VExpenses onde o
     * usuário não tem autorização
     */
    public function testReturnErrorWhenTryCreateContactWithMemberAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $externalId = $this->faker->numberBetween(1, 100);

        $this->postJson(route('vexpenses.store', $externalId), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.store', $externalId),
                'method'  => 'POST',
                'code'    => 401,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 6
                    ],
                ],
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar criar um contato com um membro do VExpenses onde os
     * dados retornados da API são nulos
     */
    public function testReturnErrorWhenMemberResponseOfVexpensesIsNull()
    {
        $externalId = $this->faker->numberBetween(1, 99);

        $mockMembersResponse = $this->vexpensesProvider->getMockReturnNull();

        $this->addMockMethod(
            'sendRequest',
            new ServiceResponse(
                false,
                '',
                $mockMembersResponse->response->data
            )
        );

        $this->applyMock(VexpensesService::class);

        $body = [
            'adresses' => [
                [
                    'street_name'  => $this->faker->streetName,
                    'number'       => $this->faker->buildingNumber,
                    'complement'   => $this->faker->secondaryAddress,
                    'neighborhood' => $this->faker->streetSuffix,
                    'city'         => $this->faker->city,
                    'state'        => $this->faker->stateAbbr,
                    'postal_code'  => $this->faker->regexify('[0-9]{5}-[0-9]{3}'),
                    'country'      => $this->faker->country,
                ],
            ],
        ];

        $this->postJson(route('vexpenses.store', $externalId), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.store', $externalId),
                'method'  => 'POST',
                'code'    => 500,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar criar um contato com um membro do VExpenses onde o
     * o usuário não possui um token de integração
     */
    public function testReturnErrorWhenTryCreateContactWithoutTokenIntegration()
    {
        $externalId = $this->faker->numberBetween(1, 99);

        $body = [
            'adresses' => [
                [
                    'street_name'  => $this->faker->streetName,
                    'number'       => $this->faker->buildingNumber,
                    'complement'   => $this->faker->secondaryAddress,
                    'neighborhood' => $this->faker->streetSuffix,
                    'city'         => $this->faker->city,
                    'state'        => $this->faker->stateAbbr,
                    'postal_code'  => $this->faker->regexify('[0-9]{5}-[0-9]{3}'),
                    'country'      => $this->faker->country,
                ],
            ],
        ];

        $this->postJson(route('vexpenses.store', $externalId), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('vexpenses.store', $externalId),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 24
                    ],
                ],
            ], true)
            ->assertJsonStructure(['errors']);
    }
}
