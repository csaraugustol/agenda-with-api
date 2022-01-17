<?php

namespace Tests\Unit\Services;

use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use App\Services\Contracts\TagContactServiceInterface;
use App\Services\Responses\ServiceResponse;

class TagContact extends BaseTestCase
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
}
