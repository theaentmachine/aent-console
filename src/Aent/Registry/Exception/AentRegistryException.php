<?php

namespace TheAentMachine\Aent\Registry\Exception;

use TheAentMachine\Aent\Exception\AentException;

final class AentRegistryException extends AentException
{
    /**
     * @param string $key
     * @return AentRegistryException
     */
    public static function aentNotFound(string $key): self
    {
        return new self("Aent not found for key $key!");
    }
}
