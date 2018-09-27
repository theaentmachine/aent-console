<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\Orchestrator\AbstractOrchestratorAddEvent;
use TheAentMachine\Aent\Event\Orchestrator\AbstractOrchestratorNewServiceEvent;
use TheAentMachine\Aent\Event\Orchestrator\OrchestratorChooseEnvironmentEvent;

final class OrchestratorAent extends AbstractAent
{
    /**
     * OrchestratorAent constructor.
     * @param string $name
     * @param AbstractOrchestratorAddEvent $addEvent
     * @param AbstractOrchestratorNewServiceEvent $newServiceEvent
     */
    public function __construct(string $name, AbstractOrchestratorAddEvent $addEvent, AbstractOrchestratorNewServiceEvent $newServiceEvent)
    {
        parent::__construct($name);
        $this->add($addEvent);
        $this->add($newServiceEvent);
        $this->add(new OrchestratorChooseEnvironmentEvent());
    }
}
