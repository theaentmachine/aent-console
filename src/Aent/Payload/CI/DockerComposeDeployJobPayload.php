<?php

namespace TheAentMachine\Aent\Payload\CI;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class DockerComposeDeployJobPayload implements JsonPayloadInterface
{
    /** @var string */
    private $dockerComposeFilename;

    /**
     * DockerComposeDeployJobPayload constructor.
     * @param string $dockerComposeFilename
     */
    public function __construct(string $dockerComposeFilename)
    {
        $this->dockerComposeFilename = $dockerComposeFilename;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
          'DOCKER_COMPOSE_FILENAME' => $this->dockerComposeFilename,
        ];
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        return new self($assoc['DOCKER_COMPOSE_FILENAME']);
    }

    /**
     * @return string
     */
    public function getDockerComposeFilename(): string
    {
        return $this->dockerComposeFilename;
    }
}
