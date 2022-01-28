<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Contact;

class AddressTest extends BaseTestCase
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
     * Retorna sucesso ao realizar a atualização de dados do endereço ligado ao
     * contato do usuário
     */
    public function testReturnSuccessWhenUpdateAddress()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $address = $contact->adresses->first();

        $newComplement = $this->faker->secondaryAddress;

        $body = [
            'complement'   => $newComplement,
        ];

        $this->patchJson(route('address.update', $address->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('address.update', $address->id),
                'method'  => 'PATCH',
                'code'    => 200,
                'data'    => [
                    'id'           => $address->id,
                    'street_name'  => $address->street_name,
                    'number'       => $address->number,
                    'complement'   => $newComplement,
                    'neighborhood' => $address->neighborhood,
                    'city'         => $address->city,
                    'state'        => $address->state,
                    'postal_code'  => $address->postal_code,
                    'country'      => $address->country,
                ],
            ], true);

        $this->assertNotEquals($newComplement, $address->complement);
        $this->assertEquals($address->contact_id, $contact->id);
        $this->assertEquals($address->id, $this->user->contacts->first()->adresses->first()->id);
    }

    /**
     * Retorna erro ao tentar realizar a atualização de dados do endereço ligado
     * ao contato do usuário mas não possui autorização para a alteração
     */
    public function testReturnErrorWhenTryUpdateAddressAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $address = $contact->adresses->first();

        $body = [
            'state' => $this->faker->stateAbbr,
        ];

        $this->patchJson(route('address.update', $address->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('address.update', $address->id),
                'method'  => 'PATCH',
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
     * Retorna erro ao tentar realizar a atualização de dados do endereço ligado
     * ao contato de outro usuário
     */
    public function testReturnErrorWhenTryUpdateAddressOfOtherUser()
    {
        $contact = factory(Contact::class)->create();

        $address = $contact->adresses->first();

        $body = [
            'neighborhood' => $this->faker->streetSuffix,
        ];

        $this->patchJson(route('address.update', $address->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('address.update', $address->id),
                'method'  => 'PATCH',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 15
                    ],
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar realizar a atualização de dados de um endereço
     * que não existe
     */
    public function testReturnErrorWhenTryUpdateAddressThatDoesntExists()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $address = $contact->adresses->first();

        $address->delete();

        $body = [
            'street_name' => $this->faker->streetName,
        ];

        $this->patchJson(route('address.update', $address->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('address.update', $address->id),
                'method'  => 'PATCH',
                'code'    => 200,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 9
            ]);

        $this->assertEmpty($this->user->contacts->first()->adresses);
    }

    /**
     * Retorna sucesso ao realizar a deleção de um endereço ligado ao contato
     */
    public function testReturnSuccessWhenDeleteAddress()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $address = $contact->adresses->first();

        $this->deleteJson(route('address.delete', $address->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => true,
                'request' => route('address.delete', $address->id),
                'method'  => 'DELETE',
                'code'    => 200,
                'data'    => null,
            ], true);

        $this->assertEmpty($this->user->contacts->first()->adresses);
    }

    /**
     * Retorna erro ao tentar realizar a deleção de um endereço ligado ao contato
     * mas não possui autorização para a deleção
     */
    public function testReturnErrorWhenTryDeleteAddressAndUserIsUnauthorized()
    {
        $this->withHeaders(['Authorization' => $this->generateUnauthorizedToken()]);

        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $address = $contact->adresses->first();

        $this->deleteJson(route('address.delete', $address->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
                'request' => route('address.delete', $address->id),
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
     * Retorna erro ao tentar realizar a deleção de um endereço ligado ao contato
     * de outro usuário
     */
    public function testReturnErrorWhenTryDeleteAddressOfOtherUser()
    {
        $contact = factory(Contact::class)->create();

        $address = $contact->adresses->first();

        $newComplement = $this->faker->secondaryAddress;

        $body = [
            'complement' => $newComplement,
        ];

        $this->patchJson(route('address.update', $address->id), $body)
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('address.update', $address->id),
                'method'  => 'PATCH',
                'code'    => 200,
                'data'    => null,
                'errors'  => [
                    [
                        'code' => 15
                    ],
                ],
            ], true);
    }

    /**
     * Retorna erro ao tentar realizar a deleção de um endereço que não existe
     */
    public function testReturnErrorWhenTryDeleteAddressThatDoesntExists()
    {
        $contact = factory(Contact::class)->create([
            'user_id' => $this->user->id
        ]);

        $address = $contact->adresses->first();

        $address->delete();

        $this->deleteJson(route('address.delete', $address->id))
            ->assertHeader('content-type', 'application/json')
            ->assertStatus(200)
            ->assertJson([
                'success' => false,
                'request' => route('address.delete', $address->id),
                'method'  => 'DELETE',
                'code'    => 200,
                'data'    => null,
            ], true)
            ->assertJsonStructure(['errors'])
            ->assertJsonFragment([
                'code' => 9
            ]);

        $this->assertEmpty($this->user->contacts->first()->adresses);
    }
}
