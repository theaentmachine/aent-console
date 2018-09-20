<?php

namespace TheAentMachine\Aent\Payload\CI;

final class CIAddDockerComposePayload extends AbstractCIAddPayload
{
    /** @var string */
    private $dockerComposeFilename;

    /**
     * CIAddDockerComposePayload constructor.
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
            'DOCKER_COMPOSE_FILENAME' => $this->dockerComposeFilename
        ];
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        $dockerComposeFilename = $assoc['DOCKER_COMPOSE_FILENAME'];
        return new self($dockerComposeFilename);
    }

    /**
     * @return string
     */
    public function getDockerComposeFilename(): string
    {
        return $this->dockerComposeFilename;
    }
}
