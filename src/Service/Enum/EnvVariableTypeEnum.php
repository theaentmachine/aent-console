<?php

namespace TheAentMachine\Service\Enum;

class EnvVariableTypeEnum
{
    public const SHARED_ENV_VARIABLE = 'sharedEnvVariable';
    public const SHARED_SECRET = 'sharedSecret';
    public const IMAGE_ENV_VARIABLE = 'imageEnvVariable';
    public const CONTAINER_ENV_VARIABLE = 'containerEnvVariable';

    /**
     * @return string[]
     */
    public static function getEnvVariableTypes(): array
    {
        return array(self::SHARED_ENV_VARIABLE, self::SHARED_SECRET, self::IMAGE_ENV_VARIABLE, self::CONTAINER_ENV_VARIABLE);
    }
}
