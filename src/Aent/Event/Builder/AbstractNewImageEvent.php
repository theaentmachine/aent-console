<?php

namespace TheAentMachine\Aent\Event\Builder;

use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\StringsException;
use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Payload\Builder\NewImageReplyPayload;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;
use function Safe\sprintf;

abstract class AbstractNewImageEvent extends AbstractJsonEvent
{
    /** @var string */
    private $dockerfileName;

    /**
     * @param Service $service
     * @return NewImageReplyPayload
     */
    abstract protected function createDockerfile(Service $service): NewImageReplyPayload;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'NEW_IMAGE';
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
     * @throws StringsException
     */
    protected function beforeExecute(): void
    {
        /** @var Context $context */
        $context = Context::fromMetadata();
        $this->output->writeln(sprintf(
            "\n👋 Hello! I'm the aent <info>%s</info> and I'm going to create a Dockerfile for your service for your <info>%s</info> environment <info>%s</info>.",
            $this->getAentName(),
            $context->getEnvironmentType(),
            $context->getEnvironmentName()
        ));
    }

    /**
     * @param array $payload
     * @return array|null
     * @throws ServiceException
     * @throws StringsException
     * @throws FilesystemException
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $service = Service::parsePayload($payload);
        $payload = $this->createDockerfile($service);
        $this->dockerfileName = $payload->getDockerfileName();
        return $payload->toArray();
    }

    /**
     * @return void
     * @throws StringsException
     */
    protected function afterExecute(): void
    {
        /** @var Context $context */
        $context = Context::fromMetadata();
        $this->output->writeln(sprintf(
            "\nI've successfully created <info>%s</info> for your <info>%s</info> environment <info>%s</info>!",
            $this->dockerfileName,
            $context->getEnvironmentType(),
            $context->getEnvironmentName()
        ));
    }
}
