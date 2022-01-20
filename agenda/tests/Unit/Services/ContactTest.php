<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Models\Phone;
use App\Models\Contact;
use App\Models\Tag;
use App\Services\Responses\ServiceResponse;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Contracts\ContactServiceInterface;
use App\Services\Params\Contact\CreateCompleteContactsServiceParams;

class ContactTest extends BaseTestCase
{
    /**
     * @var ContactServiceInterface
     */
    protected $contactService;

    public function setUp(): void
    {
        parent::setUp();

        $this->contactService = app(ContactServiceInterface::class);
    }

    /**
     * Testa o método FindAllWithFilter na ContactService retornando sucesso ao
     * buscar todos os contatos vinculados ao usuário
     */
    public function testReturnSuccessWhenFindAllContactsOfUser()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        factory(Phone::class, 2)->create([
            'contact_id' => $contact->id
        ]);

        $findAllContactsResponse = $this->contactService->findAllWithFilter(
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $findAllContactsResponse);
        $this->assertInstanceOf(Collection::class, $findAllContactsResponse->data);
        $this->assertIsBool($findAllContactsResponse->success);
        $this->assertTrue($findAllContactsResponse->success);
        $this->assertNotEmpty($user->contacts);
        $this->assertNotEmpty($contact->phones);
    }

    /**
     * Testa o método FindAllWithFilter na ContactService retornando sucesso ao
     * realizar a busca filtrada de um contato pelo seu nome
     */
    public function testReturnSuccessWhenFindContactWithFilterContactName()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        factory(Phone::class, 2)->create([
            'contact_id' => $contact->id
        ]);

        $findAllContactsResponse = $this->contactService->findAllWithFilter(
            $user->id,
            $contact->name
        );

        $data = $findAllContactsResponse->data->first();

        $this->assertInstanceOf(ServiceResponse::class, $findAllContactsResponse);
        $this->assertInstanceOf(Collection::class, $findAllContactsResponse->data);
        $this->assertInstanceOf(Contact::class, $data);
        $this->assertIsObject($data);
        $this->assertIsBool($findAllContactsResponse->success);
        $this->assertTrue($findAllContactsResponse->success);
        $this->assertNotEmpty($user->contacts);
        $this->assertNotEmpty($contact->phones);
        $this->assertEquals($data->name, $contact->name);
    }

    /**
     * Testa o método FindAllWithFilter na ContactService retornando sucesso ao
     * realizar a busca filtrada de um contato pelo número de telefone que está
     * associado a ele
     */
    public function testReturnSuccessWhenFindContactWithFilterPhoneNumber()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $phone = factory(Phone::class)->create([
            'contact_id' => $contact->id
        ]);

        $findAllContactsResponse = $this->contactService->findAllWithFilter(
            $user->id,
            $phone->phone_number
        );

        $data = $findAllContactsResponse->data->first();

        $this->assertInstanceOf(ServiceResponse::class, $findAllContactsResponse);
        $this->assertInstanceOf(Collection::class, $findAllContactsResponse->data);
        $this->assertInstanceOf(Contact::class, $data);
        $this->assertNotFalse($findAllContactsResponse->success);
        $this->assertNotEmpty($user->contacts);
        $this->assertNotEmpty($contact->phones);
        $this->assertEquals($data->name, $contact->name);
    }

    /**
     * Testa o método FindAllWithFilter na ContactService retornando sucesso ao
     * tentar buscar um contato que o nome não existe
     */
    public function testFindAllWithFilterReturnSuccessWhenContactDoesntExists()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $contact->delete();

        factory(Phone::class)->create([
            'contact_id' => $contact->id
        ]);

        $findAllContactsResponse = $this->contactService->findAllWithFilter(
            $user->id,
            $contact->name
        );

        $this->assertInstanceOf(ServiceResponse::class, $findAllContactsResponse);
        $this->assertTrue($findAllContactsResponse->success);
        $this->assertEmpty($findAllContactsResponse->data);
        $this->assertEmpty($user->contacts);
    }

    /**
     * Testa o método Find na ContactService retornando sucesso ao realizar uma
     * busca por um contato do usuário
     */
    public function testReturnSuccessWhenFindContactOfUser()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $findContactResponse = $this->contactService->find(
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $findContactResponse);
        $this->assertTrue($findContactResponse->success);
        $this->assertNotNull($findContactResponse->data);
        $this->assertEquals($findContactResponse->data->id, $contact->id);
        $this->assertEquals($findContactResponse->data->user_id, $user->id);
    }

    /**
     * Testa o método Find na ContactService retornando erro tentar realizar
     * uma busca de contato com um usuário que não existe
     */
    public function testFindContactReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $findContactResponse = $this->contactService->find(
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $findContactResponse);
        $this->assertFalse($findContactResponse->success);
        $this->assertNull($findContactResponse->data);
        $this->assertHasInternalError($findContactResponse, 3);
    }

    /**
     * Testa o método Find na ContactService retornando erro tentar realizar
     * uma busca por um contato que não existe
     */
    public function testFindContactReturnErrorWhenContactDoesntExists()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $contact->delete();

        $findContactResponse = $this->contactService->find(
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $findContactResponse);
        $this->assertTrue($findContactResponse->success);
        $this->assertIsBool($findContactResponse->success);
        $this->assertNull($findContactResponse->data);
        $this->assertHasInternalError($findContactResponse, 14);
    }

    /**
     * Testa o método Find na ContactService retornando erro tentar realizar
     * uma busca por um contato que não pertence ao usuário
     */
    public function testFindContactReturnErrorWhenContactIsOfOtherUser()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create();

        $findContactResponse = $this->contactService->find(
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $findContactResponse);
        $this->assertTrue($findContactResponse->success);
        $this->assertNull($findContactResponse->data);
        $this->assertHasInternalError($findContactResponse, 14);
    }

    /**
     * Testa o método Store na ContactService retornando sucesso ao realizar uma
     * criação de um contato completo para o usuário. Possuindo telefones,
     * endereços e tags que serão vinculadas ao contato que está sendo criado
     */
    public function testReturnSuccessWhenCreateContact()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $tags = [
            [
                'id' => $tag->id
            ],
        ];

        $phones = [
            [
                'phone_number' => $this->faker->phoneNumber,
            ],
            [
                'phone_number' => $this->faker->phoneNumber,
            ],
        ];

        $adresses = [
            [
                'street_name'  => $this->faker->streetName,
                'number'       => $this->faker->buildingNumber,
                'complement'   => $this->faker->secondaryAddress,
                'neighborhood' => $this->faker->streetSuffix,
                'city'         => $this->faker->city,
                'state'        => $this->faker->state,
                'postal_code'  => $this->faker->postcode,
                'country'      => $this->faker->country,
            ],
        ];

        $newContactResponse = $this->contactService->store(
            new CreateCompleteContactsServiceParams(
                $this->faker->name,
                $user->id,
                $phones,
                $adresses,
                $tags
            )
        );

        $data = $newContactResponse->data->tagcontacts->first();

        $this->assertInstanceOf(ServiceResponse::class, $newContactResponse);
        $this->assertInstanceOf(Contact::class, $newContactResponse->data);
        $this->assertTrue($newContactResponse->success);
        $this->assertNotNull($newContactResponse->data);
        $this->assertNotNull($newContactResponse->data->phones);
        $this->assertNotNull($newContactResponse->data->adresses);
        $this->assertNotNull($newContactResponse->data->tagcontacts);
        $this->assertEquals($data->tag_id, $tag->id);
    }

    /**
     * Testa o método Store na ContactService retornando erro ao realizar uma
     * criação de um contato completo para o usuário quando o id da tag
     * é de uma tag que não existe
     */
    public function testStoreReturnErrorWhenCreateContactAndTagDoesntExists()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $tag->delete();

        $tags = [
            [
                'id' => $tag->id
            ],
        ];

        $phones = [
            [
                'phone_number' => $this->faker->phoneNumber,
            ],
        ];

        $adresses = [
            [
                'street_name'  => $this->faker->streetName,
                'number'       => $this->faker->buildingNumber,
                'complement'   => $this->faker->secondaryAddress,
                'neighborhood' => $this->faker->streetSuffix,
                'city'         => $this->faker->city,
                'state'        => $this->faker->state,
                'postal_code'  => $this->faker->postcode,
                'country'      => $this->faker->country,
            ],
        ];

        $newContactResponse = $this->contactService->store(
            new CreateCompleteContactsServiceParams(
                $this->faker->name,
                $user->id,
                $phones,
                $adresses,
                $tags
            )
        );

        $this->assertInstanceOf(ServiceResponse::class, $newContactResponse);
        $this->assertFalse($newContactResponse->success);
        $this->assertNull($newContactResponse->data);
        $this->assertHasInternalError($newContactResponse, 11);
    }

    /**
     * Testa o método Update na ContactService retornando sucesso ao realizar
     * a atualização de dados do contato do usuário
     */
    public function testReturnSuccessWhenUpdateContact()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $updateContactResponse = $this->contactService->update(
            $this->faker->name,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateContactResponse);
        $this->assertInstanceOf(Contact::class, $updateContactResponse->data);
        $this->assertTrue($updateContactResponse->success);
        $this->assertNotNull($updateContactResponse->data);
        $this->assertNotEquals($updateContactResponse->data->name, $contact->name);
    }

    /**
     * Testa o método Update na ContactService retornando erro ao tentar realizar
     * a atualização de dados de um contato, quando o usuário que está vinculado
     * ao contato não existe
     */
    public function testUpdateReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $updateContactResponse = $this->contactService->update(
            $this->faker->name,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateContactResponse);
        $this->assertFalse($updateContactResponse->success);
        $this->assertNull($updateContactResponse->data);
        $this->assertHasInternalError($updateContactResponse, 3);
    }

    /**
     * Testa o método Update na ContactService retornando erro ao tentar realizar
     * a atualização de dados de um contato que não existe
     */
    public function testUpdateReturnErrorWhenContactDoesntExists()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $contact->delete();

        $updateContactResponse = $this->contactService->update(
            $this->faker->name,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateContactResponse);
        $this->assertNotTrue($updateContactResponse->success);
        $this->assertIsBool($updateContactResponse->success);
        $this->assertNull($updateContactResponse->data);
        $this->assertHasInternalError($updateContactResponse, 14);
    }

    /**
     * Testa o método Update na ContactService retornando erro ao tentar realizar
     * a atualização de dados de um contato que não está vinculado ao usuário
     */
    public function testUpdateReturnErrorWhenContacIsOfOtherUser()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create();

        $updateContactResponse = $this->contactService->update(
            $this->faker->name,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateContactResponse);
        $this->assertNotTrue($updateContactResponse->success);
        $this->assertIsBool($updateContactResponse->success);
        $this->assertNull($updateContactResponse->data);
        $this->assertHasInternalError($updateContactResponse, 14);
    }

    /**
     * Testa o método Delete na ContactService retornando sucesso ao realizar
     * a deleçao de um contato do usuário
     */
    public function testReturnSuccessWhenDeleteContact()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $updateContactResponse = $this->contactService->delete(
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateContactResponse);
        $this->assertTrue($updateContactResponse->success);
        $this->assertNull($updateContactResponse->data);
    }

    /**
     * Testa o método Delete na ContactService retornando erro ao tentar deletar
     * um contato de um usuário que não existe
     */
    public function testDeleteReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $updateContactResponse = $this->contactService->delete(
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateContactResponse);
        $this->assertFalse($updateContactResponse->success);
        $this->assertIsBool($updateContactResponse->success);
        $this->assertNull($updateContactResponse->data);
        $this->assertHasInternalError($updateContactResponse, 3);
    }

    /**
     * Testa o método Delete na ContactService retornando erro ao tentar deletar
     * um contato que não existe
     */
    public function testDeleteReturnErrorWhenContactDoesntExists()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $contact->delete();

        $updateContactResponse = $this->contactService->delete(
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateContactResponse);
        $this->assertNotTrue($updateContactResponse->success);
        $this->assertNull($updateContactResponse->data);
        $this->assertHasInternalError($updateContactResponse, 14);
    }

    /**
     * Testa o método Delete na ContactService retornando erro ao tentar deletar
     * um contato que pertence a outro usuario
     */
    public function testDeleteReturnErrorWhenContactIsOfOtherUser()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create();

        $updateContactResponse = $this->contactService->delete(
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $updateContactResponse);
        $this->assertFalse($updateContactResponse->success);
        $this->assertIsBool($updateContactResponse->success);
        $this->assertNull($updateContactResponse->data);
        $this->assertHasInternalError($updateContactResponse, 14);
    }
}
