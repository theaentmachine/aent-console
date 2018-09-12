<?php

namespace TheAentMachine\Aent\Event\Bootstrap;

use TheAentMachine\Aent\Payload\Bootstrap\BootstrapPayload;
use TheAentMachine\Aent\Payload\Bootstrap\BootstrapPayloadAggregator;
use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Aent\Event\AbstractEvent;

abstract class AbstractBootstrapAddEvent extends AbstractEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'ADD';
    }

    /**
     * @param null|string $payload
     * @return null|string
     */
    protected function executeEvent(?string $payload): ?string
    {
        $aggregator = $this->process();
        /** @var BootstrapPayload $payload */
        foreach ($aggregator->getBootstrapPayloads() as $orchestratorAent => $payload) {
            $key = \uniqid();
            Aenthill::register($orchestratorAent, \uniqid());
            Aenthill::runJson($key, 'ADD', $payload->toArray());
        }
        return null;
    }

    /**
     * @return BootstrapPayloadAggregator
     */
    abstract protected function process(): BootstrapPayloadAggregator;
}
