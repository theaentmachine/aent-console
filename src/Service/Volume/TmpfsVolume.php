<?php

namespace TheAentMachine\Service\Volume;

use TheAentMachine\Service\Enum\VolumeTypeEnum;

class TmpfsVolume extends Volume
{
    /**
     * @return string
     */
    public static function getType(): string
    {
        return VolumeTypeEnum::TMPFS_VOLUME;
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
        );
    }
}
