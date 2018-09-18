<?php

namespace TheAentMachine\Aent\Event\Helper;

use TheAentMachine\Aenthill\Aenthill;

final class EventHelper
{
    /**
     * @param string[] $events
     */
    public static function registerEvents(array $events): void
    {
        Aenthill::update(null, $events);
    }
}
