<?php

namespace TheAentMachine\Service\Enum;

class VolumeTypeEnum
{
    public const NAMED_VOLUME = 'volume';
    public const BIND_VOLUME = 'bind';
    public const TMPFS_VOLUME = 'tmpfs';

    /**
     * @return string[]
     */
    public static function getVolumeTypes(): array
    {
        return array(self::NAMED_VOLUME, self::BIND_VOLUME, self::TMPFS_VOLUME);
    }
}
