<?php

namespace TheAentMachine\Aent\Event\Service\Model;

use TheAentMachine\Service\Service;

final class ServiceState
{
    /** @var Service|null */
    private $developmentVersion;

    /** @var Service|null */
    private $testVersion;

    /** @var Service|null */
    private $productionVersion;

    /**
     * ServiceState constructor.
     * @param null|Service $developmentVersion
     * @param null|Service $testVersion
     * @param null|Service $productionVersion
     */
    public function __construct(?Service $developmentVersion, ?Service $testVersion, ?Service $productionVersion)
    {
        $this->developmentVersion = $developmentVersion;
        $this->testVersion = $testVersion;
        $this->productionVersion = $productionVersion;
    }

    /**
     * @return null|Service
     */
    public function getDevelopmentVersion(): ?Service
    {
        return $this->developmentVersion;
    }

    /**
     * @return null|Service
     */
    public function getTestVersion(): ?Service
    {
        return $this->testVersion;
    }

    /**
     * @return null|Service
     */
    public function getProductionVersion(): ?Service
    {
        return $this->productionVersion;
    }
}
