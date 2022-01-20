<?php

namespace Tests\Integration\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\Phone;
use App\Models\Address;
use App\Models\Contact;
use App\Models\TagContact;
use Tests\Unit\Services\BaseTestCase;
use Illuminate\Database\Eloquent\Collection;

class ContactTest extends BaseTestCase
{
    /**
     * Verifica o relacionamento com o User
     */
    public function testRelationshipContactWithUser()
    {
        $user = factory(User::class)->create();

        $contact = factory(Contact::class)->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(User::class, $contact->user);
        $this->assertEquals($contact->user->id, $user->id);
    }

    /**
     * Verifica o relacionamento com o Address
     */
    public function testRelationshipContactWithAddress()
    {
        $contact = factory(Contact::class)->create();

        factory(Address::class)->create([
            'contact_id' => $contact->id
        ]);

        $address = $contact->adresses->first();

        $this->assertInstanceOf(Collection::class, $contact->adresses);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($address->contact_id, $contact->id);
    }

    /**
     * Verifica o relacionamento com o Phone
     */
    public function testRelationshipContactWithPhone()
    {
        $contact = factory(Contact::class)->create();

        factory(Phone::class)->create([
            'contact_id' => $contact->id
        ]);

        $phone = $contact->phones->first();

        $this->assertInstanceOf(Collection::class, $contact->phones);
        $this->assertInstanceOf(Phone::class, $phone);
        $this->assertEquals($phone->contact_id, $contact->id);
    }

    /**
     * Verifica o relacionamento com o TagContact
     */
    public function testRelationshipContactWithTagContact()
    {
        $contact = factory(Contact::class)->create();

        $tag = factory(Tag::class)->create();

        factory(TagContact::class)->create([
            'tag_id'     => $tag->id,
            'contact_id' => $contact->id
        ]);

        $tagContact = $contact->tagcontacts->first();

        $this->assertInstanceOf(Collection::class, $contact->tagcontacts);
        $this->assertInstanceOf(TagContact::class, $tagContact);
        $this->assertEquals($tagContact->contact_id, $contact->id);
        $this->assertEquals($tagContact->tag_id, $tag->id);
    }

    /**
     * Verifica o relacionamento com o Tag
     */
    public function testRelationshipContactWithTag()
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

        $tagContact = $contact->tagcontacts->first();

        $this->assertEquals($tagContact->tag_id, $tag->id);
        $this->assertEquals($tagContact->tag->id, $tag->id);
        $this->assertEquals($contact->user_id, $tag->user_id);
    }
}
