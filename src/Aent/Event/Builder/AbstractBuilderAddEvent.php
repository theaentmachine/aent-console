<?php

namespace TheAentMachine\Aent\Event\Builder;

use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Event\Helper\EventHelper;

abstract class AbstractBuilderAddEvent extends AbstractJsonEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'ADD_BUILDER';
    }

    /**
     * @param array $payload
     * @return array|null
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        EventHelper::registerEvents($this->getAllEventNames());
        $this->before();
        $this->process();
        $this->after();
        return null;
    }

    /**
     * @return void
     */
    abstract protected function before(): void;

    /**
     * @return void
     */
    abstract protected function process(): void;

    /**
     * @return void
     */
    abstract protected function after(): void;
}
