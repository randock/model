<?php

namespace Randock\Model\Traits;

use Randock\Event\Definition\DomainEvent;

trait EventTrait
{
    /**
     * @var DomainEvent[]
     */
    private $storedEvents;

    /**
     * @return array
     */
    public function getStoredEvents(): array
    {
        return $this->storedEvents;
    }

    /**
     * @param DomainEvent $event
     */
    public function recordThat(DomainEvent $event)
    {
        $this->storedEvents[] = $event;
    }
}
