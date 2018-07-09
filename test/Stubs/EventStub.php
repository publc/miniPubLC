<?php

namespace Test\Stubs;

use App\Core\Events\Event;

class EventStub extends Event
{
    public function getName()
    {
        return 'UserSignedUp';
    }
}
