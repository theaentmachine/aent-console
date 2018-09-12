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
        $this->output->writeln($this->welcomeMessage());
        $aggregator = $this->process();
        /** @var BootstrapPayload $payload */
        foreach ($aggregator->getBootstrapPayloads() as $orchestratorAent => $payload) {
            $key = \uniqid();
            Aenthill::register($orchestratorAent, \uniqid());
            Aenthill::runJson($key, 'ADD', $payload->toArray());
        }
        $this->output->writeln($this->goodByeMessage());
        return null;
    }

    /**
     * @return string
     */
    abstract protected function welcomeMessage(): string;

    /**
     * @return BootstrapPayloadAggregator
     */
    abstract protected function process(): BootstrapPayloadAggregator;

    /**
     * @return string
     */
    abstract protected function goodByeMessage(): string;
}
