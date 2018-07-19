<?php

namespace TheAentMachine;

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
    private static function getOrFail(string $variableName): string
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
    private static function getOrNull(string $variableName): ?string
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
        return self::getOrFail('PHEROMONE_HOST_PROJECT_DIR');
    }

    /**
     * The project directory path in the container.
     *
     * @return string
     * @throws MissingEnvironmentVariableException
     */
    public static function getContainerProjectDirectory(): string
    {
        return rtrim(self::getOrFail('PHEROMONE_CONTAINER_PROJECT_DIR'), '/');
    }

    /**
     * The current image of this aent.
     *
     * @return string
     * @throws MissingEnvironmentVariableException
     */
    public static function getImage(): string
    {
        return self::getOrFail('PHEROMONE_IMAGE_NAME');
    }

    /**
     * The container ID which has started this aent or null.
     *
     * @return null|string
     */
    public static function getOriginContainer(): ?string
    {
        return self::getOrNull('PHEROMONE_FROM_CONTAINER_ID');
    }

    /**
     * The key from the manifest if this aent has been installed or null.
     *
     * @return null|string
     */
    public static function getKey(): ?string
    {
        return self::getOrNull('PHEROMONE_KEY');
    }

    /**
     * The metadata or null.
     *
     * @param string $key the key on which this aent stored the metadata.
     * @return null|string
     */
    public static function getMetadata(string $key): ?string
    {
        return self::getOrNull('PHEROMONE_METADATA_' . strtoupper($key));
    }

    /**
     * The key from the manifest of the dependency or null.
     *
     * @param string $key the key on which this aent stored the dependency.
     * @return null|string
     */
    public static function getDependency(string $key): ?string
    {
        return self::getOrNull('PHEROMONE_DEPENDENCY_' . strtoupper($key));
    }

    /**
     * Returns the content of the manifest.
     *
     * @return mixed[]
     * @throws MissingEnvironmentVariableException
     */
    public static function getAenthillManifestContent(): array
    {
        $containerProjectDir = self::getContainerProjectDirectory();
        $aenthillJSONstr = file_get_contents($containerProjectDir . '/aenthill.json');
        if ($aenthillJSONstr === false) {
            throw new \RuntimeException('Failed to load the aenthill manifest file ' . $containerProjectDir . '/aenthill.json');
        }
        return \GuzzleHttp\json_decode($aenthillJSONstr, true);
    }
}
