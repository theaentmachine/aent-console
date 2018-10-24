<?php

namespace TheAentMachine\Aent\Payload\Builder;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class NewImageReplyPayload implements JsonPayloadInterface
{
    /** @var string */
    private $dockerfileName;

    /**
     * NewImageReplyPayload constructor.
     * @param string $dockerfileName
     */
    public function __construct(string $dockerfileName)
    {
        $this->dockerfileName = $dockerfileName;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'DOCKERFILE_NAME' => $this->dockerfileName,
        ];
    }

    /**
     * @param array $assoc
     * @return NewImageReplyPayload
     */
    public static function fromArray(array $assoc): self
    {
        return new self($assoc['DOCKERFILE_NAME']);
    }

    /**
     * @return string
     */
    public function getDockerfileName(): string
    {
        return $this->dockerfileName;
    }
}
