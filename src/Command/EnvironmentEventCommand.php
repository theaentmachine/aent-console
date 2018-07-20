<?php


namespace TheAentMachine\Command;

use TheAentMachine\Aenthill\Manifest;
use TheAentMachine\Aenthill\Metadata;
use TheAentMachine\Exception\ManifestException;
use TheAentMachine\Exception\MissingEnvironmentVariableException;

class EnvironmentEventCommand extends JsonEventCommand
{
    protected function getEventName(): string
    {
        return 'ENVIRONMENT';
    }

    /**
     * @param array $payload
     * @return array|null
     * @throws ManifestException
     * @throws MissingEnvironmentVariableException
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        return [
            Manifest::getMetadata(Metadata::ENV_NAME_KEY),
            Manifest::getMetadata(Metadata::ENV_TYPE_KEY)
        ];
    }
}
