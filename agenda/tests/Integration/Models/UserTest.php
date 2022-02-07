<?php

namespace Tests\Integration\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Contact;
use App\Models\ExternalToken;
use App\Models\ChangePassword;
use App\Models\AuthenticateToken;
use Tests\Unit\Services\BaseTestCase;
use Illuminate\Database\Eloquent\Collection;

class UserTest extends BaseTestCase
{
    /**
     * Verifica o relacionamento do User com o Contact
     */
    public function testRelationshipUserWithContact()
    {
        $user = factory(User::class)->create();

        factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $contact = $user->contacts->first();

        $this->assertInstanceOf(Collection::class, $user->contacts);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals($contact->user_id, $user->id);
    }

    /**
     * Verifica o relacionamento do User com o Tag
     */
    public function testRelationshipUserWithTag()
    {
        $user = factory(User::class)->create();

        factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $tag = $user->tags->first();

        $this->assertInstanceOf(Collection::class, $user->tags);
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals($tag->user_id, $user->id);
    }

    /**
     * Verifica o relacionamento do User com o AuthenticateToken
     */
    public function testRelationshipUserWithAuthenticateToken()
    {
        $user = factory(User::class)->create();

        factory(AuthenticateToken::class)->create([
            'user_id' => $user->id
        ]);

        $authenticateToken = $user->authenticateTokens->first();

        $this->assertInstanceOf(Collection::class, $user->authenticateTokens);
        $this->assertInstanceOf(AuthenticateToken::class, $authenticateToken);
        $this->assertEquals($authenticateToken->user_id, $user->id);
    }

     /**
     * Verifica o relacionamento do User com o ChangePassword
     */
    public function testRelationshipUserWithChangePasswords()
    {
        $user = factory(User::class)->create();

        factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $changePassword = $user->changePasswords->first();

        $this->assertInstanceOf(Collection::class, $user->changePasswords);
        $this->assertInstanceOf(ChangePassword::class, $changePassword);
        $this->assertEquals($changePassword->user_id, $user->id);
    }

    /**
     * Verifica o relacionamento do User com o ExternalToken
     */
    public function testRelationshipUserWithExternalToken()
    {
        $user = factory(User::class)->create();

        factory(ExternalToken::class)->create([
            'user_id' => $user->id
        ]);

        $externalToken = $user->externalTokens->first();

        $this->assertInstanceOf(Collection::class, $user->externalTokens);
        $this->assertInstanceOf(ExternalToken::class, $externalToken);
        $this->assertEquals($externalToken->user_id, $user->id);
    }
}
