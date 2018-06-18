<?php


namespace TheAentMachine;

use TheAentMachine\Exception\LogLevelException;
use TheAentMachine\Exception\MissingEnvironmentVariableException;

/**
 * Utility class to access the Aent configuration settings (stored in environment variables)
 */
class Pheromone
{
    private static $levels = [
        'DEBUG' => true,
        'INFO' => true,
        'WARN' => true,
        'ERROR' => true,
    ];

    /**
     * Returns the log level for this Aent.
     *
     * @return string
     */
    public static function getLogLevel(): string
    {
        $logLevel = getenv('PHEROMONE_LOG_LEVEL');

        if ($logLevel === false) {
            throw LogLevelException::emptyLogLevel();
        }

        if (!array_key_exists($logLevel, self::$levels)) {
            throw LogLevelException::invalidLogLevel($logLevel);
        }

        return $logLevel;
    }

    private static function getOrFail(string $variableName): string
    {
        $value = \getenv($variableName);
        if ($value === false) {
            throw MissingEnvironmentVariableException::missingEnv($variableName);
        }
        return $value;
    }

    public static function getWhoAmI(): string
    {
        return self::getOrFail('PHEROMONE_WHOAMI');
    }

    /**
     * The project directory path on the host machine
     */
    public static function getHostProjectDirectory(): string
    {
        return self::getOrFail('PHEROMONE_HOST_PROJECT_DIR');
    }

    /**
     * The project directory path in the container
     */
    public static function getContainerProjectDirectory(): string
    {
        return rtrim(self::getOrFail('PHEROMONE_CONTAINER_PROJECT_DIR'), '/');
    }

    public static function getOriginContainer(): ?string
    {
        $from = getenv('PHEROMONE_FROM');
        return $from === false ? null : $from;
    }
}
