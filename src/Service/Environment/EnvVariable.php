<?php

namespace TheAentMachine\Service\Environment;

class EnvVariable implements \JsonSerializable
{
    /** @var string */
    private $value;
    /** @var string */
    private $type;

    /**
     * EnvironmentVariable constructor.
     * @param string $value
     * @param string $type
     */
    public function __construct(string $value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
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
        return array(
            'value' => $this->value,
            'type' => $this->type
        );
    }
}
