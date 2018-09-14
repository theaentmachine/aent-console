<?php

namespace TheAentMachine\Aent\Registry\Exception;

use TheAentMachine\Aent\Exception\AentException;

final class AentRegistryException extends AentException
{
    /**
     * @param string $key
     * @return self
     */
    public static function imageNotFound(string $key): self
    {
        return new self("Image not found for key \"$key\"!");
    }

    /**
     * @param string $image
     * @return self
     */
    public static function keyNotFound(string $image): self
    {
        return new self("Key not found for image \"$image\"!");
    }
}
