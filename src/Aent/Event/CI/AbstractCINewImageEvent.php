<?php

namespace TheAentMachine\Aent\Event\CI;

use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Payload\CI\CINewImagePayload;
use TheAentMachine\Aent\Payload\CI\CINewImageReplyPayload;

abstract class AbstractCINewImageEvent extends AbstractJsonEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'NEW_IMAGE';
    }

    /**
     * @param mixed[] $payload
     * @return array<string,string>|null
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $payload = CINewImagePayload::fromArray($payload);
        $this->before($payload);
        $response = $this->process($payload);
        $this->after($payload, $response);
        return $response->toArray();
    }

    /**
     * @param CINewImagePayload $payload
     * @return void
     */
    abstract protected function before(CINewImagePayload $payload): void;

    /**
     * @param CINewImagePayload $payload
     * @return CINewImageReplyPayload
     */
    abstract protected function process(CINewImagePayload $payload): CINewImageReplyPayload;

    /**
     * @param CINewImagePayload $payload
     * @param CINewImageReplyPayload $response
     * @return void
     */
    abstract protected function after(CINewImagePayload $payload, CINewImageReplyPayload $response): void;
}
