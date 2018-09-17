<?php

namespace TheAentMachine\Aent\Event\Bootstrap;

use TheAentMachine\Aent\Payload\Bootstrap\BootstrapPayload;
use TheAentMachine\Aent\Payload\Bootstrap\BootstrapPayloadAggregator;
use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Aent\Event\AbstractEvent;

abstract class AbstractBootstrapAddEvent extends AbstractEvent
{
    /**
     * @return string
     */
    protected function getEventName(): string
    {
        return 'ADD';
    }

    /**
     * @param null|string $payload
     * @return null|string
     */
    protected function executeEvent(?string $payload): ?string
    {
        $this->before();
        $aggregator = $this->process();
        /** @var BootstrapPayload $payload */
        foreach ($aggregator->getBootstrapPayloads() as $payload) {
            $this->printSettingUp($payload);
            $key = \uniqid();
            Aenthill::register($payload->getOrchestratorAent()->getImage(), $key);
            Aenthill::runJson($key, 'ADD', $payload->toArray());
        }
        $this->after();
        return null;
    }

    /**
     * @param BootstrapPayload $payload
     * @return void
     */
    private function printSettingUp(BootstrapPayload $payload): void
    {
        $orchastratorName = $payload->getOrchestratorAent()->getName();
        $context = $payload->getContext();
        $type = $context->getType();
        $name = $context->getName();
        $this->output->writeln("\nSetting up <info>$orchastratorName</info> for <info>$type</info> environment <info>$name</info>.");
    }

    /**
     * @return void
     */
    abstract protected function before(): void;

    /**
     * @return BootstrapPayloadAggregator
     */
    abstract protected function process(): BootstrapPayloadAggregator;

    /**
     * @return void
     */
    abstract protected function after(): void;
}
