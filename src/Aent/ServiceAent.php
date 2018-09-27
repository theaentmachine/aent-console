<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\Service\AbstractServiceAddEvent;

final class ServiceAent extends AbstractAent
{
    /**
     * ServiceAent constructor.
     * @param string $name
     * @param AbstractServiceAddEvent $addEvent
     */
    public function __construct(string $name, AbstractServiceAddEvent $addEvent)
    {
        parent::__construct($name);
        $this->add($addEvent);
    }
}
