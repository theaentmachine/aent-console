<?php

namespace TheAentMachine\Exception;

use TheAentMachine\Aenthill\Pheromone;

class EventException extends AenthillException
{
    /**
     * @return EventException
     * @throws MissingEnvironmentVariableException
     */
    public static function cannotHandleAddEvent(): self
    {
        $image = Pheromone::getImage();
        return new self("The aent '$image' cannot be installed in the manifest");
    }
}
