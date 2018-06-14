<?php

namespace TheAentMachine\Service\Exception;

use Opis\JsonSchema\ValidationError;

class ServiceException extends \Exception
{
    /**
     * @param ValidationError $vError
     * @return ServiceException
     */
    public static function invalidServiceData(ValidationError $vError): ServiceException
    {
        $massage = 'Invalid service data' . PHP_EOL
            . 'Error: ' . $vError->keyword() . PHP_EOL
            . json_encode($vError->keywordArgs(), JSON_PRETTY_PRINT);
        return new self($massage);
    }
}
