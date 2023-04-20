<?php

namespace App\Decorator;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DebugEventDispatcherDecorator implements EventDispatcherInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function dispatch(object $event, string $eventName = null): object
    {
        return $this->eventDispatcher->dispatch($event, $eventName);
    }

    public function addListener(string $eventName, $listener, int $priority = 0)
    {
        $this->eventDispatcher->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->eventDispatcher->addSubscriber($subscriber);
    }

    public function removeListener(string $eventName, $listener)
    {
        $this->eventDispatcher->removeListener($eventName, $listener);
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->eventDispatcher->removeSubscriber($subscriber);
    }

    public function getListeners(string $eventName = null): array
    {
        return $this->eventDispatcher->getListeners($eventName);
    }

    public function getListenerPriority(string $eventName, $listener): ?int
    {
        return $this->eventDispatcher->getListenerPriority($eventName, $listener);
    }

    public function hasListeners(string $eventName = null): bool
    {
        return $this->eventDispatcher->hasListeners($eventName);
    }
}
