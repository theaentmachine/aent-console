<?php

namespace TheAentMachine\Aent\Event;

final class VoidEvent extends AbstractEvent
{
    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this->setHidden(true);
    }

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'VOID';
    }

    /**
     * @param null|string $payload
     * @return null|string
     */
    protected function executeEvent(?string $payload): ?string
    {
        // Let's do nothing.
        return null;
    }
}
