<?php

namespace TheAentMachine\Aent\Payload\Bootstrap;

use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Payload\JsonPayloadInterface;
use TheAentMachine\Aent\Registry\AentItemRegistry;

final class BootstrapPayload implements JsonPayloadInterface
{
    /** @var Context */
    private $context;

    /** @var AentItemRegistry */
    private $orchestratorAent;

    /** @var null|AentItemRegistry */
    private $CIAent;

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'context' => $this->context->toArray(),
            'orchestratorAent' => $this->orchestratorAent->toArray(),
            'CIAent' => !empty($this->CIAent) ? $this->CIAent->toArray() : null,
        ];
    }

    /**
     * @param mixed[] $assoc
     * @return BootstrapPayload
     */
    public static function fromArray(array $assoc): self
    {
        $context = Context::fromArray($assoc['context']);
        $orchestratorAent = AentItemRegistry::fromArray($assoc['orchestratorAent']);
        $CIAent = isset($assoc['CIAent']) && !empty($assoc['CIAent']) ? AentItemRegistry::fromArray($assoc['CIAent']) : null;
        $self = new self();
        $self->setContext($context);
        $self->setOrchestratorAent($orchestratorAent);
        $self->setCIAent($CIAent);
        return $self;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @param Context $context
     */
    public function setContext(Context $context): void
    {
        $this->context = $context;
    }

    /**
     * @return AentItemRegistry
     */
    public function getOrchestratorAent(): AentItemRegistry
    {
        return $this->orchestratorAent;
    }

    /**
     * @param AentItemRegistry $orchestratorAent
     */
    public function setOrchestratorAent(AentItemRegistry $orchestratorAent): void
    {
        $this->orchestratorAent = $orchestratorAent;
    }

    /**
     * @return null|AentItemRegistry
     */
    public function getCIAent(): ?AentItemRegistry
    {
        return $this->CIAent;
    }

    /**
     * @param null|AentItemRegistry $CIAent
     */
    public function setCIAent(?AentItemRegistry $CIAent): void
    {
        $this->CIAent = $CIAent;
    }
}
