<?php

namespace TheAentMachine\Aent\Event\CI;

use Safe\Exceptions\StringsException;
use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\K8SProvider\Provider;
use TheAentMachine\Aent\Payload\CI\KubernetesDeployJobPayload;
use function Safe\sprintf;

abstract class AbstractCIKubernetesDeployJobEvent extends AbstractJsonEvent
{
    /**
     * @param string $directoryName
     * @param Provider $provider
     * @return void
     */
    abstract protected function addDeployJob(string $directoryName, Provider $provider): void;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'KUBERNETES_DEPLOY_JOB';
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
            "\n👋 Hello! I'm the aent <info>%s</info> and I'm going to setup a deploy job for Kubernetes on your <info>%s</info> environment <info>%s</info>.",
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
        $payload = KubernetesDeployJobPayload::fromArray($payload);
        $this->addDeployJob($payload->getDirectoryName(), $payload->getProvider());
        return null;
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
                "\nI've added the deploy job for Kubernetes on your <info>%s</info> environment <info>%s</info>. See you later!",
                $context->getEnvironmentType(),
                $context->getEnvironmentName()
            )
        );
    }
}
