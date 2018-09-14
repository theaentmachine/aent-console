<?php

namespace TheAentMachine\Prompt\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;

final class ValidatorHelper
{
    private const defaultErrorMessage = 'Value "%s" is invalid';

    /**
     * @param callable|null $v1
     * @param callable|null $v2
     * @return callable
     */
    public static function merge(?callable $v1, ?callable $v2): callable
    {
        return function (?string $response) use ($v1, $v2) {
            if (!empty($v1)) {
                $response = $v1($response);
            }
            if (!empty($v2)) {
                $response = $v2($response);
            }
            return $response;
        };
    }

    /**
     * @param callable $func
     * @param null|string $errorMessage
     * @return callable
     */
    public static function getFuncShouldNotReturnTrueValidator(callable $func, ?string $errorMessage = null): callable
    {
        return function (string $response) use ($func, $errorMessage) {
            $response = \trim($response);
            $message = \sprintf((!empty($errorMessage) ? $errorMessage : self::defaultErrorMessage), $response);
            if ($func($response)) {
                throw new InvalidArgumentException($message);
            }
            return $response;
        };
    }

    /**
     * @param null|string $errorMessage
     * @return callable
     */
    public static function getAlphaValidator(?string $errorMessage = null): callable
    {
        return function (string $response) use ($errorMessage) {
            $response = \trim($response);
            $pattern = '/^[a-zA-Z0-9]+$/';
            if (!\preg_match($pattern, $response)) {
                $message = \sprintf((!empty($errorMessage) ? $errorMessage : self::defaultErrorMessage . '. Hint: only alphanumerical characters are allowed'), $response);
                throw new InvalidArgumentException($message);
            }
            return $response;
        };
    }

    /**
     * @param null|string $errorMessage
     * @return callable
     */
    public static function getDomainNameValidator(?string $errorMessage = null): callable
    {
        return function (string $response) use ($errorMessage) {
            $response = trim($response);
            if (!\preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/im', $response)) {
                $message = \sprintf((!empty($errorMessage) ? $errorMessage : self::defaultErrorMessage . '. Hint: the domain name must not start with "http(s)://".'), $response);
                throw new InvalidArgumentException($message);
            }
            return $response;
        };
    }

    /**
     * @return callable
     */
    public static function getDockerImageWithoutTagValidator(): callable
    {
        return function (string $response) {
            $response = \trim($response);
            if (!\preg_match('/^[a-z0-9]+\/([a-z0-9]+(?:[._-][a-z0-9]+)*)$/', $response)) {
                throw new InvalidArgumentException(self::defaultErrorMessage . '. Hint: the docker image should be of type "username/repository"');
            }
            return $response;
        };
    }
}
