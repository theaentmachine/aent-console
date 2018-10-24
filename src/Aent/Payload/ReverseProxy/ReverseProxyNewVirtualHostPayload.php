<?php

namespace TheAentMachine\Aent\Payload\ReverseProxy;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

final class ReverseProxyNewVirtualHostPayload implements JsonPayloadInterface
{
    /** @var string */
    private $baseVirtualHost;

    /** @var Service */
    private $service;

    /**
     * ReverseProxyNewVirtualHostPayload constructor.
     * @param string $baseVirtualHost
     * @param Service $service
     */
    public function __construct(string $baseVirtualHost, Service $service)
    {
        $this->baseVirtualHost = $baseVirtualHost;
        $this->service = $service;
    }

    /**
     * @return mixed[]
     * @throws ServiceException
     */
    public function toArray(): array
    {
        return [
            'BASE_VIRTUAL_HOST' => $this->baseVirtualHost,
            'SERVICE' => $this->service->jsonSerialize(),
        ];
    }

    /**
     * @param mixed[] $assoc
     * @return self
     * @throws ServiceException
     */
    public static function fromArray(array $assoc): self
    {
        return new self($assoc['BASE_VIRTUAL_HOST'], Service::parsePayload($assoc['SERVICE']));
    }

    /**
     * @return string
     */
    public function getBaseVirtualHost(): string
    {
        return $this->baseVirtualHost;
    }

    /**
     * @return Service
     */
    public function getService(): Service
    {
        return $this->service;
    }
}
