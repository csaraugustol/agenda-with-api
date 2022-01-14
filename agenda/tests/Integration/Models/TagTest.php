<?php

namespace Tests\Integration\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\TagContact;
use Tests\Unit\Services\BaseTestCase;
use Illuminate\Database\Eloquent\Collection;

class TagTest extends BaseTestCase
{
    /**
     * Verifica o relacionamento da Tag com o User
     */
    public function testRelationshipTagWithUser()
    {
        $tag = factory(Tag::class)->create();

        $this->assertInstanceOf(User::class, $tag->user);
    }

    /**
     * Verifica o relacionamento da Tag com a TagContacts
     */
    public function testRelationshipTagWithTagContact()
    {
        $tag = factory(Tag::class)->create();

        factory(TagContact::class)->create(['tag_id' => $tag->id]);

        $this->assertInstanceOf(Collection::class, $tag->tagContacts);
        $this->assertInstanceOf(TagContact::class, $tag->tagContacts->first());
    }
}
