<?php

namespace TheAentMachine\Aent\K8SProvider;

use TheAentMachine\Aent\Context\ContextInterface;
use TheAentMachine\Aent\Payload\JsonPayloadInterface;
use TheAentMachine\Aenthill\Aenthill;

final class Provider implements ContextInterface, JsonPayloadInterface
{
    public const RANCHER = 'Rancher';
    public const GOOGLE_CLOUD = 'Google Cloud';

    /** @var string */
    private $name;

    /** @var bool */
    private $certManager;

    /** @var bool */
    private $useNodePortForIngress;

    /** @var string */
    private $ingressClass;

    /**
     * Provider constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return Provider
     */
    public static function newRancherProvider(): self
    {
        $self = new self(self::RANCHER);
        return $self;
    }

    /**
     * @return Provider
     */
    public static function newGoogleCloudProvider(): self
    {
        $self = new self(self::GOOGLE_CLOUD);
        return $self;
    }

    /**
     * @return void
     */
    public function toMetadata(): void
    {
        Aenthill::update([
            'PROVIDER_NAME' => $this->name,
            'CERT_MANAGER' => $this->certManager ? 'true' : 'false',
            'USE_NODE_PORT_FOR_INGRESS' => $this->useNodePortForIngress ? 'true' : 'false',
            'INGRESS_CLASS' => $this->ingressClass,
        ]);
    }

    /**
     * @return self
     */
    public static function fromMetadata()
    {
        $name = Aenthill::metadata('PROVIDER_NAME');
        $certManager = Aenthill::metadata('CERT_MANAGER') === 'true';
        $useNodePortForIngress = Aenthill::metadata('USE_NODE_PORT_FOR_INGRESS') === 'true';
        $ingressClass = Aenthill::metadata('INGRESS_CLASS');
        $self = new self($name);
        $self->certManager = $certManager;
        $self->useNodePortForIngress = $useNodePortForIngress;
        $self->ingressClass = $ingressClass;
        return $self;
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
           'PROVIDER_NAME' => $this->name,
           'CERT_MANAGER' => $this->certManager ? 'true' : 'false',
           'USE_NODE_PORT_FOR_INGRESS' => $this->useNodePortForIngress ? 'true' : 'false',
           'INGRESS_CLASS' => $this->ingressClass,
        ];
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc)
    {
        $self = new self($assoc['PROVIDER_NAME']);
        $self->certManager = $assoc['CERT_MANAGER'] === 'true';
        $self->useNodePortForIngress = $assoc['USE_NODE_PORT_FOR_INGRESS'] === 'true';
        $self->ingressClass = $assoc['INGRESS_CLASS'];
        return $self;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isCertManager(): bool
    {
        return $this->certManager;
    }

    /**
     * @param bool $certManager
     * @return void
     */
    public function setCertManager(bool $certManager): void
    {
        $this->certManager = $certManager;
    }

    /**
     * @return bool
     */
    public function isUseNodePortForIngress(): bool
    {
        return $this->useNodePortForIngress;
    }

    /**
     * @param bool $useNodePortForIngress
     * @return void
     */
    public function setUseNodePortForIngress(bool $useNodePortForIngress): void
    {
        $this->useNodePortForIngress = $useNodePortForIngress;
    }

    /**
     * @return string
     */
    public function getIngressClass(): string
    {
        return $this->ingressClass;
    }

    /**
     * @param string $ingressClass
     * @return void
     */
    public function setIngressClass(string $ingressClass): void
    {
        $this->ingressClass = $ingressClass;
    }
}
