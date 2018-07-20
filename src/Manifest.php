<?php


namespace TheAentMachine;

/**
 * Utility class to access the manifest data.
 */
class Manifest
{
    private static function getFilePath(): string
    {
        $containerProjectDir = Pheromone::getContainerProjectDirectory();
        return $containerProjectDir . '/aenthill.json';
    }

    private static function parse(): array
    {
        $filePath = self::getFilePath();
        $str = file_get_contents($filePath);
        if ($str === false) {
            throw new \RuntimeException('Failed to load the aenthill manifest file ' . $filePath);
        }
        return \GuzzleHttp\json_decode($str, true);
    }

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
     * @return null|string
     * @throws Exception\MissingEnvironmentVariableException
     */
    public static function getMetadata(string $key): ?string
    {
        $manifest = self::parse();
        $aentID = Pheromone::getKey();
        if (isset($manifest['aents'])) {
            foreach ($manifest['aents'] as $ID => $aent) {
                if ($ID === $aentID && array_key_exists('metadata', $aent) && array_key_exists($key, $aent['metadata'])) {
                    return $aent['metadata'][$key];
                }
            }
        }
        return null;
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
     * @return null|string
     * @throws Exception\MissingEnvironmentVariableException
     */
    public static function getDependency(string $key): ?string
    {
        $manifest = self::parse();
        $aentID = Pheromone::getKey();
        if (isset($manifest['aents'])) {
            foreach ($manifest['aents'] as $ID => $aent) {
                if ($ID === $aentID && array_key_exists('dependencies', $aent) && array_key_exists($key, $aent['dependencies'])) {
                    return $aent['dependencies'][$key];
                }
            }
        }
        return null;
    }
}
