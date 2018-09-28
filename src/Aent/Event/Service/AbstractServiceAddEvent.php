<?php

namespace TheAentMachine\Aent\Event\Service;

use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Event\AbstractEvent;
use TheAentMachine\Aent\Event\Service\Model\Environments;
use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Service\Service;

abstract class AbstractServiceAddEvent extends AbstractEvent
{
    /**
     * @param Environments $environments
     * @return Service[]
     */
    abstract protected function createServices(Environments $environments): array;

    /**
     * @return string
     */
    protected function getEventName(): string
    {
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
        $this->output->writeln(sprintf(
            "\n👋 Hello! I'm the aent <info>%s</info> and I'm going to setup myself!",
            $this->getAentName()
        ));
    }

    /**
     * @param null|string $payload
     * @return null|string
     */
    protected function executeEvent(?string $payload): ?string
    {
        $environments = $this->fetchEnvironments();
        $this->prompt->printAltBlock(sprintf("%s: configuring service(s)...", $this->getAentName()));
        $services = $this->createServices($environments);
        foreach ($services as $service) {
            $this->dispatch($environments, $service);
        }
        $this->prompt->printBlock("Dispatch done.");
        return null;
    }

    /**
     * @return void
     */
    protected function afterExecute(): void
    {
        $this->output->writeln(sprintf("\n👋 Hello again! This is the aent <info>%s</info> and we have finished my setup.", $this->getAentName()));
    }

    /**
     * @return Environments
     */
    private function fetchEnvironments(): Environments
    {
        $this->prompt->printAltBlock(sprintf("%s: fetching environments...", $this->getAentName()));
        $responses = Aenthill::dispatchJson('CHOOSE_ENVIRONMENT', []);
        if (empty($responses)) {
            $this->byebye();
        }
        $environments = new Environments();
        foreach ($responses as $response) {
            if (!empty($response)) {
                $context = Context::fromArray($response);
                $environments->add($context);
            }
        }
        if ($environments->isEmpty()) {
            $this->byebye();
        }
        return $environments;
    }

    /**
     * @return void
     */
    private function byebye(): void
    {
        $this->output->writeln(sprintf("\n👋 Hello again! This is the aent <info>%s</info> and you have not selected any environment! Bye!", $this->getAentName()));
        exit(0);
    }

    /**
     * @param Environments $environments
     * @param Service $service
     */
    private function dispatch(Environments $environments, Service $service): void
    {
        $this->prompt->printBlock(sprintf("Dispatching service %s.", $service->getServiceName()));
        $environmentTypes = $service->getDestEnvTypes();
        if (empty($environmentTypes)) {
            $this->dispatchToEnvironments($environments->getDevelopmentEnvironments(), $service);
            $this->dispatchToEnvironments($environments->getTestEnvironments(), $service);
            $this->dispatchToEnvironments($environments->getProductionEnvironments(), $service);
            return;
        }
        foreach ($environmentTypes as $environmentType) {
            switch ($environmentType) {
                case Context::DEV:
                    $this->dispatchToEnvironments($environments->getDevelopmentEnvironments(), $service);
                    break;
                case Context::TEST:
                    $this->dispatchToEnvironments($environments->getTestEnvironments(), $service);
                    break;
                default:
                    $this->dispatchToEnvironments($environments->getProductionEnvironments(), $service);
            }
        }
    }

    /**
     * @param Context[] $environments
     * @param Service $service
     */
    private function dispatchToEnvironments(array $environments, Service $service): void
    {
        foreach ($environments as $environment) {
            Aenthill::dispatchJson('NEW_SERVICE', $service, sprintf('"ENVIRONMENT_NAME" in Metadata and Metadata["ENVIRONMENT_NAME"] == "%s"', $environment->getEnvironmentName()));
        }
    }
}
