<?php

namespace TheAentMachine\Aent\Registry;

final class OrchestratorAentRegistry extends AbstractAentRegistry
{
    public const DOCKER_COMPOSE = 'Docker Compose';
    public const KUBERNETES = 'Kubernetes';

    /** @var array<string,string> */
    protected static $aents = [
        self::DOCKER_COMPOSE => 'theaentmachine/aent-docker-compose',
        self::KUBERNETES => 'theaentmachine/aent-kubernetes',
    ];
}
