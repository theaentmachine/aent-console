<?php

namespace TheAentMachine\Service\Volume;

use TheAentMachine\Service\Enum\VolumeTypeEnum;

class BindVolume extends Volume
{
    /** @var string */
    private $target;
    /** @var bool */
    protected $readOnly;

    /**
     * BindVolume constructor.
     * @param string $source
     * @param bool $readOnly
     * @param string $target
     */
    public function __construct(string $source, string $target, bool $readOnly = false)
    {
        parent::__construct($source);
        $this->target = $target;
        $this->readOnly = $readOnly;
    }

    /**
     * @return string
     */
    public static function getType(): string
    {
        return VolumeTypeEnum::BIND_VOLUME;
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
            'type' => self::getType(),
            'source' => $this->source,
            'target' => $this->target,
            'readOnly' => $this->readOnly,
        );
    }
}
