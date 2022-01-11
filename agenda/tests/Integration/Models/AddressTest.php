<?php

namespace Tests\Integration\Models;

use Tests\TestCase;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddressTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Verifica o relacionamento com o Contato
     */
    public function testRelationshipWithContact()
    {
        $address = factory(Address::class)->create();

        $this->assertInstanceOf(Contact::class, $address->contact);
    }
}
