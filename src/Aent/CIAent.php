<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\CI\AbstractCIConfigureCIEvent;
use TheAentMachine\Aent\Event\CI\AbstractCIDockerComposeDeployJobEvent;

final class CIAent extends AbstractAent
{
    /**
     * CIAent constructor.
     * @param string $name
     * @param AbstractCIConfigureCIEvent $configureCIEvent
     * @param AbstractCIDockerComposeDeployJobEvent $dockerComposeDeployJobEvent
     */
    public function __construct(string $name, AbstractCIConfigureCIEvent $configureCIEvent, AbstractCIDockerComposeDeployJobEvent $dockerComposeDeployJobEvent)
    {
        parent::__construct($name);
        $this->add($configureCIEvent);
        $this->add($dockerComposeDeployJobEvent);
    }
}
