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
            throw AentRegistryException::imageNotFound($key);
        }
        return static::$aents[$key];
    }

    /**
     * @param string $image
     * @return string
     * @throws AentRegistryException
     */
    public static function getKey(string $image): string
    {
        foreach (static::$aents as $key => $aent) {
            if ($image === $aent) {
                return $key;
            }
        }
        throw AentRegistryException::keyNotFound($image);
    }
}
