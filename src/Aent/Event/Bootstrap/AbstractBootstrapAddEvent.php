<?php

namespace TheAentMachine\Aent\Event\Bootstrap;

use TheAentMachine\Aent\Context\BaseOrchestratorContext;
use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\Bootstrap\Model\CIBootstrap;
use TheAentMachine\Aent\Event\Bootstrap\Model\OrchestratorBootstrap;
use TheAentMachine\Aent\Payload\Bootstrap\BootstrapPayload;
use TheAentMachine\Aent\Registry\ColonyRegistry;
use TheAentMachine\Aent\Registry\Exception\ColonyRegistryException;
use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Aent\Event\AbstractEvent;

abstract class AbstractBootstrapAddEvent extends AbstractEvent
{
    /** @var OrchestratorBootstrap[] */
    private $orchestratorsBootstraps;

    /** @var null|CIBootstrap */
    private $CIBootstrap;

    /**
     * @return OrchestratorBootstrap[]
     */
    abstract protected function getOrchestratorsBootstraps(): array;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
        // TODO update event name to ADD_BOOTSTRAP
        return 'ADD';
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
        $this->output->writeln(sprintf("👋 Hello! I'm the aent <info>%s</info> and I'll help you bootstrapping a Docker project for your web application.", $this->getAentName()));
    }

    /**
     * @param null|string $payload
     * @return null|string
     * @throws ColonyRegistryException
     */
    protected function executeEvent(?string $payload): ?string
    {
        $this->orchestratorsBootstraps = $this->getOrchestratorsBootstraps();
        $this->CIBootstrap = $this->hasOrchestratorsForRemoteEnvironments() ? $this->getCIBootstrap() : null;
        $this->output->writeln("\n👌 I'm going to wake up some aents, see you later!");
        $this->printSummary($this->orchestratorsBootstraps);
        foreach ($this->orchestratorsBootstraps as $bootstrap) {
            $this->addOrchestrator($bootstrap);
        }
        $this->prompt->printBlock('Setup done.');
        return null;
    }

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        $this->output->writeln(sprintf("\n👋 Hello again! This is the aent <info>%s</info> and we have finished your project setup.", $this->getAentName()));
        $this->printSummary($this->orchestratorsBootstraps);
        $this->output->writeln("\nYou may now start adding services with <info>aenthill add [image]</info>. See https://aenthill.github.io/ for the complete documentation!");
    }

    /**
     * @return bool
     */
    private function hasOrchestratorsForRemoteEnvironments(): bool
    {
        foreach ($this->orchestratorsBootstraps as $orchestratorBootstrap) {
            if ($orchestratorBootstrap->getEnvironmentType() !== Context::DEV) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return CIBootstrap
     * @throws ColonyRegistryException
     */
    private function getCIBootstrap(): CIBootstrap
    {
        $this->output->writeln("\nHey, you have defined at least one remote environment, so let's configure a <info>CI/CD provider</info>!");
        $text = "\nYour CI/CD provider";
        $helpText = "A CI provider will automatically build the images of your containers and deploy them in your remote environment(s)";
        $aent = $this->prompt->getPromptHelper()->getFromColonyRegistry(ColonyRegistry::CIRegistry(), $text, $helpText);
        $this->output->writeln(sprintf("\n👌 Alright, I'm going to wake up the aent <info>%s</info>!", $aent->getName()));
        $response = Aenthill::runJson($aent->getImage(), 'CONFIGURE_CI', []);
        $metadata = \GuzzleHttp\json_decode($response[0], true);
        $this->output->writeln(sprintf(
            "\n👋 Hello again! This is the aent <info>%s</info> and I've received the configuration of the aent <info>%s</info>.",
            $this->getAentName(),
            $aent->getName()
        ));
        $bootstrap = new CIBootstrap();
        $bootstrap
            ->setAent($aent)
            ->setMetadata($metadata);
        return $bootstrap;
    }

    /**
     * @param OrchestratorBootstrap $bootstrap
     */
    private function addOrchestrator(OrchestratorBootstrap $bootstrap): void
    {
        $message = sprintf(
            "Setting up %s for %s environment %s.",
            $bootstrap->getAent()->getName(),
            $bootstrap->getEnvironmentType(),
            $bootstrap->getEnvironmentName()
        );
        $this->prompt->printBlock($message);
        $key = \uniqid();
        $context = new BaseOrchestratorContext($bootstrap->getEnvironmentType(), $bootstrap->getEnvironmentName(), $bootstrap->getBaseVirtualHost());
        Aenthill::register($bootstrap->getAent()->getImage(), $key, $context->toArray());
        if (!$context->isDevelopment() && !empty($this->CIBootstrap)) {
            $payload = new BootstrapPayload($this->CIBootstrap->getAent(), $this->CIBootstrap->getMetadata());
        }
        Aenthill::runJson($key, 'ADD_ORCHESTRATOR', !empty($payload) ? $payload->toArray() : []);
    }

    /**
     * @param OrchestratorBootstrap[] $orchestratorsBootstraps
     * @return void
     */
    protected function printSummary(array $orchestratorsBootstraps): void
    {
        $this->output->writeln("\nSetup summary:");
        foreach ($orchestratorsBootstraps as $orchestratorBootstrap) {
            $message = sprintf(
                " - a <info>%s</info> environment <info>%s</info> with the base virtual host <info>%s</info> and with <info>%s</info> as orchestrator",
                $orchestratorBootstrap->getEnvironmentType(),
                $orchestratorBootstrap->getEnvironmentName(),
                $orchestratorBootstrap->getBaseVirtualHost(),
                $orchestratorBootstrap->getAent()->getName()
            );
            $this->output->writeln($message);
        }
        if (!empty($this->CIBootstrap)) {
            $this->output->writeln(sprintf(" - <info>%s</info> as CI/CD provider for your remote environment(s)", $this->CIBootstrap->getAent()->getName()));
        }
    }
}
