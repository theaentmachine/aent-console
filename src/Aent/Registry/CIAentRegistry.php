<?php

namespace TheAentMachine\Aent\Registry;

final class CIAentRegistry extends AbstractAentRegistry
{
    public const GITLAB = 'GitLab';

    /** @var array<string,string> */
    protected static $aents = [
        self::GITLAB => 'theaentmachine/aent-gitlabci',
    ];
}
