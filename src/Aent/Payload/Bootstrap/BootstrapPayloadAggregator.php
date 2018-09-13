<?php

namespace TheAentMachine\Aent\Payload\Bootstrap;

use TheAentMachine\Aent\Payload\Bootstrap\Exception\BootstrapPayloadException;

final class BootstrapPayloadAggregator
{
    /** @var array<string,BootstrapPayload> */
    private $bootstrapPayloads;

    /**
     * BootstrapPayloadAggregator constructor.
     */
    public function __construct()
    {
        $this->bootstrapPayloads = [];
    }

    /**
     * @param string $orchestratorAent
     * @param BootstrapPayload $payload
     * @throws BootstrapPayloadException
     */
    public function addBootstrapPayload(string $orchestratorAent, BootstrapPayload $payload): void
    {
        $name = $payload->getContext()->getName();
        if ($this->doesEnvironmentNameExist($name)) {
            throw BootstrapPayloadException::environmentNameDoesAlreadyExist($name);
        }
        $this->bootstrapPayloads[$orchestratorAent] = $payload;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function doesEnvironmentNameExist(string $name): bool
    {
        /** @var BootstrapPayload $bootstrapPayload */
        foreach ($this->bootstrapPayloads as $k => $bootstrapPayload) {
            if ($bootstrapPayload->getContext()->getName() === $name) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array<string,BootstrapPayload>
     */
    public function getBootstrapPayloads(): array
    {
        return $this->bootstrapPayloads;
    }
}
