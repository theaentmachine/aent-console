<?php

namespace TheAentMachine\Aenthill;

class Metadata
{
    public const ENV_NAME_KEY = 'ENV_NAME';
    public const ENV_TYPE_KEY = 'ENV_TYPE';

    public const ENV_TYPE_DEV = 'DEV';
    public const ENV_TYPE_TEST = 'TEST';
    public const ENV_TYPE_PROD = 'PROD';

    public const CI_KEY = 'CI';
    public const REVERSE_PROXY_KEY = 'REVERSE_PROXY';
    public const IMAGE_BUILDER_KEY = 'IMAGE_BUILDER';

    public const DOCKER_COMPOSE_FILENAME_KEY = 'DOCKER_COMPOSE_FILENAME';
}
