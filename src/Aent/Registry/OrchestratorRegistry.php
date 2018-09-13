<?php

namespace TheAentMachine\Aent\Registry;

final class OrchestratorRegistry extends AbstractRegistry
{
    /** @var array<string,string> */
    private static $orchestrators = [
        'Docker Compose' => 'theaentmachine/aent-docker-compose',
        'Kubernetes' => 'theaentmachine/aent-kubernetes',
    ];

    /**
     * @return array<string,string>
     */
    public static function getList(): array
    {
        return self::$orchestrators;
    }
}
