<?php

namespace TheAentMachine\Service\Volume;

abstract class Volume implements \JsonSerializable
{
    /** @var string */
    protected $source;

    /**
     * Volume constructor.
     * @param string $source
     */
    public function __construct(string $source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    abstract public function getType(): string;
}
