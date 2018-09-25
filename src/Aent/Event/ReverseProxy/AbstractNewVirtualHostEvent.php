<?php

namespace TheAentMachine\Aent\Event\ReverseProxy;

use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Payload\ReverseProxy\ReverseProxyNewVirtualHostPayload;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

abstract class AbstractNewVirtualHostEvent extends AbstractJsonEvent
{
    /**
     * @param ReverseProxyNewVirtualHostPayload $payload
     * @return Service
     */
    abstract protected function populateService(ReverseProxyNewVirtualHostPayload $payload): Service;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'NEW_VIRTUAL_HOST';
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
            "\n👋 Hello! I'm the aent <info>%s</info> and I'm going to configure the virtual host of your service on your <info>%s</info> environment <info>%s</info>.",
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
        $payload = ReverseProxyNewVirtualHostPayload::fromArray($payload);
        $service = $this->populateService($payload);
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
            "\nI've successfully configured the virtual host of your service on your <info>%s</info> environment <info>%s</info>.",
            $context->getEnvironmentType(),
            $context->getEnvironmentName()
        ));
    }
}
