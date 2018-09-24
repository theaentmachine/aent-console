<?php

namespace TheAentMachine\Aent\Event\Bootstrap\Model;

use TheAentMachine\Aent\Registry\AentItemRegistry;

final class OrchestratorBootstrap
{
    /** @var AentItemRegistry */
    private $aent;

    /** @var string */
    private $environmentType;

    /** @var string */
    private $environmentName;

    /** @var string */
    private $baseVirtualHost;

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
     * @return string
     */
    public function getEnvironmentType(): string
    {
        return $this->environmentType;
    }

    /**
     * @param string $environmentType
     * @return self
     */
    public function setEnvironmentType(string $environmentType): self
    {
        $this->environmentType = $environmentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnvironmentName(): string
    {
        return $this->environmentName;
    }

    /**
     * @param string $environmentName
     * @return self
     */
    public function setEnvironmentName(string $environmentName): self
    {
        $this->environmentName = $environmentName;
        return $this;
    }

    /**
     * @return string
     */
    public function getBaseVirtualHost(): string
    {
        return $this->baseVirtualHost;
    }

    /**
     * @param string $baseVirtualHost
     * @return self
     */
    public function setBaseVirtualHost(string $baseVirtualHost): self
    {
        $this->baseVirtualHost = $baseVirtualHost;
        return $this;
    }
}
