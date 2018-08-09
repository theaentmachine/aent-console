<?php

namespace TheAentMachine\Service\Volume;

abstract class Volume implements \JsonSerializable
{
    /** @var string */
    protected $source;
    /** @var null|string */
    protected $comment;

    public function __construct(string $source, ?string $comment = null)
    {
        $this->source = $source;
        $this->comment = $comment;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    abstract public function getType(): string;
}
