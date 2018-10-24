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
     * @return bool
     */
    protected function shouldRegisterEvents(): bool
    {
        return false;
    }

    /**
     * @return void
     */
    protected function beforeExecute(): void
    {
        // Let's do nothing.
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

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        // Let's do nothing.
    }
}
