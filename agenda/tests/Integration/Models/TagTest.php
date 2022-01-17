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
        $user = factory(User::class)->create();

        $tag = factory(Tag::class)->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(User::class, $tag->user);
        $this->assertEquals($tag->user->id, $user->id);
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
