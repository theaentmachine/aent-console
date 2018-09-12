<?php

namespace TheAentMachine\Prompt\Helper;

final class ValidatorHelper
{
    /**
     * @param callable|null $v1
     * @param callable|null $v2
     * @return callable|null
     */
    public static function merge(?callable $v1, ?callable $v2): ?callable
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
}
