<?php

namespace TheAentMachine\Aent\Registry\Exception;

use TheAentMachine\Aent\Exception\AentException;

final class ColonyRegistryException extends AentException
{
    /**
     * @param string $name
     * @return ColonyRegistryException
     */
    public static function aentNotFound(string $name): self
    {
        return new self("aent \"$name\" not found!");
    }
}
