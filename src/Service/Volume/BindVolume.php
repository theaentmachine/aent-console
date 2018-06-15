<?php

namespace TheAentMachine\Service\Volume;

use TheAentMachine\Service\Enum\VolumeTypeEnum;

class BindVolume extends Volume
{
    /** @var string */
    private $target;
    /** @var bool|null */
    protected $readOnly;

    /**
     * BindVolume constructor.
     * @param string $source
     * @param bool|null $readOnly
     * @param string $target
     */
    public function __construct(string $source, string $target, ?bool $readOnly)
    {
        parent::__construct(VolumeTypeEnum::BIND_VOLUME, $source);
        $this->target = $target;
        $this->readOnly = $readOnly;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return bool|null
     */
    public function isReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    /**
     * @param bool|null $readOnly
     */
    public function setReadOnly(?bool $readOnly): void
    {
        $this->readOnly = $readOnly;
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
        return array(
            'type' => $this->type,
            'source' => $this->source,
            'target' => $this->target,
            'readOnly' => $this->readOnly,
        );
    }
}
