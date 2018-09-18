<?php

namespace TheAentMachine\Aent\Event\Orchestrator;

use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Event\Helper\EventHelper;
use TheAentMachine\Aent\Payload\Bootstrap\BootstrapPayload;

abstract class AbstractOrchestratorAddEvent extends AbstractJsonEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'ADD_ORCHESTRATOR';
    }

    /**
     * @param array $payload
     * @return array|null
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        EventHelper::registerEvents($this->getAllEventNames());
        $this->before();
        $payload = BootstrapPayload::fromArray($payload);
        $this->process($payload);
        $this->after();
        return null;
    }

    /**
     * @return void
     */
    abstract protected function before(): void;

    /**
     * @param BootstrapPayload $payload
     * @return void
     */
    abstract protected function process(BootstrapPayload $payload): void;

    /**
     * @return void
     */
    abstract protected function after(): void;
}
