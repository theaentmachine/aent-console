<?php

namespace TheAentMachine\Aenthill;

final class CommonMetadata
{
    public const ENV_NAME_KEY = 'ENV_NAME';
    public const ENV_TYPE_KEY = 'ENV_TYPE';
    public const DOCKER_COMPOSE_FILENAME_KEY = 'DOCKER_COMPOSE_FILENAME';
    public const KUBERNETES_DIRNAME_KEY = 'KUBERNETES_DIRNAME';
    public const DOCKERFILE_NAME_KEY = 'DOCKERFILE_NAME';
    public const SINGLE_ENVIRONMENT_KEY = 'SINGLE_ENVIRONMENT';

    // common values...
    public const ENV_TYPE_DEV = 'DEV';
    public const ENV_TYPE_TEST = 'TEST';
    public const ENV_TYPE_PROD = 'PROD';
}
