<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\CI\AbstractCIBuildJobEvent;
use TheAentMachine\Aent\Event\CI\AbstractCIConfigureCIEvent;
use TheAentMachine\Aent\Event\CI\AbstractCIDockerComposeDeployJobEvent;
use TheAentMachine\Aent\Event\CI\AbstractCIKubernetesDeployJobEvent;

final class CIAent extends AbstractAent
{
    /**
     * CIAent constructor.
     * @param string $name
     * @param AbstractCIConfigureCIEvent $configureCIEvent
     * @param AbstractCIDockerComposeDeployJobEvent $dockerComposeDeployJobEvent
     * @param AbstractCIKubernetesDeployJobEvent $kubernetesDeployJobEvent
     * @param AbstractCIBuildJobEvent $buildJobEvent
     */
    public function __construct(string $name, AbstractCIConfigureCIEvent $configureCIEvent, AbstractCIDockerComposeDeployJobEvent $dockerComposeDeployJobEvent, AbstractCIKubernetesDeployJobEvent $kubernetesDeployJobEvent, AbstractCIBuildJobEvent $buildJobEvent)
    {
        parent::__construct($name);
        $this->add($configureCIEvent);
        $this->add($dockerComposeDeployJobEvent);
        $this->add($kubernetesDeployJobEvent);
        $this->add($buildJobEvent);
    }
}
