<?php


namespace TheAentMachine\Exception;

final class CannotHandleEventException extends AenthillException
{
    public static function cannotHandleEvent(string $eventName): self
    {
        return new self('Could not find an Aent that can handle events of type "'.$eventName.'"');
    }
}
