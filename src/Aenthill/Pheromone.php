<?php

namespace TheAentMachine\Aenthill;

use TheAentMachine\Exception\LogLevelException;
use TheAentMachine\Exception\MissingEnvironmentVariableException;

/**
 * Utility class to access the aent configuration settings (stored in environment variables)
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
     * Returns the log level for this aent.
     *
     * @return string
     * @throws LogLevelException
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

    /**
     * Tries to returns the value of an environment variable or throws an exception.
     *
     * @param string $variableName the environment variable key
     * @return string
     * @throws MissingEnvironmentVariableException
     */
    private static function mustGet(string $variableName): string
    {
        $value = getenv($variableName);
        if ($value === false) {
            throw MissingEnvironmentVariableException::missingEnv($variableName);
        }
        return $value;
    }

    /**
     * Tries to returns the value of an environment variable or null.
     *
     * @param string $variableName the environment variable key
     * @return null|string
     */
    private static function get(string $variableName): ?string
    {
        $value = getenv($variableName);
        return $value === false ? null : $value;
    }

    /**
     * The project directory path on the host machine.
     *
     * @return string
     * @throws MissingEnvironmentVariableException
     */
    public static function getHostProjectDirectory(): string
    {
        return self::mustGet('PHEROMONE_HOST_PROJECT_DIR');
    }

    /**
     * The project directory path in the container.
     *
     * @return string
     * @throws MissingEnvironmentVariableException
     */
    public static function getContainerProjectDirectory(): string
    {
        return rtrim(self::mustGet('PHEROMONE_CONTAINER_PROJECT_DIR'), '/');
    }

    /**
     * The current image of this aent.
     *
     * @return string
     * @throws MissingEnvironmentVariableException
     */
    public static function getImage(): string
    {
        return self::mustGet('PHEROMONE_IMAGE_NAME');
    }

    /**
     * The ID from the manifest if this aent has been registred.
     *
     * @return string
     * @throws MissingEnvironmentVariableException
     */
    public static function getID(): string
    {
        return self::mustGet('PHEROMONE_ID');
    }

    /**
     * The container ID which has started this aent or null.
     *
     * @return null|string
     */
    public static function getOriginContainer(): ?string
    {
        return self::get('PHEROMONE_FROM_CONTAINER_ID');
    }
}
