<?php

namespace Test\Stubs;

use App\Core\Events\Event;
use App\Core\Events\Listener;

class ListenerStub extends Listener
{
    public function handle(Event $event)
    {
        //
    }
}
