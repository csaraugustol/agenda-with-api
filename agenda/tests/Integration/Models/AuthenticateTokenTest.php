<?php

namespace Tests\Integration\Models;

use App\Models\User;
use App\Models\AuthenticateToken;
use Tests\Unit\Services\BaseTestCase;

class AuthenticateTokenTest extends BaseTestCase
{
    /**
     * Verifica o relacionamento com o User
     */
    public function testRelationshipAuthenticateTokenWithUser()
    {
        $user = factory(User::class)->create();

        $authenticateToken = factory(AuthenticateToken::class)->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(User::class, $authenticateToken->user);
        $this->assertEquals($authenticateToken->user->id, $user->id);
    }
}
