<?php


namespace TheAentMachine\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * A command that does nothing
 */
final class VoidCommand extends AbstractEventCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setHidden(true);
        $this->addArgument('void_args', InputArgument::IS_ARRAY, 'stub arguments');
    }

    protected function getEventName(): string
    {
        return 'void';
    }

    protected function executeEvent(?string $payload): ?string
    {
        // Let's do nothing.
        $this->log->debug('Event cannot be handled. Ignoring.');
        return null;
    }
}
