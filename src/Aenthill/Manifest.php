<?php


namespace TheAentMachine\Aenthill;

use TheAentMachine\Exception\ManifestException;
use TheAentMachine\Exception\MissingEnvironmentVariableException;

/**
 * Utility class to access the manifest data.
 */
class Manifest
{
    /**
     * @return string
     * @throws MissingEnvironmentVariableException
     */
    private static function getFilePath(): string
    {
        $containerProjectDir = Pheromone::getContainerProjectDirectory();
        return $containerProjectDir . '/aenthill.json';
    }

    /**
     * @return mixed[]
     * @throws MissingEnvironmentVariableException
     */
    private static function parse(): array
    {
        $filePath = self::getFilePath();
        $str = file_get_contents($filePath);
        if ($str === false) {
            throw new \RuntimeException('Failed to load the aenthill manifest file ' . $filePath);
        }
        return \GuzzleHttp\json_decode($str, true);
    }

    /**
     * @param string[] $events
     */
    public static function setEvents(array $events): void
    {
        Aenthill::update(null, $events);
    }

    public static function addMetadata(string $key, string $value): void
    {
        Aenthill::update([$key => $value]);
    }

    /**
     * @param string $key
     * @return string
     * @throws MissingEnvironmentVariableException
     * @throws ManifestException
     */
    public static function getMetadata(string $key): string
    {
        $manifest = self::parse();
        $aentID = Pheromone::getID();
        if (isset($manifest['aents'])) {
            foreach ($manifest['aents'] as $ID => $aent) {
                if ($ID === $aentID && array_key_exists('metadata', $aent) && array_key_exists($key, $aent['metadata'])) {
                    return $aent['metadata'][$key];
                }
            }
        }
        throw ManifestException::missingMetadata($key);
    }

    /**
     * @param string $image
     * @param string $key
     * @param array<string,string>|null $metadata
     */
    public static function addDependency(string $image, string $key, ?array $metadata): void
    {
        Aenthill::register($image, $key, $metadata);
    }

    /**
     * @param string $key
     * @return string
     * @throws MissingEnvironmentVariableException
     * @throws ManifestException
     */
    public static function getDependency(string $key): string
    {
        $manifest = self::parse();
        $aentID = Pheromone::getID();
        if (isset($manifest['aents'])) {
            foreach ($manifest['aents'] as $ID => $aent) {
                if ($ID === $aentID && array_key_exists('dependencies', $aent) && array_key_exists($key, $aent['dependencies'])) {
                    return $aent['dependencies'][$key];
                }
            }
        }
        throw ManifestException::missingDependency($key);
    }
}
