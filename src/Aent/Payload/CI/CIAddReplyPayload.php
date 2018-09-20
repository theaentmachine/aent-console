<?php

namespace TheAentMachine\Aent\Payload\CI;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class CIAddReplyPayload implements JsonPayloadInterface
{
    /** @var array<string,string> */
    private $toRename;

    /**
     * CIAddReplyPayload constructor.
     */
    public function __construct()
    {
        $this->toRename = [];
    }

    /**
     * @param string $currentFilename
     * @param string $newFilename
     */
    public function addToRename(string $currentFilename, string $newFilename): void
    {
        $this->toRename[$currentFilename] = $newFilename;
    }

    /**
     * @return bool
     */
    public function hasFilesToRename(): bool
    {
        return !empty($this->toRename);
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return $this->toRename;
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        $self = new self();
        $self->toRename = $assoc;
        return $self;
    }

    /**
     * @return array<string,string>
     */
    public function getToRename(): array
    {
        return $this->toRename;
    }
}
