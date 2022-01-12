<?php

namespace Tests\Integration\Models;

use App\Models\Address;
use App\Models\Contact;
use Tests\Unit\Services\BaseTestCase;

class AddressTest extends BaseTestCase
{
    /**
     * Verifica o relacionamento com o Contato
     */
    public function testRelationshipWithContact()
    {
        $address = factory(Address::class)->create();

        $this->assertInstanceOf(Contact::class, $address->contact);
    }
}
