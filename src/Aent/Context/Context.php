<?php

namespace TheAentMachine\Aent\Context;

use TheAentMachine\Aent\Payload\JsonPayloadInterface;
use TheAentMachine\Aenthill\Aenthill;

class Context implements JsonPayloadInterface, ContextInterface
{
    public const DEV = 'development';
    public const TEST = 'test';
    public const PROD = 'production';

    /** @var string */
    protected $type;

    /** @var string */
    private $name;

    /** @var string */
    private $baseVirtualHost;

    /**
     * Environment constructor.
     * @param string $type
     * @param string $name
     * @param string $baseVirtualHost
     */
    public function __construct(string $type, string $name, string $baseVirtualHost)
    {
        $this->type = $type;
        $this->name = $name;
        $this->baseVirtualHost = $baseVirtualHost;
    }

    /**
     * @return string[]
     */
    public static function getList(): array
    {
        return [
            self::DEV,
            self::TEST,
            self::PROD,
        ];
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'baseVirtualHost' => $this->baseVirtualHost,
        ];
    }

    /**
     * @param array<string,string> $assoc
     * @return self
     */
    public static function fromArray(array $assoc): self
    {
        $type = $assoc['type'];
        $name = $assoc['name'];
        $baseVirtualHost = $assoc['baseVirtualHost'];
        return new self($type, $name, $baseVirtualHost);
    }

    /**
     * @return void
     */
    public function toMetadata(): void
    {
        Aenthill::update([
            'ENVIRONMENT_TYPE' => $this->type,
            'ENVIRONMENT_NAME' => $this->name,
            'BASE_VIRTUAL_HOST' => $this->baseVirtualHost,
        ]);
    }

    /**
     * @return mixed
     */
    public static function fromMetadata()
    {
        $type = Aenthill::metadata('ENVIRONMENT_TYPE');
        $name = Aenthill::metadata('ENVIRONMENT_NAME');
        $baseVirtualHost = Aenthill::metadata('BASE_VIRTUAL_HOST');
        return new self($type, $name, $baseVirtualHost);
    }

    /**
     * @return bool
     */
    public function isDevelopment(): bool
    {
        return $this->type === self::DEV;
    }

    /**
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->type === self::TEST;
    }

    /**
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->type === self::PROD;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     */
    public function setBaseVirtualHost(string $baseVirtualHost): void
    {
        $this->baseVirtualHost = $baseVirtualHost;
    }
}
