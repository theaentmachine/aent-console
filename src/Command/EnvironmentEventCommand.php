<?php


namespace TheAentMachine\Command;

use TheAentMachine\Aenthill\Manifest;
use TheAentMachine\Aenthill\Metadata;
use TheAentMachine\Exception\ManifestException;

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
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        return [
            Metadata::ENV_NAME_KEY => Manifest::getMetadata(Metadata::ENV_NAME_KEY),
            Metadata::ENV_TYPE_KEY => Manifest::getMetadata(Metadata::ENV_TYPE_KEY)
        ];
    }
}
