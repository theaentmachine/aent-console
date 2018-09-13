<?php

namespace TheAentMachine\Prompt\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;

final class ValidatorHelper
{
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
            $message = !empty($errorMessage) ? $errorMessage : 'Value "' . $response . '" is invalid';
            if ($func($response)) {
                throw new InvalidArgumentException($message);
            }
            return $response;
        };
    }

    /**
     * @return callable
     */
    public static function getAlphaValidator(): callable
    {
        return function (string $response) {
            $response = \trim($response);
            $pattern = '/^[a-zA-Z0-9]+$/';
            if (!\preg_match($pattern, $response)) {
                $message = 'Value "' . $response . '" is invalid';
                $message .= "\nHint: only alphanumerical characters are allowed";
                throw new InvalidArgumentException($message);
            }
            return $response;
        };
    }
}
