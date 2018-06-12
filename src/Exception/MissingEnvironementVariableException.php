<?php
namespace TheAentMachine\Exception;

use TheAentMachine\Enum\PheromoneEnum;
use TheAentMachine\LogLevelConfigurator;

class MissingEnvironementVariableException extends AenthillException
{
    public static function missingEnv(string $variableName): self
    {
        return new self("Missing environement variable '$variableName'");
    }
}
