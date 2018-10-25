<?php

namespace TheAentMachine\Aent\Context;

use TheAentMachine\Aenthill\Aenthill;

class BaseOrchestratorContext extends Context
{
    /** @var string */
    private $baseVirtualHost;

    /** @var bool */
    private $singleEnvironment;

    public const BUILDER_DEPENDENCY_KEY = 'BUILDER';
    public const CI_DEPENDENCY_KEY = 'CI';

    /**
     * BaseOrchestratorContext constructor.
     * @param string $environmentType
     * @param string $environmentName
     * @param string $baseVirtualHost
     * @param bool $singleEnvironment
     */
    public function __construct(string $environmentType, string $environmentName, string $baseVirtualHost, bool $singleEnvironment = true)
    {
        parent::__construct($environmentType, $environmentName);
        $this->baseVirtualHost = $baseVirtualHost;
        $this->singleEnvironment = $singleEnvironment;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $assoc = parent::toArray();
        $assoc['BASE_VIRTUAL_HOST'] = $this->baseVirtualHost;
        $assoc['IS_SINGLE_ENVIRONMENT'] = $this->singleEnvironment ? 'true' : 'false';
        return $assoc;
    }

    /**
     * @param array<string,mixed> $assoc
     * @return mixed
     */
    public static function fromArray(array $assoc)
    {
        $context = parent::fromArray($assoc);
        $baseVirtualHost = $assoc['BASE_VIRTUAL_HOST'];
        $singleEnvironment = $assoc['IS_SINGLE_ENVIRONMENT'] === 'true';
        $self = new self($context->getEnvironmentType(), $context->getEnvironmentName(), $baseVirtualHost, $singleEnvironment);
        return $self;
    }

    /**
     * @return void
     */
    public function toMetadata(): void
    {
        Aenthill::update($this->toArray());
    }

    /**
     * @return mixed
     */
    public static function fromMetadata()
    {
        $context = parent::fromMetadata();
        $baseVirtualHost = Aenthill::metadata('BASE_VIRTUAL_HOST');
        $singleEnvironment = Aenthill::metadata('IS_SINGLE_ENVIRONMENT') === 'true';
        $self = new self($context->getEnvironmentType(), $context->getEnvironmentName(), $baseVirtualHost, $singleEnvironment);
        return $self;
    }

    /**
     * @return string
     */
    public function getBaseVirtualHost(): string
    {
        return $this->baseVirtualHost;
    }

    /**
     * @param string $baseVirtualHost
     * @return void
     */
    public function setBaseVirtualHost(string $baseVirtualHost): void
    {
        $this->baseVirtualHost = $baseVirtualHost;
    }

    /**
     * @return bool
     */
    public function isSingleEnvironment(): bool
    {
        return $this->singleEnvironment;
    }

    /**
     * @param bool $singleEnvironment
     */
    public function setSingleEnvironment(bool $singleEnvironment): void
    {
        $this->singleEnvironment = $singleEnvironment;
    }
}
