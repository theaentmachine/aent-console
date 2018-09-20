<?php

namespace TheAentMachine\Aent\Event\CI;

use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Event\Helper\EventHelper;
use TheAentMachine\Aent\Payload\CI\AbstractCIAddPayload;
use TheAentMachine\Aent\Payload\CI\CIAddDockerComposePayload;
use TheAentMachine\Aent\Payload\CI\CIAddReplyPayload;

abstract class AbstractCIAddEvent extends AbstractJsonEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'ADD_CI';
    }

    /**
     * @param mixed[] $payload
     * @return array<string,string>|null
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        EventHelper::registerEvents($this->getAllEventNames());
        $payload = AbstractCIAddPayload::fromArray($payload);
        $this->before($payload);
        if ($payload instanceof CIAddDockerComposePayload) {
            $response = $this->processDockerCompose($payload);
        } else {
            $response = $this->processKubernetes($payload);
        }
        $this->after($payload);
        return $response->toArray();
    }

    /**
     * @param AbstractCIAddPayload $payload
     * @return void
     */
    abstract protected function before(AbstractCIAddPayload $payload): void;

    /**
     * @param CIAddDockerComposePayload $payload
     * @return CIAddReplyPayload
     */
    abstract protected function processDockerCompose(CIAddDockerComposePayload $payload): CIAddReplyPayload;

    /**
     * @param mixed[] $payload TODO
     * @return CIAddReplyPayload
     */
    abstract protected function processKubernetes(array $payload): CIAddReplyPayload;

    /**
     * @param AbstractCIAddPayload $payload
     * @return void
     */
    abstract protected function after(AbstractCIAddPayload $payload): void;
}
