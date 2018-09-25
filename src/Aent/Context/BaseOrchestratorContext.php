<?php

namespace TheAentMachine\Aent\Context;

use TheAentMachine\Aenthill\Aenthill;

class BaseOrchestratorContext extends Context
{
    /** @var string */
    private $baseVirtualHost;

    public const BUIlDER_DEPENDENCY_KEY = 'BUIlDER';
    public const CI_DEPENDENCY_KEY = 'CI';

    /**
     * BaseOrchestratorContext constructor.
     * @param string $environmentType
     * @param string $environmentName
     * @param string $baseVirtualHost
     */
    public function __construct(string $environmentType, string $environmentName, string $baseVirtualHost)
    {
        parent::__construct($environmentType, $environmentName);
        $this->baseVirtualHost = $baseVirtualHost;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        $assoc = parent::toArray();
        $assoc['BASE_VIRTUAL_HOST'] = $this->baseVirtualHost;
        return $assoc;
    }

    /**
     * @param array<string,string> $assoc
     * @return mixed
     */
    public static function fromArray(array $assoc)
    {
        $context = parent::fromArray($assoc);
        $baseVirtualHost = $assoc['BASE_VIRTUAL_HOST'];
        return new self($context->getEnvironmentType(), $context->getEnvironmentName(), $baseVirtualHost);
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
        return new self($context->getEnvironmentType(), $context->getEnvironmentName(), $baseVirtualHost);
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
}
