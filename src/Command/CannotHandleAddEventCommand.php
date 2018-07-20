<?php


namespace TheAentMachine\Command;

use TheAentMachine\Exception\EventException;
use TheAentMachine\Exception\MissingEnvironmentVariableException;

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
     * @throws MissingEnvironmentVariableException
     */
    protected function executeEvent(?string $payload): ?string
    {
        throw EventException::cannotHandleAddEvent();
    }
}
