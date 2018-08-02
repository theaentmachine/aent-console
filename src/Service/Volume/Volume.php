<?php

namespace TheAentMachine\Service\Volume;

abstract class Volume implements \JsonSerializable
{
    /** @var string */
    protected $source;
    /** @var null|string */
    protected $comment;

    /**
     * Volume constructor.
     * @param string $source
     * @param null|string $comment
     */
    public function __construct(string $source, ?string $comment = null)
    {
        $this->source = $source;
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return null|string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    abstract public function getType(): string;
}
