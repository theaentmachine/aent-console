<?php

namespace TheAentMachine\Aent\Registry;

final class CIRegistry extends AbstractRegistry
{
    /** @var array<string,string> */
    private static $ci = [
        'GitLab' => 'theaentmachine/aent-gitlabci',
    ];

    /**
     * @return array<string,string>
     */
    public static function getList(): array
    {
        return self::$ci;
    }
}
