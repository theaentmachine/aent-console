<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\ReverseProxy\AbstractNewVirtualHostEvent;
use TheAentMachine\Aent\Event\ReverseProxy\AbstractReverseProxyAddEvent;

final class ReverseProxyAent extends AbstractAent
{
    /**
     * ReverseProxyAent constructor.
     * @param AbstractReverseProxyAddEvent $addEvent
     * @param AbstractNewVirtualHostEvent $newVirtualHostEvent
     */
    public function __construct(AbstractReverseProxyAddEvent $addEvent, AbstractNewVirtualHostEvent $newVirtualHostEvent)
    {
        parent::__construct();
        $this->add($addEvent);
        $this->add($newVirtualHostEvent);
    }
}
