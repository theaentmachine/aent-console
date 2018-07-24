<?php

namespace TheAentMachine\Exception;

class ManifestException extends AenthillException
{
    public static function missingMetadata(string $key, \Exception $e): self
    {
        return new self("Missing metadata for key '$key'", 500, $e);
    }

    public static function missingDependency(string $key, \Exception $e): self
    {
        return new self("Missing dependency for key '$key'", 501, $e);
    }
}
