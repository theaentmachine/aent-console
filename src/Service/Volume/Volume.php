<?php

namespace TheAentMachine\Service\Volume;

abstract class Volume implements \JsonSerializable
{
    /** @var string */
    protected $type;
    /** @var string */
    protected $source;

    /**
     * Volume constructor.
     * @param string $type
     * @param string $source
     */
    public function __construct(string $type, string $source)
    {
        $this->type = $type;
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }
}
