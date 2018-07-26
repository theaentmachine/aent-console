<?php

namespace TheAentMachine\Aenthill;

use TheAentMachine\Exception\CommonAentsException;

final class CommonAents
{
    // TODO handle version
    private static $orchestratorAents = [
        'theaentmachine/aent-docker-compose',
        'theaentmachine/aent-kubernetes'
    ];

    private static $reverseProxyAents = [
        'theaentmachine/aent-traefik'
    ];

    private static $CIAents = [
        'theaentmachine/aent-gitlabci'
    ];

    private static $imageBuilderAents = [
        'theaentmachine/aent-dockerfile'
    ];

    /**
     * @param string $key
     * @return string[]
     * @throws CommonAentsException
     */
    public static function getAentsListByDependencyKey(string $key): array
    {
        switch ($key) {
            case CommonDependencies::ORCHESTRATOR_KEY:
                return self::$orchestratorAents;
            case CommonDependencies::REVERSE_PROXY_KEY:
                return self::$reverseProxyAents;
            case CommonDependencies::CI_KEY:
                return self::$CIAents;
            case CommonDependencies::IMAGE_BUILDER_KEY:
                return self::$imageBuilderAents;
            default:
                throw CommonAentsException::noAentsAvailable($key);
        }
    }
}
