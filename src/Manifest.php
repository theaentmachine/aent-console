<?php


namespace TheAentMachine;

/**
 * Utility class to access the manifest data.
 */
class Manifest
{
    /** @var string */
    private $filePath;

    /** @var array|null */
    private $content;

    /**
     * Manifest constructor.
     */
    public function __construct()
    {
        $containerProjectDir = Pheromone::getContainerProjectDirectory();
        $this->filePath = $containerProjectDir . '/aenthill.json';
    }

    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            $this->parse();
            call_user_func([$this, $method], $arguments);
            $this->parse();
        }
    }

    private function parse(): void
    {
        $str = file_get_contents($this->filePath);
        if ($str === false) {
            throw new \RuntimeException('Failed to load the aenthill manifest file ' . $this->filePath);
        }
        $this->content = \GuzzleHttp\json_decode($str, true);
    }

    public function setEvents(array $events): void
    {
        Aenthill::update(null, $events);
    }

    public function addMetadata(string $key, string $value): void
    {
        Aenthill::update([$key => $value]);
    }

    /**
     * @param string $key
     * @return null|string
     * @throws Exception\MissingEnvironmentVariableException
     */
    public function getMetadata(string $key): ?string
    {
        $aentID = Pheromone::getKey();
        if (isset($this->content['aents'])) {
            foreach ($this->content['aents'] as $ID => $aent) {
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
    public function addDependency(string $image, string $key, ?array $metadata): void
    {
        Aenthill::register($image, $key, $metadata);
    }

    /**
     * @param string $key
     * @return null|string
     * @throws Exception\MissingEnvironmentVariableException
     */
    public function getDependency(string $key): ?string
    {
        $aentID = Pheromone::getKey();
        if (isset($this->content['aents'])) {
            foreach ($this->content['aents'] as $ID => $aent) {
                if ($ID === $aentID && array_key_exists('dependencies', $aent) && array_key_exists($key, $aent['dependencies'])) {
                    return $aent['dependencies'][$key];
                }
            }
        }
        return null;
    }
}
