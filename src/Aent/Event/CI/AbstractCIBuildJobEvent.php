<?php

namespace TheAentMachine\Aent\Event\CI;

use Safe\Exceptions\StringsException;
use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Payload\CI\CINewImagePayload;
use TheAentMachine\Aent\Payload\CI\CINewImageReplyPayload;
use function Safe\sprintf;

abstract class AbstractCIBuildJobEvent extends AbstractJsonEvent
{
    /**
     * @param string $serviceName
     * @param string $dockerfileName
     * @return CINewImageReplyPayload
     */
    abstract protected function addBuildJob(string $serviceName, string $dockerfileName): CINewImageReplyPayload;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'BUILD_JOB';
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
            "\n👋 Hello! I'm the aent <info>%s</info> and I'm going to setup a build job for the Dockerfile of your service on your <info>%s</info> environment <info>%s</info>.",
            $this->getAentName(),
            $context->getEnvironmentType(),
            $context->getEnvironmentName()
        ));
    }

    /**
     * @param array<string,string> $payload
     * @return mixed[]|null
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $payload = CINewImagePayload::fromArray($payload);
        $replyPayload = $this->addBuildJob($payload->getServiceName(), $payload->getDockerfileName());
        return $replyPayload->toArray();
    }

    /**
     * @return void
     * @throws StringsException
     */
    protected function afterExecute(): void
    {
        /** @var Context $context */
        $context = Context::fromMetadata();
        $this->output->writeln(
            sprintf(
                "\nI've added the build job for the Dockerfile of your service on your <info>%s</info> environment <info>%s</info>. See you later!",
                $context->getEnvironmentType(),
                $context->getEnvironmentName()
            )
        );
    }
}
