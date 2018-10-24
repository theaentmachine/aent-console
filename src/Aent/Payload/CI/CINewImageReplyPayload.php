<?php

namespace TheAentMachine\Aent\Payload\CI;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class CINewImageReplyPayload implements JsonPayloadInterface
{
    /** @var string */
    private $imageName;

    /**
     * CINewImageReplyPayload constructor.
     * @param string $imageName
     */
    public function __construct(string $imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'IMAGE_NAME' => $this->imageName,
        ];
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        $imageName = $assoc['IMAGE_NAME'];
        return new self($imageName);
    }

    /**
     * @return string
     */
    public function getImageName(): string
    {
        return $this->imageName;
    }
}
