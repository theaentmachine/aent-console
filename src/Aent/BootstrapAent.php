<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\Bootstrap\AbstractBootstrapAddEvent;

final class BootstrapAent extends AbstractAent
{
    /**
     * BootstrapAent constructor.
     * @param string $name
     * @param AbstractBootstrapAddEvent $addEvent
     */
    public function __construct(string $name, AbstractBootstrapAddEvent $addEvent)
    {
        parent::__construct($name);
        $this->add($addEvent);
    }
}
