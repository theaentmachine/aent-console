<?php

namespace TheAentMachine\Aent\Event\Service\Model;

use TheAentMachine\Aent\Context\Context;

final class Environments
{
    /** @var Context[] */
    private $environments;

    /**
     * Environnments constructor.
     */
    public function __construct()
    {
        $this->environments = [];
    }

    /**
     * @param Context $context
     * @return void
     */
    public function add(Context $context): void
    {
        $this->environments[] = $context;
    }

    /**
     * @return bool
     */
    public function hasDevelopmentEnvironments(): bool
    {
        foreach ($this->environments as $environment) {
            if ($environment->isDevelopment()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasTestEnvironments(): bool
    {
        foreach ($this->environments as $environment) {
            if ($environment->isTest()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function hasProductionEnvironments(): bool
    {
        foreach ($this->environments as $environment) {
            if ($environment->isProduction()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->environments);
    }
}
