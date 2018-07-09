<?php

namespace Test\Unit;

use TypeError;
use Test\Stubs\EventStub;
use Test\Stubs\ListenerStub;
use PHPUnit\Framework\TestCase;

class ListenerTest extends TestCase
{
    /** @test */
    public function handleMethodThrowErrorIfInvalidEventGiven()
    {
        $this->expectException(TypeError::class);
        $listener = new ListenerStub;
        $listener->handle('not an event');
    }

    /** @test */
    public function handleMethodAcceptsAnEvent()
    {
        $listener = new ListenerStub;
        $listener->handle(new EventStub);

        $this->addToAssertionCount(1);
    }
}
