<?php

namespace TheAentMachine\Service\Environment;

class EnvVariable implements \JsonSerializable
{
    /** @var string */
    private $value;
    /** @var string */
    private $type;
    /** @var null|string */
    private $comment;

    /**
     * EnvironmentVariable constructor.
     * @param string $value
     * @param string $type
     * @param null|string $comment
     */
    public function __construct(string $value, string $type, ?string $comment)
    {
        $this->value = $value;
        $this->type = $type;
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getComment(): ?string
    {
        return $this->comment;
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
            'value' => $this->value,
            'type' => $this->type,
            'comment' => $this->comment
        ], function ($v) {
            return null !== $v;
        });
    }
}
