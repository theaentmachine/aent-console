<?php

namespace TheAentMachine\Service\Volume;

use TheAentMachine\Service\Enum\VolumeTypeEnum;

class NamedVolume extends Volume
{
    /** @var string */
    private $target;
    /** @var bool */
    private $readOnly;
    /** @var null|string */
    private $requestStorage;

    public function __construct(string $source, string $target, bool $readOnly = false, ?string $comment = null, ?string $requestStorage = null)
    {
        parent::__construct($source, $comment);
        $this->target = $target;
        $this->readOnly = $readOnly;
        $this->requestStorage = $requestStorage;
    }

    public function getType(): string
    {
        return VolumeTypeEnum::NAMED_VOLUME;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    public function getRequestStorage(): ?string
    {
        return $this->requestStorage;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'type' => $this->getType(),
            'source' => $this->source,
            'target' => $this->target,
            'readOnly' => $this->readOnly,
            'comment' => $this->comment,
            'requestStorage' => $this->requestStorage,
        ], function ($v) {
            return null !== $v;
        });
    }
}
