<?php

namespace TheAentMachine\Aent\Registry;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class AentItemRegistry implements JsonPayloadInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $image;

    /**
     * AentItemRegistry constructor.
     * @param string $name
     * @param string $image
     */
    public function __construct(string $name, string $image)
    {
        $this->name = $name;
        $this->image = $image;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'NAME' => $this->getName(),
            'IMAGE' => $this->getImage(),
        ];
    }

    /**
     * @param array $assoc
     * @return AentItemRegistry
     */
    public static function fromArray(array $assoc): self
    {
        $name = $assoc['NAME'];
        $image = $assoc['IMAGE'];
        return new self($name, $image);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }
}
