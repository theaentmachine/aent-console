<?php

namespace TheAentMachine\Aent\Event\ReverseProxy;

use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

abstract class AbstractNewVirtualHostEvent extends AbstractJsonEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'NEW_VIRTUAL_HOST';
    }

    /**
     * @param array $payload
     * @return array|null
     * @throws ServiceException
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $service = Service::parsePayload($payload);
        $this->before();
        $service = $this->process($service);
        $this->after();
        return $service->jsonSerialize();
    }

    /**
     * @return void
     */
    abstract protected function before(): void;

    /**
     * @param Service $service
     * @return Service
     */
    abstract protected function process(Service $service): Service;

    /**
     * @return void
     */
    abstract protected function after(): void;
}
