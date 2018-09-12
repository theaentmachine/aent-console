<?php

namespace TheAentMachine\Aent\Payload\Bootstrap;

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
     */
    public function addBootstrapPayload(string $orchestratorAent, BootstrapPayload $payload): void
    {
        $this->bootstrapPayloads[$orchestratorAent] = $payload;
    }

    /**
     * @return array<string,BootstrapPayload>
     */
    public function getBootstrapPayloads(): array
    {
        return $this->bootstrapPayloads;
    }
}
