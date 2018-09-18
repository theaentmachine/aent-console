<?php

namespace TheAentMachine\Aent\Event\Builder;

use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Payload\Builder\NewImageReplyPayload;

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
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $this->before();
        $payload = $this->process();
        $this->after();
        return $payload->toArray();
    }

    /**
     * @return void
     */
    abstract protected function before(): void;

    /**
     * @return NewImageReplyPayload
     */
    abstract protected function process(): NewImageReplyPayload;

    /**
     * @return void
     */
    abstract protected function after(): void;
}
