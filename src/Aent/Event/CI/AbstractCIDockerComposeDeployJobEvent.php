<?php

namespace TheAentMachine\Aent\Event\CI;

use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Payload\CI\DockerComposeDeployJobPayload;

abstract class AbstractCIDockerComposeDeployJobEvent extends AbstractJsonEvent
{
    /**
     * @param string $dockerComposeFilename
     * @return void
     */
    abstract protected function addDeployJob(string $dockerComposeFilename): void;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'DOCKER_COMPOSE_DEPLOY_JOB';
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
            "\n👋 Hello! I'm the aent <info>%s</info> and I'm going to setup a deploy job for Docker Compose on your <info>%s</info> environment <info>%s</info>.",
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
        $payload = DockerComposeDeployJobPayload::fromArray($payload);
        $this->addDeployJob($payload->getDockerComposeFilename());
        return null;
    }

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        /** @var Context $context */
        $context = Context::fromMetadata();
        $this->output->writeln(
            sprintf(
                "\nI've added the deploy job for Docker Compose on your <info>%s</info> environment <info>%s</info>. See you later!",
                $context->getEnvironmentType(),
                $context->getEnvironmentName()
            )
        );
    }
}
