<?php

namespace TheAentMachine\Aent\Payload\Bootstrap;

use TheAentMachine\Aent\Context\Context;
use TheAentMachine\Aent\Payload\JsonPayloadInterface;

final class BootstrapPayload implements JsonPayloadInterface
{
    /** @var Context */
    private $context;

    /** @var string */
    private $orchestratorAent;

    /** @var null|string */
    private $CIAent;

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'context' => $this->context->toArray(),
            'orchestratorAent' => $this->orchestratorAent,
            'CIAent' => $this->CIAent,
        ];
    }

    /**
     * @param mixed[] $assoc
     * @return BootstrapPayload
     */
    public static function fromArray(array $assoc): self
    {
        $context = Context::fromArray($assoc['context']);
        $orchestratorAent = $assoc['orchestratorAent'];
        $CIAent = isset($assoc['CIAent']) ? $assoc['CIAent'] : null;
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
     * @return string
     */
    public function getOrchestratorAent(): string
    {
        return $this->orchestratorAent;
    }

    /**
     * @param string $orchestratorAent
     */
    public function setOrchestratorAent(string $orchestratorAent): void
    {
        $this->orchestratorAent = $orchestratorAent;
    }

    /**
     * @return null|string
     */
    public function getCIAent(): ?string
    {
        return $this->CIAent;
    }

    /**
     * @param null|string $CIAent
     */
    public function setCIAent(?string $CIAent): void
    {
        $this->CIAent = $CIAent;
    }
}
