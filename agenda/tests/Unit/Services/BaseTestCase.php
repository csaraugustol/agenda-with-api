<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Tests\Traits\MockClassMethod;
use Prophecy\PhpUnit\ProphecyTrait;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BaseTestCase extends TestCase
{
    use WithFaker;
    use ProphecyTrait;
    use MockClassMethod;
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->resetMock();
    }
}
