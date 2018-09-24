<?php

namespace TheAentMachine\Aent\Event\Bootstrap\Model;

use TheAentMachine\Aent\Registry\AentItemRegistry;

final class CIBootstrap
{
    /** @var AentItemRegistry */
    private $aent;

    /** @var array<string,string> */
    private $metadata;

    /**
     * @return AentItemRegistry
     */
    public function getAent(): AentItemRegistry
    {
        return $this->aent;
    }

    /**
     * @param AentItemRegistry $aent
     * @return self
     */
    public function setAent(AentItemRegistry $aent): self
    {
        $this->aent = $aent;
        return $this;
    }

    /**
     * @return array<string,string>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array<string,string> $metadata
     * @return self
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }
}
