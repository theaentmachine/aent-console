<?php

namespace TheAentMachine\Aent\Payload\CI;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class CINewImagePayload implements JsonPayloadInterface
{
    /** @var string */
    private $serviceName;

    /** @var string */
    private $dockerfileName;

    /**
     * CINewImagePayload constructor.
     * @param string $serviceName
     * @param string $dockerfileName
     */
    public function __construct(string $serviceName, string $dockerfileName)
    {
        $this->serviceName = $serviceName;
        $this->dockerfileName = $dockerfileName;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'SERVICE_NAME' => $this->serviceName,
            'DOCKERFILE_NAME' => $this->dockerfileName,
        ];
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        $serviceName = $assoc['SERVICE_NAME'];
        $dockerfileName = $assoc['DOCKERFILE_NAME'];
        return new self($serviceName, $dockerfileName);
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * @return string
     */
    public function getDockerfileName(): string
    {
        return $this->dockerfileName;
    }
}
