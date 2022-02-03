<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use App\Models\Contact;

class ContactTest extends BaseTestCase
{
    /**
     * O usuário solicitante da request
     * @var User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $tokenResponse = $this->generateUserAndToken();

        $this->user = $tokenResponse->user;
        $this->withHeaders(['Authorization' => $tokenResponse->token]);
    }

    /**
     * Retorna sucesso ao realizar busca dos contatos do usuário com autenticação
     */
    public function testReturnSuccessWhenListAllContactsOfUserAndIsAuthenticated()
    {
        factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $contact =  $this->user->contacts->first();

        $this->get(route('contacts.index'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.index'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    [
                        'id'           => $contact->id,
                        'name'         => $contact->name,
                        'phone_number' => $contact->phones->first()->phone_number
                    ],
                ],
            ], true);
    }

    /**
     * Retorna sucesso ao realizar busca dos contatos do usuário com autenticação
     * pelo nome do contato
     */
    public function testReturnSuccessWhenListAllContactsWithFilterContactName()
    {
        $contact = factory(Contact::class, 3)->create([
            'user_id' => $this->user->id
        ]);

        $contact =  $this->user->contacts->first();

        $body = [
            'filter' => $contact->name,
        ];

        $this->get(route('contacts.index'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.index'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    [
                        'id'           => $contact->id,
                        'name'         => $contact->name,
                        'phone_number' => $contact->phones->first()->phone_number
                    ],
                ],
            ], true);
    }

    /**
     * Retorna sucesso ao realizar busca dos contatos do usuário com autenticação
     * pelo número de telefone do contato
     */
    public function testReturnSuccessWhenListAllContactsWithFilterPhoneNumber()
    {
        $contact = factory(Contact::class, 5)->create([
            'user_id' => $this->user->id
        ]);

        $contact =  $this->user->contacts->first();
        $phone =  $this->user->contacts->first()->phones->first();

        $body = [
            'filter' => $contact->phones->first()->phone_number,
        ];

        $this->get(route('contacts.index'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.index'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    [
                        'id'           => $contact->id,
                        'name'         => $contact->name,
                        'phone_number' => $phone->phone_number
                    ],
                ],
            ], true);
    }

    /**
     * Retorna erro ao realizar busca dos contatos do usuário sem um token
     * devidamente autorizado
     */
    public function testReturnErrorWhenTryListAllContactsOfUserAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->get(route('contacts.index'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.index'),
                'method'  => 'GET',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ]);
    }

    /**
     * Retorna sucesso ao realizar busca dos detalhes do contato que possui o
     * id informado
     */
    public function testReturnSuccessWhenShowDetailsOfContact()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $phone   = $this->user->contacts->first()->phones->first();
        $address = $this->user->contacts->first()->adresses->first();

        $this->get(route('contacts.show', $contact->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.show', $contact->id),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    'id'      => $this->user->contacts->first()->id,
                    'user_id' => $this->user->id,
                    'name'    => $this->user->contacts->first()->name,
                    'phones'  => [
                        [
                            'id'           => $phone->id,
                            'phone_number' => $phone->phone_number
                        ],
                    ],
                    'adresses' => [
                        [
                            'id'           => $address->id,
                            'street_name'  => $address->street_name,
                            'number'       => $address->number,
                            'complement'   => $address->complement,
                            'neighborhood' => $address->neighborhood,
                            'city'         => $address->city,
                            'state'        => $address->state,
                            'postal_code'  => $address->postal_code,
                            'country'      => $address->country
                        ],
                    ],
                    'tags' => null
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar realizar busca dos detalhes do contato que possui o
     * id informado, mas esse contato não existe
     */
    public function testReturnErrorWhenShowDetailsOfContactThatDoesntExists()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $contact->delete();

        $this->get(route('contacts.show', $contact->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.show', $contact->id),
                'method'  => 'GET',
                'code'    => 200,
                'data'    =>  null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 14
            ]);
    }

    /**
     * Retorna erro ao tentar realizar busca dos detalhes do contato que possui o
     * id informado, mas usuário não tem autenticação
     */
    public function testReturnErrorWhenShowDetailsOfContactWhenUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $idContact = $this->faker->uuid;

        $this->get(route('contacts.show', $idContact))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.show', $idContact),
                'method'  => 'GET',
                'code'    => 401,
                'data'    =>  null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ]);
    }

    /**
     * Retorna sucesso ao criar um contato completo para o usuário
     */
    public function testReturnSuccessWhenCreateNewContactToUser()
    {
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            "name"   => $this->faker->name,
            'phones' => [
                [
                    'phone_number' => $this->faker->phoneNumber,
                ],
            ],
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
            'tags' => [
                [
                    'id' => $tag->id
                ],
            ]
        ];

        $this->postJson(route('contacts.store'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.store'),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'id'           => $this->user->contacts->first()->id,
                    'user_id'      => $this->user->id,
                    'name'         => $this->user->contacts->first()->name,
                    'phones' => [
                        [
                            'id'           => $this->user->contacts->first()->phones->first()->id,
                            'phone_number' => $this->user->contacts->first()->phones->first()->phone_number
                        ],
                    ],
                    'adresses' => [
                        [
                            'id'           => $this->user->contacts->first()->adresses->first()->id,
                            'street_name'  => $this->user->contacts->first()->adresses->first()->street_name,
                            'number'       => $this->user->contacts->first()->adresses->first()->number,
                            'complement'   => $this->user->contacts->first()->adresses->first()->complement,
                            'neighborhood' => $this->user->contacts->first()->adresses->first()->neighborhood,
                            'city'         => $this->user->contacts->first()->adresses->first()->city,
                            'state'        => $this->user->contacts->first()->adresses->first()->state,
                            'postal_code'  => $this->user->contacts->first()->adresses->first()->postal_code,
                            'country'      => $this->user->contacts->first()->adresses->first()->country
                        ],
                    ],
                    'tags' => [
                        [
                            'id' => $tag->id,
                            'description' => $tag->description
                        ]
                    ],
                ],
            ], true);
    }

    /**
     * Retorna sucesso ao criar um contato para o usuário que não possui
     * vínculo com nenhuma tag
     */
    public function testReturnSuccessWhenCreateNewContactToUserWithoutTag()
    {
        $body = [
            "name"   => $this->faker->name,
            'phones' => [
                [
                    'phone_number' => $this->faker->phoneNumber,
                ],
            ],
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
            'tags' => [],
        ];

        $this->postJson(route('contacts.store'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.store'),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'id'      => $this->user->contacts->first()->id,
                    'user_id' => $this->user->id,
                    'name'    => $this->user->contacts->first()->name,
                    'phones'  => [
                        [
                            'id'           => $this->user->contacts->first()->phones->first()->id,
                            'phone_number' => $this->user->contacts->first()->phones->first()->phone_number
                        ],
                    ],
                    'adresses' => [
                        [
                            'id'           => $this->user->contacts->first()->adresses->first()->id,
                            'street_name'  => $this->user->contacts->first()->adresses->first()->street_name,
                            'number'       => $this->user->contacts->first()->adresses->first()->number,
                            'complement'   => $this->user->contacts->first()->adresses->first()->complement,
                            'neighborhood' => $this->user->contacts->first()->adresses->first()->neighborhood,
                            'city'         => $this->user->contacts->first()->adresses->first()->city,
                            'state'        => $this->user->contacts->first()->adresses->first()->state,
                            'postal_code'  => $this->user->contacts->first()->adresses->first()->postal_code,
                            'country'      => $this->user->contacts->first()->adresses->first()->country
                        ],
                    ],
                    'tags' => null,
                ],
            ], true);
    }

    /**
     * Retorna erro ao criar um contato para o usuário que não possui
     * vínculo com nenhuma tag mas esse usuário não tem autenticação
     */
    public function testReturnErrorWhenTryCreateNewContactWhenUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->postJson(route('contacts.store'), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.store'),
                'method'  => 'POST',
                'code'    => 401,
                'data'    =>  null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ]);
    }

    /**
     * Retorna erro ao tentar criar um contato para o usuário quando o nome do
     * contato a ser criado já está cadastrado
     */
    public function testReturnErrorWhenTryCreateNewContactThatContactNameExists()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            "name"   => $contact->name,
            'phones' => [
                [
                    'phone_number' => $this->faker->phoneNumber,
                ],
            ],
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
            'tags' => [],
        ];

        $this->postJson(route('contacts.store'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.store'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true);
    }

    /**
     * Retorna erro ao tentar criar um contato para o usuário quando não é
     * informado um número de telefone para o contato
     */
    public function testReturnErrorWhenCreateNewContactAndDoesntInformationPhone()
    {
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            "name"     => $this->faker->name,
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
            'tags' => [
                [
                    'id' => $tag->id
                ],
            ]
        ];

        $this->postJson(route('contacts.store'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.store'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true);
    }

    /**
     * Retorna erro ao tentar criar um contato para o usuário quando não é
     * informado o nome do contato
     */
    public function testReturnErrorWhenCreateNewContactAndDoesntInformationNameContact()
    {
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            'phones' => [
                [
                    'phone_number' => $this->faker->phoneNumber,
                ],
            ],
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
            'tags' => [
                [
                    'id' => $tag->id
                ],
            ]
        ];

        $this->postJson(route('contacts.store'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.store'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true);
    }

    /**
     * Retorna erro ao tentar criar um contato para o usuário quando não é
     * informado um endereço para o contato
     */
    public function testReturnErrorWhenCreateNewContactAndDoesntInformationAddress()
    {
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            "name" => $this->faker->name,
            'phones' => [
                [
                    'phone_number' => $this->faker->phoneNumber,
                ],
            ],
            'tags' => [
                [
                    'id' => $tag->id
                ],
            ]
        ];

        $this->postJson(route('contacts.store'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.store'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true);
    }

    /**
     * Retorna sucesso ao atualizar o nome de um contato
     */
    public function testReturnSuccessWhenUpdateContactName()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $newContactName = $this->faker->name;

        $body = [
            'name' => $newContactName,
        ];

        $phone   = $this->user->contacts->first()->phones->first();
        $address = $this->user->contacts->first()->adresses->first();

        $this->patchJson(route('contacts.update', $contact->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.update', $contact->id),
                'method'  => 'PATCH',
                'code'    => 200,
                'data'    => [
                    'id'           => $this->user->contacts->first()->id,
                    'user_id'      => $this->user->id,
                    'name'         => $newContactName,
                    'phones' => [
                        [
                            'id'           => $phone->id,
                            'phone_number' => $phone->phone_number
                        ],
                    ],
                    'adresses' => [
                        [
                            'id'           => $address->id,
                            'street_name'  => $address->street_name,
                            'number'       => $address->number,
                            'complement'   => $address->complement,
                            'neighborhood' => $address->neighborhood,
                            'city'         => $address->city,
                            'state'        => $address->state,
                            'postal_code'  => $address->postal_code,
                            'country'      => $address->country
                        ],
                    ],
                    'tags' => null
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar atualizar o nome de um contato e o nome informado
     * já pertence a outro contato cadastrado
     */
    public function testReturnErrorWhenUpdateContactNameThatThisContactNameExists()
    {
        $principalContact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $secondaryContact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            'name' => $secondaryContact->name,
        ];

        $this->patchJson(route('contacts.update', $principalContact->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.update', $principalContact->id),
                'method'  => 'PATCH',
                'code'    => 422,
                'data'    => null,
            ], true);
    }

    /**
     * Retorna erro ao tentar atualizar o nome de um contato e o usuário não
     * tem autenticação
     */
    public function testReturnErrorWhenUpdateContactNameAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $idContact = $this->faker->uuid;

        $this->patchJson(route('contacts.update', $idContact), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.update', $idContact),
                'method'  => 'PATCH',
                'code'    => 401,
                'data'    =>  null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ]);
    }

    /**
     * Retorna sucesso ao deletar um contato
     */
    public function testReturnSuccessWhenDeleteContact()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $this->deleteJson(route('contacts.delete', $contact->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('contacts.delete', $contact->id),
                'method'  => 'DELETE',
                'code'    => 200,
                'data'    => null,
            ], true);

        $this->assertEmpty($this->user->contacts);
    }

    /**
     * Retorna erro ao deletar um contato quando o usuário não tem autenticação
     */
    public function testReturnErrorWhenTryDeleteContactAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $idContact = $this->faker->uuid;

        $this->deleteJson(route('contacts.delete', $idContact))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.delete', $idContact),
                'method'  => 'DELETE',
                'code'    => 401,
                'data'    =>  null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ]);
    }


    /**
     * Retorna erro ao tentar deletar um contato que não existe
     */
    public function testReturnErrorWhenDeleteContactAndDoesntExists()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $contact->delete();

        $this->deleteJson(route('contacts.delete', $contact->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.delete', $contact->id),
                'method'  => 'DELETE',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 14,
                    ],
                ],
            ], true)
            ->assertJsonStructure(['errors']);

        $this->assertEmpty($this->user->contacts);
    }

    /**
     * Retorna erro ao tentar deletar um contato de outro usuário
     */
    public function testReturnErrorWhenTryDeleteContactOtherUser()
    {
        $contact = factory(Contact::class)->create();

        $this->deleteJson(route('contacts.delete', $contact->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('contacts.delete', $contact->id),
                'method'  => 'DELETE',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 14,
                    ],
                ],
            ], true)
            ->assertJsonStructure(['errors']);
    }
}
