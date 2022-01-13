<?php

namespace Tests\Integration\Models;

use App\Models\Tag;
use App\Models\User;
use Tests\Unit\Services\BaseTestCase;

class TagTest extends BaseTestCase
{
    /**
     * Verifica o relacionamento com a Tag
     */
    public function testRelationshipTagWithUser()
    {
        $tag = factory(Tag::class)->create();

        $this->assertInstanceOf(User::class, $tag->user);
    }
}
