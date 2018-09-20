<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\CI\AbstractCIAddEvent;
use TheAentMachine\Aent\Event\CI\AbstractCINewImageEvent;

final class CIAent extends AbstractAent
{
    /**
     * CIAent constructor.
     * @param AbstractCIAddEvent $addEvent
     * @param AbstractCINewImageEvent $newImageEvent
     */
    public function __construct(AbstractCIAddEvent $addEvent, AbstractCINewImageEvent $newImageEvent)
    {
        parent::__construct();
        $this->add($addEvent);
        $this->add($newImageEvent);
    }
}
