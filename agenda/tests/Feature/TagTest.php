<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use App\Models\Contact;

class TagTest extends BaseTestCase
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
     * Retorna sucesso ao listar todas as tags do usuário
     */
    public function testReturnSuccessWhenListAllTags()
    {
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $this->get(route('tags.index'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('tags.index'),
                'method'  => 'GET',
                'code'    => 200,
                'data'    => [
                    [
                        'id'          => $tag->id,
                        'description' => $tag->description,
                    ]
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar listar todas as tags mas o usuário não
     * possui autorização
     */
    public function testReturnErrorWhenTryListAllTagsAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->get(route('tags.index'))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('tags.index'),
                'method'  => 'GET',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ])
            ->assertUnauthorized();
    }

    /**
     * Retorna sucesso ao criar uma tag para o usuário
     */
    public function testReturnSuccessWhenCreateNewTag()
    {
        $body = [
            'description' => $this->faker->word,
        ];

        $this->postJson(route('tags.store'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('tags.store'),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'id'          => $this->user->tags->first()->id,
                    'description' => $body['description'],
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar criar uma tag com o nome de uma tag já existente
     * para o usuário
     */
    public function testReturnErrorWhenTryCreateNewTagWhenDescriptionExistsInOtherTag()
    {
        $description = $this->faker->word;

        factory(Tag::class)->create([
            'description' => $description,
            'user_id'     => $this->user->id,
        ]);

        $body = [
            'description' => $description,
        ];

        $this->postJson(route('tags.store'), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'request' => route('tags.store'),
                'method'  => 'POST',
                'code'    => 422,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors']);
    }

    /**
     * Retorna erro ao tentar criar uma tag para o usuário sem autenticação
     */
    public function testReturnErrorWhenCreateNewTagAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $this->postJson(route('tags.store'), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('tags.store'),
                'method'  => 'POST',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ])
            ->assertUnauthorized();
    }

    /**
     * Retorna sucesso ao atualizar a descrição de uma tag
     */
    public function testReturnSuccessWhenUpdateTag()
    {
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $newTagDescription = $this->faker->word;

        $body = [
            'description' => $newTagDescription,
        ];

        $this->patchJson(route('tags.update', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('tags.update', $tag->id),
                'method'  => 'PATCH',
                'code'    => 200,
                'data'    => [
                    'id'          => $tag->id,
                    'description' => $newTagDescription,
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar atualizar a descrição de uma tag que pertence a
     * outro usuário
     */
    public function testReturnErrorWhenUpdateTagOtherUser()
    {
        $tag = factory(Tag::class)->create();

        $newTagDescription = $this->faker->word;

        $body = [
            'description' => $newTagDescription,
        ];

        $this->patchJson(route('tags.update', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.update', $tag->id),
                'method'  => 'PATCH',
                'code'    => 200,
                'data'    => null,
                'errors'    => [
                    [
                        'code' => 11,
                    ]
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar atualizar a descrição de uma tag e o usuário
     * não tem autorização
     */
    public function testReturnErrorWhenUpdateTagAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $idTag = $this->faker->uuid;

        $this->patchJson(route('tags.update', $idTag), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('tags.update', $idTag),
                'method'  => 'PATCH',
                'code'    => 401,
                'data'    => null
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ])
            ->assertUnauthorized();
    }

    /**
     * Retorna sucesso ao deletar uma tag do usuário
     */
    public function testReturnSuccessWhenDeleteTag()
    {
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $this->deleteJson(route('tags.delete', $tag->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('tags.delete', $tag->id),
                'method'  => 'DELETE',
                'code'    => 200,
                'data'    => null,
            ], true);

        $this->assertEmpty($this->user->tags);
    }

    /**
     * Retorna erro ao tentar deletar uma tag do usuário que não existe
     */
    public function testReturnErrorWhenTryDeleteTagAndDoesntExists()
    {
        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $tag->delete();

        $this->deleteJson(route('tags.delete', $tag->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.delete', $tag->id),
                'method'  => 'DELETE',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 11,
                    ]
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar deletar uma tag de outro usuário
     */
    public function testReturnErrorWhenDeleteTagOtherUser()
    {
        $tag = factory(Tag::class)->create();

        $this->deleteJson(route('tags.delete', $tag->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.delete', $tag->id),
                'method'  => 'DELETE',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 11,
                    ]
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar deletar uma tag quando o usuário
     * não tem autorização
     */
    public function testReturnErrorWhenDeleteTagAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $idTag = $this->faker->uuid;

        $this->deleteJson(route('tags.delete', $idTag))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('tags.delete', $idTag),
                'method'  => 'DELETE',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ])
            ->assertUnauthorized();
    }

    /**
     * Retorna sucesso ao criar um vínculo de uma tag com um contato
     */
    public function testReturnSuccessWhenAttachTagAndContact()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.attach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('tags.attach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => [
                    'id'         => $this->user->tags->first()->tagContacts->first()->id,
                    'tag_id'     => $this->user->tags->first()->tagContacts->first()->tag_id,
                    'contact_id' => $this->user->tags->first()->tagContacts->first()->contact_id
                ],
            ], true);

        $this->assertNotNull($this->user->tags->first()->tagContacts->first());
    }

    /**
     * Retorna erro ao tentar criar um vínculo de uma tag com um contato quando
     * a tag não existe
     */
    public function testReturnErrorWhenTryAttachTagAndContactAndTagDoesntExists()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $tag->delete();

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.attach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.attach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 11,
                    ]
                ],
            ], true);

        $this->assertEmpty($this->user->tags->first());
        $this->assertNotEmpty($this->user->contacts->first());
    }

    /**
     * Retorna erro ao tentar criar um vínculo de uma tag com um contato quando
     * o contato não existe
     */
    public function testReturnErrorWhenTryAttachTagAndContactAndContactDoesntExists()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $contact->delete();

        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.attach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.attach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 14,
                    ]
                ],
            ], true);

        $this->assertNotEmpty($this->user->tags->first());
        $this->assertEmpty($this->user->contacts->first());
    }

    /**
     * Retorna erro ao tentar criar um vínculo de uma tag com um contato quando
     * a tag pertence a outro usuário
     */
    public function testReturnErrorWhenTryAttachTagAndContactAndTagIsOfOtherUser()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $tag = factory(Tag::class)->create();

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.attach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.attach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 11,
                    ]
                ],
            ], true);

        $this->assertEmpty($this->user->tags->first());
    }

    /**
     * Retorna erro ao tentar criar um vínculo de uma tag com um contato quando
     * o contato pertence a outro usuário
     */
    public function testReturnErrorWhenTryAttachTagAndContactAndContactIsOfOtherUser()
    {
        $contact = factory(Contact::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.attach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.attach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 14,
                    ]
                ],
            ], true);

        $this->assertEmpty($this->user->contacts->first());
    }

    /**
     * Retorna erro ao tentar criar um vínculo de uma tag com um contato e o
     * usuário não tem autorização
     */
    public function testReturnErrorWhenTryAttachTagAndContactAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $idTag = $this->faker->uuid;

        $this->postJson(route('tags.attach', $idTag), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('tags.attach', $idTag),
                'method'  => 'POST',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ])
            ->assertUnauthorized();
    }

    /**
     * Retorna sucesso ao desvincular uma tag de um contato
     */
    public function testReturnSuccessWhenDetachTagAndContact()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.detach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('tags.detach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
            ], true);

        $this->assertEmpty($this->user->tags->first()->tagContacts->first());
    }

    /**
     * Retorna erro ao tentar desvincular uma tag de um contato quando
     * a tag não existe
     */
    public function testReturnErrorWhenTryDetachTagAndContactAndTagDoesntExists()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $tag->delete();

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.detach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.detach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 11,
                    ]
                ],
            ], true);

        $this->assertEmpty($this->user->tags->first());
    }

    /**
     * Retorna erro ao tentar desvincular uma tag de um contato quando
     * o contato não existe
     */
    public function testReturnErrorWhenTryDetachTagAndContactAndContactDoesntExists()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $contact->delete();

        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.detach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.detach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 14,
                    ]
                ],
            ], true);

        $this->assertEmpty($this->user->contacts->first());
    }

    /**
     * Retorna erro ao tentar desvincular uma tag de um contato quando
     * a tag pertence a outro usuário
     */
    public function testReturnErrorWhenTryDetachTagAndContactAndTagIsOfOtherUser()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $tag = factory(Tag::class)->create();

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.detach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.detach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 11,
                    ]
                ],
            ], true);

        $this->assertEmpty($this->user->tags->first());
    }

    /**
     * Retorna erro ao tentar desvincular uma tag de um contato quando
     * o contato pertence a outro usuário
     */
    public function testReturnErrorWhenTryDetachTagAndContactAndContactIsOfOtherUser()
    {
        $contact = factory(Contact::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $this->user->id
        ]);

        $body = [
            'contact_id' => $contact->id,
        ];

        $this->postJson(route('tags.detach', $tag->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('tags.detach', $tag->id),
                'method'  => 'POST',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 14,
                    ]
                ],
            ], true);

        $this->assertEmpty($this->user->contacts->first());
    }

    /**
     * Retorna erro ao tentar desvincular uma tag de um contato quando
     * usuário não tem autorização
     */
    public function testReturnErrorWhenTryDetachTagAndContactAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $idTag = $this->faker->uuid;

        $this->postJson(route('tags.detach', $idTag), [])
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('tags.detach', $idTag),
                'method'  => 'POST',
                'code'    => 401,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 6
            ])
            ->assertUnauthorized();
    }
}
