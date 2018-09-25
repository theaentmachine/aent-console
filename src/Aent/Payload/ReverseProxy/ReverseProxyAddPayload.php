<?php

namespace TheAentMachine\Aent\Payload\ReverseProxy;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class ReverseProxyAddPayload implements JsonPayloadInterface
{
    /** @var string */
    private $baseVirtualHost;

    /**
     * ReverseProxyAddPayload constructor.
     * @param string $baseVirtualHost
     */
    public function __construct(string $baseVirtualHost)
    {
        $this->baseVirtualHost = $baseVirtualHost;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'BASE_VIRTUAL_HOST' => $this->baseVirtualHost,
        ];
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        return new self($assoc['BASE_VIRTUAL_HOST']);
    }

    /**
     * @return string
     */
    public function getBaseVirtualHost(): string
    {
        return $this->baseVirtualHost;
    }
}
