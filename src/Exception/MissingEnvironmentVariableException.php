<?php
namespace TheAentMachine\Exception;

final class MissingEnvironmentVariableException extends AenthillException
{
    public static function missingEnv(string $variableName): self
    {
        return new self("Missing environment variable '$variableName'");
    }
}
