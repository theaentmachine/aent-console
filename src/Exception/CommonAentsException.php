<?php


namespace TheAentMachine\Exception;

final class CommonAentsException extends AenthillException
{
    public static function noAentsAvailable(string $key): self
    {
        return new self("No common aents found for key \"$key\"");
    }
}
