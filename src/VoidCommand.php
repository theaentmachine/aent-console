<?php

namespace TheAentMachine;

/**
 * A command that does nothing
 */
class VoidCommand extends EventCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setHidden(true);
    }

    protected function getEventName(): string
    {
        return 'void';
    }

    protected function executeEvent(?string $payload): ?string
    {
        // Let's do nothing.
        $this->log->debug('Event cannot be handled. Ignoring.');
        return null;
    }
}
