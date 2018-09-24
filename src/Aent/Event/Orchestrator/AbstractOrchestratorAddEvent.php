<?php

namespace TheAentMachine\Aent\Event\Orchestrator;

use TheAentMachine\Aent\Context\BaseOrchestratorContext;
use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Context\ContextInterface;
use TheAentMachine\Aent\Event\AbstractJsonEvent;
use TheAentMachine\Aent\Payload\Bootstrap\BootstrapPayload;
use TheAentMachine\Aent\Registry\ColonyRegistry;
use TheAentMachine\Aent\Registry\Exception\ColonyRegistryException;
use TheAentMachine\Aenthill\Aenthill;

abstract class AbstractOrchestratorAddEvent extends AbstractJsonEvent
{
    /**
     * @return ContextInterface
     */
    abstract protected function setup(): ContextInterface;

    /**
     * @param ContextInterface $context
     * @return ContextInterface
     */
    abstract protected function addDeployJobInCI(ContextInterface $context): ContextInterface;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'ADD_ORCHESTRATOR';
    }

    /**
     * @return bool
     */
    protected function shouldRegisterEvents(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    protected function beforeExecute(): void
    {
        /** @var Context $context */
        $context = Context::fromMetadata();
        $this->output->writeln(sprintf(
            "\n👋 Hello! I'm the aent <info>%s</info> and I'm going to setup myself as orchestrator for <info>%s</info> environment <info>%s</info>.",
            $this->getAentName(),
            $context->getEnvironmentType(),
            $context->getEnvironmentName()
        ));
    }

    /**
     * @param mixed[] $payload
     * @return mixed[]|null
     * @throws ColonyRegistryException
     */
    protected function executeJsonEvent(array $payload): ?array
    {
        $orchestratorContext = $this->setup();
        $this->registerDockerfileBuilder();
        /** @var Context $context */
        $context = Context::fromMetadata();
        if (!$context->isDevelopment() && !empty($payload)) {
            $payload = BootstrapPayload::fromArray($payload);
            $this->registerCI($payload);
            $orchestratorContext = $this->addDeployJobInCI($orchestratorContext);
        }
        $this->finish($orchestratorContext);
        return null;
    }

    /**
     * @return void
     * @throws ColonyRegistryException
     */
    private function registerDockerfileBuilder(): void
    {
        $this->prompt->printAltBlock(sprintf("%s: registering Dockerfile builder...", $this->getAentName()));
        $registry = ColonyRegistry::builderRegistry();
        $aent = $registry->getAent(ColonyRegistry::DOCKERFILE);
        /** @var Context $context */
        $context = Context::fromMetadata();
        Aenthill::register($aent->getImage(), BaseOrchestratorContext::BUIlDER_DEPENDENCY_KEY, $context->toArray());
    }

    /**
     * @param BootstrapPayload $payload
     */
    private function registerCI(BootstrapPayload $payload): void
    {
        $this->prompt->printAltBlock(sprintf("%s: registering CI/CD provider...", $this->getAentName()));
        $aent = $payload->getCIAent();
        /** @var Context $context */
        $context = Context::fromMetadata();
        $metadata = \array_merge($context->toArray(), $payload->getCIMetadata());
        Aenthill::register($aent->getImage(), BaseOrchestratorContext::CI_DEPENDENCY_KEY, $metadata);
    }

    /**
     * @param ContextInterface $context
     */
    private function finish(ContextInterface $context): void
    {
        $this->prompt->printAltBlock(sprintf("%s: finishing...", $this->getAentName()));
        $context->toMetadata();
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
                "\nI've finished my setup for your <info>%s</info> environment <info>%s</info>. See you later!",
                $context->getEnvironmentType(),
                $context->getEnvironmentName()
            )
        );
    }
}
