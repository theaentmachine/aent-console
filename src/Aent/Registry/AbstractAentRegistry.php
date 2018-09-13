<?php

namespace TheAentMachine\Aent\Registry;

use TheAentMachine\Aent\Registry\Exception\AentRegistryException;

abstract class AbstractAentRegistry
{
    /** @var array<string,string> */
    protected static $aents;

    /**
     * @return array<string,string>
     */
    public static function getList(): array
    {
        return static::$aents;
    }

    /**
     * @param string $key
     * @return string
     * @throws AentRegistryException
     */
    public static function getImage(string $key): string
    {
        if (!isset(static::$aents[$key])) {
            throw AentRegistryException::aentNotFound($key);
        }
        return static::$aents[$key];
    }
}
