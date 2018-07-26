<?php


namespace TheAentMachine\Command;

use TheAentMachine\Aenthill\CommonEvents;
use TheAentMachine\Exception\EventException;
use TheAentMachine\Exception\MissingEnvironmentVariableException;

final class CannotHandleAddEventCommand extends AbstractEventCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setHidden(true);
    }

    protected function getEventName(): string
    {
        return CommonEvents::ADD_EVENT;
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
