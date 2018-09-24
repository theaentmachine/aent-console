<?php

namespace TheAentMachine\Aent;

use TheAentMachine\Aent\Event\Builder\AbstractNewImageEvent;

final class BuilderAent extends AbstractAent
{
    /**
     * BuilderAent constructor.
     * @param string $name
     * @param AbstractNewImageEvent $newImageEvent
     */
    public function __construct(string $name, AbstractNewImageEvent $newImageEvent)
    {
        parent::__construct($name);
        $this->add($newImageEvent);
    }
}
