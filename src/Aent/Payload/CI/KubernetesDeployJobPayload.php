<?php

namespace TheAentMachine\Aent\Payload\CI;

use TheAentMachine\Aent\K8SProvider\Provider;
use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class KubernetesDeployJobPayload implements JsonPayloadInterface
{
    /** @var string */
    private $directoryName;

    /** @var Provider */
    private $provider;

    /**
     * KubernetesDeployJobPayload constructor.
     * @param string $directoryName
     * @param Provider $provider
     */
    public function __construct(string $directoryName, Provider $provider)
    {
        $this->directoryName = $directoryName;
        $this->provider = $provider;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        $assoc = $this->provider->toArray();
        $assoc['DIRECTORY_NAME'] = $this->directoryName;
        return $assoc;
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        return new self($assoc['DIRECTORY_NAME'], Provider::fromArray($assoc));
    }

    /**
     * @return string
     */
    public function getDirectoryName(): string
    {
        return $this->directoryName;
    }

    /**
     * @return Provider
     */
    public function getProvider(): Provider
    {
        return $this->provider;
    }
}
