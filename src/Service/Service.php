<?php

namespace TheAentMachine\Service;

use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Validator;
use TheAentMachine\Aenthill\CommonMetadata;
use TheAentMachine\Aenthill\Manifest;
use TheAentMachine\Service\Enum\EnvVariableTypeEnum;
use TheAentMachine\Service\Enum\VolumeTypeEnum;
use TheAentMachine\Service\Environment\EnvVariable;
use TheAentMachine\Service\Environment\SharedEnvVariable;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Volume\BindVolume;
use TheAentMachine\Service\Volume\NamedVolume;
use TheAentMachine\Service\Volume\TmpfsVolume;
use TheAentMachine\Service\Volume\Volume;
use TheAentMachine\Yaml\CommentedItem;

class Service implements \JsonSerializable
{
    /** @var string */
    private $serviceName = '';
    /** @var string|null */
    private $image;
    /** @var string[] */
    private $command = [];
    /** @var int[] */
    private $internalPorts = [];
    /** @var string[] */
    private $dependsOn = [];
    /** @var array<int, array<string, string|int>> */
    private $ports = [];
    /** @var array<string, CommentedItem> */
    private $labels = [];
    /** @var array<string, EnvVariable> */
    private $environment = [];
    /** @var mixed[] */
    private $volumes = [];
    /** @var array<int, array<string, string|int>> */
    private $virtualHosts = [];
    /** @var null|bool */
    private $needBuild;
    /** @var null|bool */
    private $isVariableEnvironment;
    /** @var \stdClass */
    private $validatorSchema;
    /** @var string[] */
    private $dockerfileCommands = [];
    /** @var null|string */
    private $requestMemory;
    /** @var null|string */
    private $requestCpu;
    /** @var null|string */
    private $limitMemory;
    /** @var null|string */
    private $limitCpu;
    /** @var string[] */
    private $destEnvTypes = []; // empty === all env types

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->validatorSchema = \GuzzleHttp\json_decode((string)file_get_contents(__DIR__ . '/ServiceJsonSchema.json'), false);
    }

    /**
     * @param mixed[] $payload
     * @return Service
     * @throws ServiceException
     */
    public static function parsePayload(array $payload): Service
    {
        $service = new self();
        $service->checkValidity($payload);
        $service->serviceName = $payload['serviceName'] ?? '';
        if (!empty($s = $payload['service'] ?? [])) {
            $service->image = $s['image'] ?? null;
            $service->command = $s['command'] ?? [];
            $service->internalPorts = $s['internalPorts'] ?? [];
            $service->dependsOn = $s['dependsOn'] ?? [];
            $service->ports = $s['ports'] ?? [];
            $labels = $s['labels'] ?? [];
            foreach ($labels as $key => $label) {
                $service->addLabel($key, $label['value'], $label['comment'] ?? null);
            }
            $environment = $s['environment'] ?? [];
            foreach ($environment as $key => $env) {
                $service->addEnvVar($key, $env['value'], $env['type'], $env['comment'] ?? null, $env['containerId'] ?? null);
            }
            $volumes = $s['volumes'] ?? [];
            foreach ($volumes as $vol) {
                $service->addVolume(
                    $vol['type'],
                    $vol['source'],
                    $vol['comment'] ?? null,
                    $vol['target'] ?? '',
                    $vol['readOnly'] ?? false,
                    $vol['requestStorage'] ?? null
                );
            }
            $service->virtualHosts = $s['virtualHosts'] ?? [];
            $service->needBuild = $s['needBuild'] ?? null;
            $service->isVariableEnvironment = $s['isVariableEnvironment'] ?? null;
        }
        $service->dockerfileCommands = $payload['dockerfileCommands'] ?? [];
        $service->destEnvTypes = $payload['destEnvTypes'] ?? [];

        $resources = $payload['resources'] ?? [];
        if (isset($resources['requests'])) {
            $r = $resources['requests'];
            $service->requestMemory = $r['memory'] ?? null;
            $service->requestCpu = $r['cpu'] ?? null;
        }
        if (isset($resources['limits'])) {
            $l = $resources['limits'];
            $service->limitMemory = $l['memory'] ?? null;
            $service->limitCpu = $l['cpu'] ?? null;
        }

        return $service;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     * @throws ServiceException
     */
    public function jsonSerialize(): array
    {
        $labelMap = function (CommentedItem $commentedItem): array {
            return null === $commentedItem->getComment() ?
                ['value' => $commentedItem->getItem()] :
                ['value' => $commentedItem->getItem(), 'comment' => $commentedItem->getComment()];
        };

        $jsonSerializeMap = function (\JsonSerializable $obj): array {
            return $obj->jsonSerialize();
        };

        $json = array(
            'serviceName' => $this->serviceName,
        );

        $service = array_filter([
            'image' => $this->image,
            'command' => $this->command,
            'internalPorts' => $this->internalPorts,
            'dependsOn' => $this->dependsOn,
            'ports' => $this->ports,
            'labels' => array_map($labelMap, $this->labels),
            'environment' => array_map($jsonSerializeMap, $this->environment),
            'volumes' => array_map($jsonSerializeMap, $this->volumes),
            'virtualHosts' => $this->virtualHosts,
            'needBuild' => $this->needBuild,
            'isVariableEnvironment' => $this->isVariableEnvironment,
        ]);

        if (!empty($service)) {
            $json['service'] = $service;
        }

        if (!empty($this->dockerfileCommands)) {
            $json['dockerfileCommands'] = $this->dockerfileCommands;
        }

        $json['destEnvTypes'] = $this->destEnvTypes;

        $resources = array_filter([
            'requests' => array_filter([
                'memory' => $this->requestMemory,
                'cpu' => $this->requestCpu,
            ], function ($v) {
                return null !== $v;
            }),
            'limits' => array_filter([
                'memory' => $this->limitMemory,
                'cpu' => $this->limitCpu,
            ], function ($v) {
                return null !== $v;
            }),
        ]);

        if (!empty($resources)) {
            $json['resources'] = $resources;
        }

        $this->checkValidity($json);
        return $json;
    }

    /** @return mixed[] */
    public function imageJsonSerialize(): array
    {
        $dockerfileCommands = [];
        $dockerfileCommands[] = 'FROM ' . $this->image;
        foreach ($this->environment as $key => $env) {
            if ($env->getType() === EnvVariableTypeEnum::IMAGE_ENV_VARIABLE) {
                $dockerfileCommands[] = "ENV $key" . '=' . $env->getValue();
            }
        }
        foreach ($this->volumes as $volume) {
            if ($volume->getType() === VolumeTypeEnum::BIND_VOLUME) {
                $dockerfileCommands[] = 'COPY ' . $volume->getSource() . ' ' . $volume->getTarget();
            }
        }

        if (!empty($this->command)) {
            $dockerfileCommands[] = 'CMD ' . implode(' ', $this->command);
        }

        $dockerfileCommands = array_merge($dockerfileCommands, $this->dockerfileCommands);

        return [
            'serviceName' => $this->serviceName,
            'dockerfileCommands' => $dockerfileCommands,
            'destEnvTypes' => $this->destEnvTypes,
        ];
    }

    /**
     * @param \stdClass|array|string $data
     * @return bool
     * @throws ServiceException
     */
    private function checkValidity($data): bool
    {
        if (\is_array($data)) {
            $data = \GuzzleHttp\json_decode(\GuzzleHttp\json_encode($data), false);
        }
        $validator = new Validator();
        $result = $validator->dataValidation($data, $this->validatorSchema);
        if (!$result->isValid()) {
            /** @var ValidationError $vError */
            $vError = $result->getFirstError();
            throw ServiceException::invalidServiceData($vError);
        }
        return $result->isValid();
    }


    /************************ getters **********************/

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    /** @return string[] */
    public function getCommand(): array
    {
        return $this->command;
    }

    /** @return int[] */
    public function getInternalPorts(): array
    {
        return $this->internalPorts;
    }

    /** @return string[] */
    public function getDependsOn(): array
    {
        return $this->dependsOn;
    }

    /** @return array<int, array<string, string|int>> */
    public function getPorts(): array
    {
        return $this->ports;
    }

    /** @return array<string, CommentedItem> */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /** @return array<string, EnvVariable> */
    public function getEnvironment(): array
    {
        return $this->environment;
    }

    /** @return mixed[] */
    public function getVolumes(): array
    {
        return $this->volumes;
    }

    /** @return array<int, array<string, string|int>> */
    public function getVirtualHosts(): array
    {
        return $this->virtualHosts;
    }

    public function getNeedBuild(): ?bool
    {
        return $this->needBuild;
    }

    public function isVariableEnvironment(): ?bool
    {
        return $this->isVariableEnvironment;
    }

    /** @return string[] */
    public function getDockerfileCommands(): array
    {
        return $this->dockerfileCommands;
    }

    public function getRequestMemory(): ?string
    {
        return $this->requestMemory;
    }

    public function getRequestCpu(): ?string
    {
        return $this->requestCpu;
    }

    public function getLimitMemory(): ?string
    {
        return $this->limitMemory;
    }

    public function getLimitCpu(): ?string
    {
        return $this->limitCpu;
    }

    /**  @return string[] */
    public function getDestEnvTypes(): array
    {
        return $this->destEnvTypes;
    }


    /************************ setters **********************/

    public function setServiceName(string $serviceName): void
    {
        $this->serviceName = $serviceName;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /** @param string[] $command */
    public function setCommand(array $command): void
    {
        $this->command = $command;
    }

    /** @param int[] $internalPorts */
    public function setInternalPorts(array $internalPorts): void
    {
        $this->internalPorts = $internalPorts;
    }

    /** @param string[] $dependsOn */
    public function setDependsOn(array $dependsOn): void
    {
        $this->dependsOn = $dependsOn;
    }

    public function setRequestMemory(string $requestMemory): void
    {
        $this->requestMemory = $requestMemory;
    }

    public function setRequestCpu(string $requestCpu): void
    {
        $this->requestCpu = $requestCpu;
    }
    public function setLimitMemory(string $limitMemory): void
    {
        $this->limitMemory = $limitMemory;
    }

    public function setLimitCpu(string $limitCpu): void
    {
        $this->limitCpu = $limitCpu;
    }

    public function setNeedBuild(?bool $needBuild): void
    {
        $this->needBuild = $needBuild;
    }

    public function setIsVariableEnvironment(bool $isVariableEnvironment): void
    {
        $this->isVariableEnvironment = $isVariableEnvironment;
    }

    /************************ adders **********************/

    public function addCommand(string $command): void
    {
        $this->command[] = $command;
    }

    public function addInternalPort(int $internalPort): void
    {
        $this->internalPorts[] = $internalPort;
    }

    public function addDependsOn(string $dependsOn): void
    {
        $this->dependsOn[] = $dependsOn;
    }

    public function addPort(int $source, int $target, ?string $comment = null): void
    {
        $this->ports[] = array_filter([
            'source' => $source,
            'target' => $target,
            'comment' => $comment,
        ], function ($v) {
            return null !== $v;
        });
    }

    public function addLabel(string $key, string $value, ?string $comment = null): void
    {
        $this->labels[$key] = new CommentedItem($value, $comment);
    }

    public function addVirtualHost(?string $host, int $port, ?string $comment = null): void
    {
        $array = [];
        if (null !== $host && '' !== $host) {
            $array['host'] = $host;
        }
        $array['port'] = $port;
        if (null !== $comment && '' !== $comment) {
            $array['comment'] = $comment;
        }
        $this->virtualHosts[] = $array;
    }

    public function addVirtualHostPrefix(?string $hostPrefix, int $port, ?string $comment = null): void
    {
        $array = [];
        if (null !== $hostPrefix && '' !== $hostPrefix) {
            $array['hostPrefix'] = $hostPrefix;
        }
        $array['port'] = $port;
        if (null !== $comment && '' !== $comment) {
            $array['comment'] = $comment;
        }
        $this->virtualHosts[] = $array;
    }

    /************************ environment adders & getters by type **********************/

    /** @throws ServiceException */
    private function addEnvVar(string $key, string $value, string $type, ?string $comment = null, ?string $containerId = null): void
    {
        switch ($type) {
            case EnvVariableTypeEnum::SHARED_ENV_VARIABLE:
                $this->addSharedEnvVariable($key, $value, $comment, $containerId);
                break;
            case EnvVariableTypeEnum::SHARED_SECRET:
                $this->addSharedSecret($key, $value, $comment, $containerId);
                break;
            case EnvVariableTypeEnum::IMAGE_ENV_VARIABLE:
                $this->addImageEnvVariable($key, $value, $comment);
                break;
            case EnvVariableTypeEnum::CONTAINER_ENV_VARIABLE:
                $this->addContainerEnvVariable($key, $value, $comment);
                break;
            default:
                throw ServiceException::unknownEnvVariableType($type);
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @param null|string $comment
     * @param null|string $containerId A string representing the ID of the container of environment variables. Typically the name of the env_file in docker_compose or the name of the ConfigMap in Kubernetes
     */
    public function addSharedEnvVariable(string $key, string $value, ?string $comment = null, ?string $containerId = null): void
    {
        $this->environment[$key] = new SharedEnvVariable($value, EnvVariableTypeEnum::SHARED_ENV_VARIABLE, $comment, $containerId);
    }

    /**
     * @param string $key
     * @param string $value
     * @param null|string $comment
     * @param null|string $containerId A string representing the ID of the container of environment variables. Typically the name of the env_file in docker_compose or the name of the Secret in Kubernetes
     */
    public function addSharedSecret(string $key, string $value, ?string $comment = null, ?string $containerId = null): void
    {
        $this->environment[$key] = new SharedEnvVariable($value, EnvVariableTypeEnum::SHARED_SECRET, $comment, $containerId);
    }

    public function addImageEnvVariable(string $key, string $value, ?string $comment = null): void
    {
        $this->environment[$key] = new EnvVariable($value, EnvVariableTypeEnum::IMAGE_ENV_VARIABLE, $comment);
    }

    public function addContainerEnvVariable(string $key, string $value, ?string $comment = null): void
    {
        $this->environment[$key] = new EnvVariable($value, EnvVariableTypeEnum::CONTAINER_ENV_VARIABLE, $comment);
    }

    /** @return array<string, EnvVariable> */
    private function getAllEnvVariablesByType(string $type): array
    {
        $res = [];
        /**
         * @var string $key
         * @var EnvVariable $envVar
         */
        foreach ($this->environment as $key => $envVar) {
            if ($envVar->getType() === $type) {
                $res[$key] = $envVar;
            }
        }
        return $res;
    }

    /** @return array<string,SharedEnvVariable> */
    public function getAllSharedEnvVariable(): array
    {
        return $this->getAllEnvVariablesByType(EnvVariableTypeEnum::SHARED_ENV_VARIABLE);
    }

    /** @return array<string,SharedEnvVariable> */
    public function getAllSharedSecret(): array
    {
        return $this->getAllEnvVariablesByType(EnvVariableTypeEnum::SHARED_SECRET);
    }

    /** @return array<string, EnvVariable> */
    public function getAllImageEnvVariable(): array
    {
        return $this->getAllEnvVariablesByType(EnvVariableTypeEnum::IMAGE_ENV_VARIABLE);
    }

    /** @return array<string, EnvVariable> */
    public function getAllContainerEnvVariable(): array
    {
        return $this->getAllEnvVariablesByType(EnvVariableTypeEnum::CONTAINER_ENV_VARIABLE);
    }


    /************************ volumes adders & removers **********************/

    /** @throws ServiceException */
    private function addVolume(string $type, string $source, ?string $comment = null, string $target = '', bool $readOnly = false, ?string $requestStorage = null): void
    {
        switch ($type) {
            case VolumeTypeEnum::NAMED_VOLUME:
                $this->addNamedVolume($source, $target, $readOnly, $comment, $requestStorage);
                break;
            case VolumeTypeEnum::BIND_VOLUME:
                $this->addBindVolume($source, $target, $readOnly, $comment);
                break;
            case VolumeTypeEnum::TMPFS_VOLUME:
                $this->addTmpfsVolume($source, $comment);
                break;
            default:
                throw ServiceException::unknownVolumeType($type);
        }
    }

    public function addNamedVolume(string $source, string $target, bool $readOnly = false, ?string $comment = null, string $requestStorage = null): void
    {
        $this->volumes[] = new NamedVolume($source, $target, $readOnly, $comment, $requestStorage);
    }

    public function addBindVolume(string $source, string $target, bool $readOnly = false, ?string $comment = null): void
    {
        $this->volumes[] = new BindVolume($source, $target, $readOnly, $comment);
    }

    public function addTmpfsVolume(string $source, ?string $comment = null): void
    {
        $this->volumes[] = new TmpfsVolume($source, $comment);
    }

    public function addDockerfileCommand(string $dockerfileCommand): void
    {
        $this->dockerfileCommands[] = $dockerfileCommand;
    }

    private function removeVolumesByType(string $type): void
    {
        $filterFunction = function (Volume $vol) use ($type) {
            return $vol->getType() !== $type;
        };
        $this->volumes = array_values(array_filter($this->volumes, $filterFunction));
    }

    public function removeAllBindVolumes(): void
    {
        $this->removeVolumesByType(VolumeTypeEnum::BIND_VOLUME);
    }

    public function removeAllNamedVolumes(): void
    {
        $this->removeVolumesByType(VolumeTypeEnum::NAMED_VOLUME);
    }

    public function removeAllTmpfsVolumes(): void
    {
        $this->removeVolumesByType(VolumeTypeEnum::TMPFS_VOLUME);
    }

    public function removeVolumesBySource(string $source): void
    {
        $filterFunction = function (Volume $vol) use ($source) {
            return $vol->getSource() !== $source;
        };
        $this->volumes = array_values(array_filter($this->volumes, $filterFunction));
    }

    /************************ destEnvTypes stuffs **********************/

    public function addDestEnvType(string $envType, bool $keepTheOtherEnvTypes = true): void
    {
        if (!$keepTheOtherEnvTypes) {
            $this->destEnvTypes = [];
        }
        $this->destEnvTypes[] = $envType;
    }

    public function isForDevEnvType(): bool
    {
        return empty($this->destEnvTypes) || \in_array(CommonMetadata::ENV_TYPE_DEV, $this->destEnvTypes);
    }

    public function isForTestEnvType(): bool
    {
        return empty($this->destEnvTypes) || \in_array(CommonMetadata::ENV_TYPE_TEST, $this->destEnvTypes);
    }

    public function isForProdEnvType(): bool
    {
        return empty($this->destEnvTypes) || \in_array(CommonMetadata::ENV_TYPE_PROD, $this->destEnvTypes);
    }

    public function isForMyEnvType(): bool
    {
        $myEnvType = Manifest::getMetadata(CommonMetadata::ENV_TYPE_KEY);
        return empty($this->destEnvTypes) || \in_array($myEnvType, $this->destEnvTypes, true);
    }
}
