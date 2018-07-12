<?php

namespace TheAentMachine\Service\Exception;

use Opis\JsonSchema\ValidationError;
use TheAentMachine\Service\Enum\EnvVariableTypeEnum;
use TheAentMachine\Service\Enum\VolumeTypeEnum;

class ServiceException extends \Exception
{
    public static function invalidServiceData(ValidationError $vError): ServiceException
    {
        $message = 'Invalid service data' . PHP_EOL
            . 'Error of type ' . $vError->keyword() . ' at ' . implode('->', $vError->dataPointer()) . PHP_EOL
            . json_encode($vError->keywordArgs(), JSON_PRETTY_PRINT);
        return new self($message);
    }

    public static function jsonSchemaNotFound(string $pathname): ServiceException
    {
        return new self("The Json Schema used for validate the Service is not found (at $pathname).");
    }

    public static function unknownVolumeType(string $volumeType): ServiceException
    {
        $message = 'Unknown service volume type: ' . $volumeType . PHP_EOL
            . 'Expected: ' . json_encode(VolumeTypeEnum::getVolumeTypes());
        return new self($message);
    }

    public static function unknownEnvVariableType(string $type): ServiceException
    {
        $message = 'Unknown environment variable type: ' . $type . PHP_EOL
            . 'Expected: ' . json_encode(EnvVariableTypeEnum::getEnvVariableTypes());
        return new self($message);
    }
}
