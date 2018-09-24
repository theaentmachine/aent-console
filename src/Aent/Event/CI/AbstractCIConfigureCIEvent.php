<?php

namespace TheAentMachine\Aent\Event\CI;

use TheAentMachine\Aent\Event\AbstractJsonEvent;

abstract class AbstractCIConfigureCIEvent extends AbstractJsonEvent
{
    /**
     * @return array<string,string>
     */
    abstract protected function getMetadata(): array;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'CONFIGURE_CI';
    }

    /**
     * @return bool
     */
    protected function shouldRegisterEvents(): bool
    {
        return false;
    }

    /**
     * @return void
     */
    protected function beforeExecute(): void
    {
        $this->output->writeln(sprintf("\n👋 Hello! I'm the aent <info>%s</info> and I'll ask you a few questions about your project.", $this->getAentName()));
    }

    /**
     * @param mixed[] $payload
     * @return array<string,string>|null
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        return $this->getMetadata();
    }

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        $this->output->writeln("\nNo more questions, see you later!");
    }
}
