<?php

namespace TheAentMachine\Aent\Event\ReverseProxy;

use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Event\Helper\EventHelper;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

abstract class AbstractReverseProxyAddEvent extends AbstractJsonEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'ADD_REVERSE_PROXY';
    }

    /**
     * @param array $payload
     * @return array|null
     * @throws ServiceException
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        EventHelper::registerEvents($this->getAllEventNames());
        $this->before();
        $service = $this->process();
        $this->after();
        return $service->jsonSerialize();
    }

    /**
     * @return void
     */
    abstract protected function before(): void;

    /**
     * @return Service
     */
    abstract protected function process(): Service;

    /**
     * @return void
     */
    abstract protected function after(): void;
}
