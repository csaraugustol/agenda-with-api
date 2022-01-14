<?php

namespace Tests\Integration\Models;

use App\Models\Phone;
use App\Models\Contact;
use Tests\Unit\Services\BaseTestCase;

class PhoneTest extends BaseTestCase
{
    /**
     * Verifica o relacionamento do Telefone com o Contato
     */
    public function testRelationshipPhoneWithContact()
    {
        $contact = factory(Contact::class)->create();

        $phone = factory(Phone::class)->create([
            'contact_id' => $contact->id,
        ]);

        $this->assertInstanceOf(Contact::class, $phone->contact);
        $this->assertEquals($phone->contact->id, $contact->id);
    }
}
