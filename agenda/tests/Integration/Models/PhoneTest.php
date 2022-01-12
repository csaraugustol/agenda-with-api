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
        $phone = factory(Phone::class)->create();

        $this->assertInstanceOf(Contact::class, $phone->contact);
    }
}
