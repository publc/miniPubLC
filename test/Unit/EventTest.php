<?php

namespace Test\Unit;

use Test\Stubs\EventStub;
use PHPUnit\Framework\TestCase;
use Test\Stubs\EventStubNoName;

class EventTest extends TestCase
{
    /** @test */
    public function cantGetEventName()
    {
        $event = new EventStub;

        $this->assertEquals('UserSignedUp', $event->getName());
    }

    /** @test */
    public function defaultEventNameOfClassName()
    {
        $event = new EventStubNoName;

        $this->assertEquals('EventStubNoName', $event->getName());
    }
}
