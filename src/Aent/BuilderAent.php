<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\Builder\AbstractNewImageEvent;

final class BuilderAent extends AbstractAent
{
    /**
     * BuilderAent constructor.
     * @param AbstractNewImageEvent $newImageEvent
     */
    public function __construct(AbstractNewImageEvent $newImageEvent)
    {
        parent::__construct();
        $this->add($newImageEvent);
    }
}
