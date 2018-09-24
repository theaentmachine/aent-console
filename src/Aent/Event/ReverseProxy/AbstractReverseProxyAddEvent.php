<?php

namespace TheAentMachine\Aent\Event\ReverseProxy;

use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

abstract class AbstractReverseProxyAddEvent extends AbstractJsonEvent
{
    /**
     * @return Service
     */
    abstract protected function createService(): Service;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'ADD_REVERSE_PROXY';
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
            "\n👋 Hello! I'm the aent <info>%s</info> and I'll help you setting up a reverse-proxy service for your <info>%s</info> environment <info>%s</info>.",
            $this->getAentName(),
            $context->getEnvironmentType(),
            $context->getEnvironmentName()
        ));
    }

    /**
     * @param array $payload
     * @return array|null
     * @throws ServiceException
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $service = $this->createService();
        return $service->jsonSerialize();
    }

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        /** @var Context $context */
        $context = Context::fromMetadata();
        $this->output->writeln(sprintf(
            "\nI've successfully created a reverse-proxy service for your <info>%s</info> environment <info>%s</info>, see you later!",
            $context->getEnvironmentType(),
            $context->getEnvironmentName()
        ));
    }
}
