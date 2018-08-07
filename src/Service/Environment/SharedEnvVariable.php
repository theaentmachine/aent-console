<?php

namespace TheAentMachine\Service\Environment;

class SharedEnvVariable extends EnvVariable
{
    /**
     * @var null|string
     */
    private $containerId;

    public function __construct(string $value, string $type, ?string $comment, ?string $containerId)
    {
        parent::__construct($value, $type, $comment);
        $this->containerId = $containerId;
    }

    /**
     * @return null|string
     */
    public function getContainerId(): ?string
    {
        return $this->containerId;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'value' => $this->getValue(),
            'type' => $this->getType(),
            'comment' => $this->getComment(),
            'containerId' => $this->containerId
        ]);
    }
}
