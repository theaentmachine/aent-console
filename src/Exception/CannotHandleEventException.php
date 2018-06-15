<?php


namespace TheAentMachine\Exception;

class CannotHandleEventException extends AenthillException
{
    public static function cannotHandleEvent(string $eventName): self
    {
        throw new self('Could not find an Aent that can handle events of type "'.$eventName.'"');
    }
}
