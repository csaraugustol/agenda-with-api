<?php

namespace Tests\Integration\Models;

use App\Models\Tag;
use App\Models\Contact;
use App\Models\TagContact;
use Tests\Unit\Services\BaseTestCase;

class TagContactTest extends BaseTestCase
{
    /**
     * Verifica o relacionamento com a Tag
     */
    public function testRelationshipWithTag()
    {
        $tag = factory(Tag::class)->create();

        $tagContact = factory(TagContact::class)->create([
            'tag_id' => $tag->id
        ]);

        $this->assertInstanceOf(Tag::class, $tagContact->tag);
        $this->assertEquals($tagContact->tag->id, $tag->id);
    }

    /**
     * Verifica o relacionamento com o Contato
     */
    public function testRelationshipWithContact()
    {
        $contact = factory(Contact::class)->create();

        $tagContact = factory(TagContact::class)->create([
            'contact_id' => $contact->id
        ]);

        $this->assertInstanceOf(Contact::class, $tagContact->contact);
        $this->assertEquals($tagContact->contact->id, $contact->id);
    }
}
