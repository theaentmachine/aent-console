<?php

namespace TheAentMachine\Aent\Event\Builder;

use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Payload\Builder\NewImageReplyPayload;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

abstract class AbstractNewImageEvent extends AbstractJsonEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'NEW_IMAGE';
    }

    /**
     * @param array $payload
     * @return array|null
     * @throws ServiceException
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $service = Service::parsePayload($payload);
        $this->before($service);
        $payload = $this->process($service);
        $this->after($service, $payload);
        return $payload->toArray();
    }

    /**
     * @param Service $service
     * @return void
     */
    abstract protected function before(Service $service): void;

    /**
     * @param Service $service
     * @return NewImageReplyPayload
     */
    abstract protected function process(Service $service): NewImageReplyPayload;

    /**
     * @param Service $service
     * @param NewImageReplyPayload $payload
     * @return void
     */
    abstract protected function after(Service $service, NewImageReplyPayload $payload): void;
}
