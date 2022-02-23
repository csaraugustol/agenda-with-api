<?php

namespace Tests\Unit\Services;

use App\Models\Tag;
use App\Models\User;
use App\Models\Contact;
use App\Models\TagContact;
use App\Services\Responses\ServiceResponse;
use App\Services\Contracts\TagContactServiceInterface;

class TagContactTest extends BaseTestCase
{
    /**
     * @var TagContactServiceInterface
     */
    protected $tagContactService;

    public function setUp(): void
    {
        parent::setUp();

        $this->tagContactService = app(TagContactServiceInterface::class);
    }

    /**
     * Testa o método Attach na service TagContactService retornando sucesso ao
     * vincular uma tag e um contato pertencentes ao usuário
     */
    public function testReturnSuccessWhenAttachTagContact()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $attachTagContactResponse = $this->tagContactService->attach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $attachTagContactResponse);
        $this->assertTrue($attachTagContactResponse->success);
        $this->assertNotNull($attachTagContactResponse->data);
    }

    /**
     * Testa o método Attach na service TagContactService retornando sucesso ao
     * vincular uma tag e um contato pertencentes ao usuário, onde o vínculo dessa
     * tag e do contato já existiam, assim, gerando apenas a restauração do vínculo
     */
    public function testReturnSuccessWhenAttachTagContactThatAttachWasDeletedAndRestored()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $tagContact = factory(TagContact::class)->create([
            'tag_id'     => $tag->id,
            'contact_id' => $contact->id
        ]);

        //Armazena o id do vínculo antes de deletar
        $tagContactId = $tagContact->id;

        $tagContact->delete();

        $attachTagContactResponse = $this->tagContactService->attach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $attachTagContactResponse);
        $this->assertTrue($attachTagContactResponse->success);
        $this->assertNotNull($attachTagContactResponse->data);
        $this->assertEquals($attachTagContactResponse->data->id, $tagContactId);
    }

    /**
     * Testa o método Attach na service TagContactService retornando sucesso ao
     * tentar vincular uma tag e um contato que já possuem vinculo
     */
    public function testReturnSuccessWhenAttachTagContactWhereExistsAttach()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $tagContact = factory(TagContact::class)->create([
            'tag_id'     => $tag->id,
            'contact_id' => $contact->id
        ]);

        $attachTagContactResponse = $this->tagContactService->attach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $attachTagContactResponse);
        $this->assertTrue($attachTagContactResponse->success);
        $this->assertNotNull($attachTagContactResponse->data);
        $this->assertEquals($attachTagContactResponse->data->id, $tagContact->id);
    }

    /**
     * Testa o método Attach na service TagContactService retornando erro ao
     * tentar realizar um vínculo com uma tag que não existe
     */
    public function testReturnErrorWhenTagDoesntExists()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $tag->delete();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $attachTagContactResponse = $this->tagContactService->attach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $attachTagContactResponse);
        $this->assertNotTrue($attachTagContactResponse->success);
        $this->assertIsBool($attachTagContactResponse->success);
        $this->assertNull($attachTagContactResponse->data);
        $this->assertHasInternalError($attachTagContactResponse, 11);
    }

    /**
     * Testa o método Attach na service TagContactService retornando erro ao
     * tentar realizar um vínculo com um contato que não existe
     */
    public function testReturnErrorWhenContactDoesntExists()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $contact->delete();

        $attachTagContactResponse = $this->tagContactService->attach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $attachTagContactResponse);
        $this->assertFalse($attachTagContactResponse->success);
        $this->assertNull($attachTagContactResponse->data);
        $this->assertHasInternalError($attachTagContactResponse, 14);
    }

    /**
     * Testa o método Attach na service TagContactService retornando erro ao
     * tentar realizar um vínculo entre uma tag e um contato, de um usuário
     * que não existe
     */
    public function testReturnErrorWhenUserDoesntExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);


        $attachTagContactResponse = $this->tagContactService->attach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $attachTagContactResponse);
        $this->assertIsBool($attachTagContactResponse->success);
        $this->assertFalse($attachTagContactResponse->success);
        $this->assertNull($attachTagContactResponse->data);
        $this->assertHasInternalError($attachTagContactResponse, 3);
    }

    /**
     * Testa o método Attach na service TagContactService retornando erro ao
     * tentar realizar um vínculo entre uma tag e um contato, com um id de usuário
     * diferente do que está vinculados a tag e o contato
     */
    public function testReturnErrorWhenAttachTagContactOfOtherUser()
    {
        $principalUser = factory(User::class)->create();
        $secondaryUser = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $principalUser->id
        ]);

        $contact = factory(Contact::class)->create([
            'user_id' => $principalUser->id
        ]);

        $attachTagContactResponse = $this->tagContactService->attach(
            $tag->id,
            $contact->id,
            $secondaryUser->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $attachTagContactResponse);
        $this->assertNotTrue($attachTagContactResponse->success);
        $this->assertNull($attachTagContactResponse->data);
        $this->assertHasInternalError($attachTagContactResponse, 11);
    }

    /**
     * Testa o método Detach na service TagContactService retornando sucesso ao
     * desvincular uma tag e um contato do usuário
     */
    public function testReturnSuccessWhenDetachTagContact()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        factory(TagContact::class)->create([
            'tag_id'     => $tag->id,
            'contact_id' => $contact->id
        ]);

        $detachTagContactResponse = $this->tagContactService->detach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $detachTagContactResponse);
        $this->assertTrue($detachTagContactResponse->success);
        $this->assertNull($detachTagContactResponse->data);
        $this->assertEmpty($tag->tagContacts);
        $this->assertEmpty($contact->tagContacts);
    }

    /**
     * Testa o método Detach na service TagContactService retornando erro ao
     * tentar desvincular uma tag de um contato, com uma tag que não existe
     */
    public function testReturnErrorDetachWhenTagDoesExists()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $tag->delete();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $detachTagContactResponse = $this->tagContactService->detach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $detachTagContactResponse);
        $this->assertNotTrue($detachTagContactResponse->success);
        $this->assertIsBool($detachTagContactResponse->success);
        $this->assertNull($detachTagContactResponse->data);
        $this->assertHasInternalError($detachTagContactResponse, 11);
    }

    /**
     * Testa o método Detach na service TagContactService retornando erro ao
     * tentar desvincular uma tag de um contato, com um contato que não existe
     */
    public function testReturnErrorDetachWhenContactDoesExists()
    {
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $contact->delete();

        $detachTagContactResponse = $this->tagContactService->detach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $detachTagContactResponse);
        $this->assertFalse($detachTagContactResponse->success);
        $this->assertNull($detachTagContactResponse->data);
        $this->assertHasInternalError($detachTagContactResponse, 14);
    }

    /**
     * Testa o método Detach na service TagContactService retornando erro ao
     * tentar desvincular uma tag de um contato, com um usuário que não existe
     */
    public function testReturnErrorDetachWhenUserDoesExists()
    {
        $user = factory(User::class)->create();

        $user->delete();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $detachTagContactResponse = $this->tagContactService->detach(
            $tag->id,
            $contact->id,
            $user->id
        );

        $this->assertInstanceOf(ServiceResponse::class, $detachTagContactResponse);
        $this->assertFalse($detachTagContactResponse->success);
        $this->assertNull($detachTagContactResponse->data);
        $this->assertHasInternalError($detachTagContactResponse, 3);
    }
}
