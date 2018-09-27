<?php

namespace TheAentMachine\Aent\Event\Orchestrator;

use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\AbstractJsonEvent;

final class OrchestratorChooseEnvironmentEvent extends AbstractJsonEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'CHOOSE_ENVIRONMENT';
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
        /** @var Context $context */
        $context = Context::fromMetadata();
        $this->output->writeln(sprintf(
            "\n👋 Hello! I'm the aent <info>%s</info> and I want to know if you want the service(s) on your <info>%s</info> environment <info>%s</info>.",
            $this->getAentName(),
            $context->getEnvironmentType(),
            $context->getEnvironmentName()
        ));
    }

    /**
     * @param mixed[] $payload
     * @return mixed[]|null
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $response = $this->prompt->confirm("\nConfirm?", null, true);
        if ($response) {
            /** @var Context $context */
            $context = Context::fromMetadata();
            return $context->toArray();
        }
        return null;
    }

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        // Let's do nothing.
    }
}