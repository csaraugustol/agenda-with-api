<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\Authentication;
use Tests\Traits\MockClassMethod;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BaseTestCase extends TestCase
{
    use WithFaker;
    use Authentication;
    use MockClassMethod;
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->withHeaders([
            'Accept'        => 'application/json',
            'content-type'  => 'application/json',
        ]);

        $this->resetMock();
    }
}
