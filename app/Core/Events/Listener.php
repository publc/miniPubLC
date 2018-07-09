<?php

namespace App\Core\Events;

abstract class Listener
{
    abstract public function handle(Event $event);
}
