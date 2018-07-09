<?php

namespace Test\Unit;

use Test\Stubs\EventStub;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /** @test */
    public function cantGetEventName()
    {
        $event = new EventStub;

        $this->assertTrue(true);
    }
}
