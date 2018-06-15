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
    abstract public static function getType(): string;
}
