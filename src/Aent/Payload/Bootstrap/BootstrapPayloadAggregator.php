<?php

namespace TheAentMachine\Aent\Payload\Bootstrap;

use TheAentMachine\Aent\Payload\Bootstrap\Exception\BootstrapPayloadException;

final class BootstrapPayloadAggregator
{
    /** @var BootstrapPayload[] */
    private $bootstrapPayloads;

    /**
     * BootstrapPayloadAggregator constructor.
     */
    public function __construct()
    {
        $this->bootstrapPayloads = [];
    }

    /**
     * @param BootstrapPayload $payload
     * @return void
     * @throws BootstrapPayloadException
     */
    public function addBootstrapPayload(BootstrapPayload $payload): void
    {
        $name = $payload->getContext()->getName();
        if ($this->doesEnvironmentNameExist($name)) {
            throw BootstrapPayloadException::environmentNameDoesAlreadyExist($name);
        }
        $baseVirtualHost = $payload->getContext()->getBaseVirtualHost();
        if ($this->doesBaseVirtualHostExist($baseVirtualHost)) {
            throw BootstrapPayloadException::baseVirtualHostDoesAlreadyExist($baseVirtualHost);
        }
        $this->bootstrapPayloads[] = $payload;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function doesEnvironmentNameExist(string $name): bool
    {
        /** @var BootstrapPayload $bootstrapPayload */
        foreach ($this->bootstrapPayloads as $bootstrapPayload) {
            if ($bootstrapPayload->getContext()->getName() === $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $baseVirtualHost
     * @return bool
     */
    public function doesBaseVirtualHostExist(string $baseVirtualHost): bool
    {
        /** @var BootstrapPayload $bootstrapPayload */
        foreach ($this->bootstrapPayloads as $bootstrapPayload) {
            if ($bootstrapPayload->getContext()->getBaseVirtualHost() === $baseVirtualHost) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return BootstrapPayload[]
     */
    public function getBootstrapPayloads(): array
    {
        return $this->bootstrapPayloads;
    }
}
