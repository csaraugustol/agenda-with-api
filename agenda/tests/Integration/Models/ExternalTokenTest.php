<?php

namespace Tests\Integration\Models;

use Exception;
use App\Models\User;
use App\Models\ExternalToken;
use Tests\Unit\Services\BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

class ExternalTokenTest extends BaseTestCase
{
    use WithFaker;

    /**
     * Verifica o relacionamento com o User
     */
    public function testRelationshipExternalTokenWithUser()
    {
        $user = factory(User::class)->create();

        $externalToken = factory(ExternalToken::class)->create([
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(User::class, $externalToken->user);
        $this->assertEquals($externalToken->user->id, $user->id);
    }

    /**
     *Verifica se o tipo do sistema a ser integrado é igual 'VEXPENSES'
     */
    public function testMethodSetTypeAttributeWhensystemIsVExpenses()
    {
        $user = factory(User::class)->create();

        $externalToken = factory(ExternalToken::class)->create([
            'user_id' => $user->id
        ]);

        $type = 'VEXPENSES';

        $methodResponse = $externalToken->setTypeAttribute($type);

        $this->assertNull($methodResponse);
        $this->assertInstanceOf(User::class, $externalToken->user);
        $this->assertEquals($externalToken->system, $type);
    }

    /**
     * Verifica se o tipo do sistema a ser integrado é diferente de 'VEXPENSES'
     */
    public function testMethodSetTypeAttributeWhensystemDoesntIsVExpenses()
    {
        $user = factory(User::class)->create();

        $externalToken = factory(ExternalToken::class)->create([
            'user_id' => $user->id,
        ]);

        $methodResponse = $externalToken->setTypeAttribute($this->faker->word);

        $this->assertInstanceOf(Exception::class, $methodResponse);
        $this->assertInstanceOf(User::class, $externalToken->user);
    }
}
