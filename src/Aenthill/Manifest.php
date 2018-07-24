<?php


namespace TheAentMachine\Aenthill;

use TheAentMachine\Exception\ManifestException;

/**
 * Utility class to access the manifest data.
 */
class Manifest
{

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
     * @throws ManifestException
     */
    public static function getMetadata(string $key): string
    {
        try {
            return Aenthill::metadata($key);
        } catch (\Exception $e) {
            throw ManifestException::missingMetadata($key);
        }
    }

    /**
     * @param string $key
     * @return null|string
     */
    public static function getMetadataOrNull(string $key): ?string
    {
        try {
            return self::getMetadata($key);
        } catch (ManifestException $e) {
            return null;
        }
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
     * @throws ManifestException
     */
    public static function getDependency(string $key): string
    {
        try {
            return Aenthill::dependency($key);
        } catch (\Exception $e) {
            throw ManifestException::missingDependency($key);
        }
    }


    /**
     * @param string $key
     * @return null|string
     */
    public static function getDependencyOrNull(string $key): ?string
    {
        try {
            return self::getDependency($key);
        } catch (ManifestException $e) {
            return null;
        }
    }
}
