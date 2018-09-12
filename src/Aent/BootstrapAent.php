<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\Bootstrap\AbstractBootstrapAddEvent;

final class BootstrapAent extends AbstractAent
{
    /**
     * BootstrapAent constructor.
     * @param AbstractBootstrapAddEvent $addEvent
     */
    public function __construct(AbstractBootstrapAddEvent $addEvent)
    {
        parent::__construct();
        $this->add($addEvent);
    }
}
