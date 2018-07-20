<?php

namespace TheAentMachine\Exception;

class ManifestException extends AenthillException
{
    public static function missingMetadata(string $key): self
    {
        return new self("Missing metadata for key '$key'");
    }

    public static function missingDependency(string $key): self
    {
        return new self("Missing dependency for key '$key'");
    }
}
