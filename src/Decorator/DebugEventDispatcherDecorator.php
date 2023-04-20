<?php

namespace App\Decorator;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DebugEventDispatcherDecorator implements EventDispatcherInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }
}
