<?php
namespace TheAentMachine\Exception;

class MissingEnvironmentVariableException extends AenthillException
{
    public static function missingEnv(string $variableName): self
    {
        return new self("Missing environement variable '$variableName'");
    }
}
