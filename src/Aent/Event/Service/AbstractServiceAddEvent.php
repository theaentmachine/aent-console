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

    protected function executeEvent(?string $payload): ?string
    {
        $environments = $this->fetchEnvironments();
        $this->prompt->printAltBlock(sprintf("%s: configuring service(s)...", $this->getAentName()));
        $services = $this->createServices($environments);
        foreach ($services as $service) {
            $this->prompt->printBlock(sprintf("Dispatching service %s.", $service->getServiceName()));
            Aenthill::dispatchJson('NEW_SERVICE', $service);
        }
        $this->prompt->printBlock("Dispatch done.");
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
        $responses = Aenthill::dispatchJson('CHOOSE_ENVIRONMENT', null);
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
}
