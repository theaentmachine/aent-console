<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\Orchestrator\AbstractOrchestratorAddEvent;

final class OrchestratorAent extends AbstractAent
{
    /**
     * OrchestratorAent constructor.
     * @param string $name
     * @param AbstractOrchestratorAddEvent $addEvent
     */
    public function __construct(string $name, AbstractOrchestratorAddEvent $addEvent)
    {
        parent::__construct($name);
        $this->add($addEvent);
    }
}
