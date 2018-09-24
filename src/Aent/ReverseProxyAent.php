<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\ReverseProxy\AbstractNewVirtualHostEvent;
use TheAentMachine\Aent\Event\ReverseProxy\AbstractReverseProxyAddEvent;

final class ReverseProxyAent extends AbstractAent
{
    /**
     * ReverseProxyAent constructor.
     * @param string $name
     * @param AbstractReverseProxyAddEvent $addEvent
     * @param AbstractNewVirtualHostEvent $newVirtualHostEvent
     */
    public function __construct(string $name, AbstractReverseProxyAddEvent $addEvent, AbstractNewVirtualHostEvent $newVirtualHostEvent)
    {
        parent::__construct($name);
        $this->add($addEvent);
        $this->add($newVirtualHostEvent);
    }
}
