<?php

namespace Tests\Integration\Models;

use App\Models\User;
use App\Models\ChangePassword;
use Tests\Unit\Services\BaseTestCase;

class ChangePasswordTest extends BaseTestCase
{
    /**
     * Verifica o relacionamento da ChangePassword com o User
     */
    public function testRelationshipChangePasswordWithUser()
    {
        $user = factory(User::class)->create();

        $changePassword = factory(ChangePassword::class)->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(User::class, $changePassword->user);
        $this->assertEquals($changePassword->user->id, $user->id);
    }
}
