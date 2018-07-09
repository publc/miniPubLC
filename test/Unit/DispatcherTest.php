<?php

namespace Test\Unit;

use Test\Stubs\ListenerStub;
use App\Core\Events\Dispatcher;
use PHPUnit\Framework\TestCase;

class DispatcherTest extends TestCase
{
    /** @test */
    public function itHoldsListenersInAnArray()
    {
        $dispatcher = new Dispatcher;

        $this->assertEmpty($dispatcher->getListeners());
        $this->assertInternalType('array', $dispatcher->getListeners());
    }

     /** @test */
    public function itCanAddListener()
    {
        $dispatcher = new Dispatcher;
        $dispatcher->addListener('UserSignedUp', new ListenerStub());

        $this->assertCount(1, $dispatcher->getListeners()["UserSignedUp"]);
    }

    /** @test */
    public function itCanGetListenersByEventName()
    {
        $dispatcher = new Dispatcher;
        $dispatcher->addListener('UserSignedUp', new ListenerStub());

        $this->assertCount(1, $dispatcher->getListenersByEvent("UserSignedUp"));
    }

    /** @test */
    public function itReturnsEmptyArrayIfNotListenersSet()
    {
        $dispatcher = new Dispatcher;

        $this->assertCount(0, $dispatcher->getListenersByEvent("UserSignedUp"));
    }

    /** @test */
    public function itCanCheckIfHasListenerRegistered()
    {
        $dispatcher = new Dispatcher;

        $this->assertFalse($dispatcher->hasListener("UserSignedUp"));
    }
}
