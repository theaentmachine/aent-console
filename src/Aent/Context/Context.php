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
    protected $environmentType;

    /** @var string */
    private $environmentName;

    /**
     * Environment constructor.
     * @param string $environmentType
     * @param string $environmentName
     */
    public function __construct(string $environmentType, string $environmentName)
    {
        $this->environmentType = $environmentType;
        $this->environmentName = $environmentName;
    }

    /**
     * @return string[]
     */
    public static function getEnvironmentTypeList(): array
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
            'ENVIRONMENT_TYPE' => $this->environmentType,
            'ENVIRONMENT_NAME' => $this->environmentName,
        ];
    }

    /**
     * @param array<string,string> $assoc
     * @return mixed
     */
    public static function fromArray(array $assoc)
    {
        $environmentType = $assoc['ENVIRONMENT_TYPE'];
        $environmentName = $assoc['ENVIRONMENT_NAME'];
        return new self($environmentType, $environmentName);
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
        $environmentType = Aenthill::metadata('ENVIRONMENT_TYPE');
        $environmentName = Aenthill::metadata('ENVIRONMENT_NAME');
        return new self($environmentType, $environmentName);
    }

    /**
     * @return bool
     */
    public function isDevelopment(): bool
    {
        return $this->environmentType === self::DEV;
    }

    /**
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->environmentType === self::TEST;
    }

    /**
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->environmentType === self::PROD;
    }

    /**
     * @return string
     */
    public function getEnvironmentType(): string
    {
        return $this->environmentType;
    }

    /**
     * @return string
     */
    public function getEnvironmentName(): string
    {
        return $this->environmentName;
    }
}
