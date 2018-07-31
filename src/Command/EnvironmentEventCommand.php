<?php


namespace TheAentMachine\Command;

use TheAentMachine\Aenthill\CommonEvents;
use TheAentMachine\Aenthill\Manifest;
use TheAentMachine\Aenthill\CommonMetadata;

final class EnvironmentEventCommand extends AbstractJsonEventCommand
{
    protected function getEventName(): string
    {
        return CommonEvents::ENVIRONMENT_EVENT;
    }

    /**
     * @param mixed[] $payload
     * @return array|null
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        return [
            CommonMetadata::ENV_NAME_KEY => Manifest::getMetadata(CommonMetadata::ENV_NAME_KEY),
            CommonMetadata::ENV_TYPE_KEY => Manifest::getMetadata(CommonMetadata::ENV_TYPE_KEY)
        ];
    }
}
