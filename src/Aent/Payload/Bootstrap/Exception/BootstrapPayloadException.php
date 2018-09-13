<?php

namespace TheAentMachine\Aent\Payload\Bootstrap\Exception;

use TheAentMachine\Aent\Exception\AentException;

final class BootstrapPayloadException extends AentException
{
    /**
     * @param string $name
     * @return BootstrapPayloadException
     */
    public static function environmentNameDoesAlreadyExist(string $name): self
    {
        return new self("Environment \"$name\" does already exist!");
    }

    /**
     * @param string $baseVirtualHost
     * @return BootstrapPayloadException
     */
    public static function baseVirtualHostDoesAlreadyExist(string $baseVirtualHost): self
    {
        return new self("Base virtual host \"$baseVirtualHost\" does already exist!");
    }
}
