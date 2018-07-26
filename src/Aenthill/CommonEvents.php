<?php

namespace TheAentMachine\Aenthill;

use TheAentMachine\Service\Service;

final class CommonEvents
{
    public const ADD_EVENT = 'ADD';
    public const START_EVENT = 'START';
    public const REPLY_EVENT = 'REPLY';
    public const ENVIRONMENT_EVENT = 'ENVIRONMENT';
    public const NEW_SERVICE_EVENT = 'NEW_SERVICE';
    public const NEW_VIRTUAL_HOST_EVENT = 'NEW_VIRTUAL_HOST';
    public const NEW_IMAGE_EVENT = 'NEW_IMAGE';
    public const NEW_BUILD_IMAGE_EVENT = 'NEW_BUILD_IMAGE';
    public const NEW_DEPLOY_JOB_DOCKER_COMPOSE_EVENT = 'NEW_DEPLOY_JOB_DOCKER_COMPOSE';
    public const NEW_DEPLOY_JOB_KUBERNETES_EVENT = 'NEW_DEPLOY_JOB_KUBERNETES';

    public static function dispatchService(Service $service): void
    {
        Aenthill::dispatchJson(self::NEW_SERVICE_EVENT, $service);
    }

    /**
     * @param string $serviceName
     * @param int $virtualPort
     * @param string|null $virtualHost
     * @return mixed[]|null
     */
    public static function dispatchNewVirtualHost(string $serviceName, int $virtualPort = 80, string $virtualHost = null): ?array
    {
        $message = [
            'service' => $serviceName,
            'virtualPort' => $virtualPort
        ];
        if ($virtualHost !== null) {
            $message['virtualHost'] = $virtualHost;
        }

        return Aenthill::dispatchJson(self::NEW_VIRTUAL_HOST_EVENT, $message);
    }

    public static function dispatchImage(Service $service): void
    {
        Aenthill::dispatchJson(self::NEW_IMAGE_EVENT, $service->imageJsonSerialize());
    }
}
