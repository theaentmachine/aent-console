<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\Orchestrator\AbstractOrchestratorAddEvent;

final class OrchestratorAent extends AbstractAent
{
    /**
     * OrchestratorAent constructor.
     * @param AbstractOrchestratorAddEvent $addEvent
     */
    public function __construct(AbstractOrchestratorAddEvent $addEvent)
    {
        parent::__construct();
        $this->add($addEvent);
    }
}
