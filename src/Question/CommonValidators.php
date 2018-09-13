<?php

namespace TheAentMachine\Question;

use function Safe\preg_match;

final class CommonValidators
{
    /**
     * @param string[]|null $additionalCharacters
     * @param null|string $hint
     * @return \Closure
     */
    public static function getAlphaValidator(?array $additionalCharacters = null, ?string $hint = null): \Closure
    {
        return function (string $value) use ($additionalCharacters, $hint) {
            $value = trim($value);
            $pattern = '/^[a-zA-Z0-9';

            if (!empty($additionalCharacters)) {
                foreach ($additionalCharacters as $character) {
                    $pattern .= $character;
                }
            }

            $pattern .= ']+$/';

            if (!preg_match($pattern, $value)) {
                $message = 'Invalid value "' . $value . '".';
                if (!empty($hint)) {
                    $message .= " Hint: $hint";
                }
                throw new \InvalidArgumentException($message);
            }

            return $value;
        };
    }

    public static function getAbsolutePathValidator(): \Closure
    {
        return function (string $value) {
            $value = trim($value);
            if (!preg_match('/^[\'"]?(?:\/[^\/\n]+)*[\'"]?$/', $value)) {
                throw new \InvalidArgumentException('Invalid value "' . $value . '". Hint: path has to be absolute without trailing "/".');
            }
            return $value;
        };
    }

    public static function getDomainNameValidator(): \Closure
    {
        return function (string $value) {
            $value = trim($value);
            if (!preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?$/im', $value)) {
                throw new \InvalidArgumentException('Invalid value "' . $value . '". Hint: the domain name must not start with "http(s)://".');
            }
            return $value;
        };
    }

    public static function getDomainNameWithPortValidator(): \Closure
    {
        return function (string $value) {
            $value = trim($value);
            if (!preg_match('/^(?!:\/\/)([a-zA-Z0-9-_]+\.)*[a-zA-Z0-9][a-zA-Z0-9-_]+\.[a-zA-Z]{2,11}?:\d*$/im', $value)) {
                throw new \InvalidArgumentException('Invalid value "' . $value . '". Hint: the domain name must not start with "http(s)://".');
            }
            return $value;
        };
    }

    public static function getIPv4Validator(): \Closure
    {
        return function (string $value) {
            $value = trim($value);
            if (!preg_match('/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/', $value)) {
                throw new \InvalidArgumentException('Invalid value "' . $value . '".');
            }
            return $value;
        };
    }

    public static function getDockerImageWithoutTagValidator(): \Closure
    {
        return function (string $value) {
            $value = trim($value);
            if (!preg_match('/^[a-z0-9]+\/([a-z0-9]+(?:[._-][a-z0-9]+)*)$/', $value)) {
                throw new \InvalidArgumentException('Invalid value "' . $value . '". Hint: the docker image should be of type "username/repository"');
            }
            return $value;
        };
    }

    public static function getDockerImageWithTagValidator(): \Closure
    {
        return function (string $value) {
            $value = trim($value);
            if (!preg_match('/^[a-z0-9]+\/([a-z0-9]+(?:[._-][a-z0-9]+)*)(:[\w]+([\w._-]+)?)$/', $value)) {
                throw new \InvalidArgumentException('Invalid value "' . $value . '". Hint: the docker image should be of type "username/repository:tag"');
            }
            return $value;
        };
    }
}
