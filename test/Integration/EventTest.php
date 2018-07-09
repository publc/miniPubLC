<?php

namespace Test\Integration;

use Test\Stubs\EventStub;
use Test\Stubs\ListenerStub;
use App\Core\Events\Dispatcher;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    /** @test */
    public function itCanDispatchAnEvent()
    {
        $dispatcher = new Dispatcher;

        $event = new EventStub;
        $mockerListener = $this->createMock(ListenerStub::class);

        $mockerListener->expects($this->once())->method('handle')->with($event);

        $dispatcher->addListener('UserSignedUp', $mockerListener);
        $dispatcher->dispatch($event);
    }

    /** @test */
    public function itCanDispatchEventsWithMultipleListeners()
    {
        $dispatcher = new Dispatcher;

        $event = new EventStub;
        $mockerListener = $this->createMock(ListenerStub::class);
        $anotherMockerListener = $this->createMock(ListenerStub::class);

        $mockerListener->expects($this->once())->method('handle')->with($event);
        $anotherMockerListener->expects($this->once())->method('handle')->with($event);

        $dispatcher->addListener('UserSignedUp', $mockerListener);
        $dispatcher->addListener('UserSignedUp', $anotherMockerListener);
        $dispatcher->dispatch($event);
    }
}
