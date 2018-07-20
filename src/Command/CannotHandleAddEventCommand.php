<?php


namespace TheAentMachine\Command;

use TheAentMachine\Exception\EventException;

class CannotHandleAddEventCommand extends EventCommand
{
    protected function getEventName(): string
    {
        return 'ADD';
    }

    /**
     * @param null|string $payload
     * @return null|string
     * @throws EventException
     * @throws \TheAentMachine\Exception\MissingEnvironmentVariableException
     */
    protected function executeEvent(?string $payload): ?string
    {
        throw EventException::cannotHandleAddEvent();
    }
}
