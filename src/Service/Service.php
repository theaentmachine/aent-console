<?php

namespace TheAentMachine\Service;

use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Validator;
use TheAentMachine\Service\Exception\ServiceException;

class Service implements \JsonSerializable
{
    /** @var string */
    private $serviceName = '';
    /** @var string */
    private $image = '';
    /** @var int[] */
    private $internalPorts = [];
    /** @var string[] */
    private $dependsOn = [];
    /** @var mixed[] */
    private $ports = [];
    /** @var mixed[] */
    private $labels = [];
    /** @var mixed[] */
    private $environment = [];
    /** @var mixed[] */
    private $volumes = [];
    /** @var \stdClass */
    private $validatorSchema;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->validatorSchema = json_decode(file_get_contents(__DIR__ . '/ServiceJsonSchema.json'), false);
    }

    /**
     * @param mixed[] $payload
     * @return Service
     * @throws ServiceException
     */
    public static function parsePayload(array $payload): Service
    {
        $service = new Service();
        $service->checkValidity($payload);
        $service->serviceName = $payload['serviceName'] ?? '';
        $s = $payload['service'] ?? array();
        if (!empty($s)) {
            $service->image = $s['image'] ?? '';
            $service->internalPorts = $s['internalPorts'] ?? array();
            $service->dependsOn = $s['dependsOn'] ?? array();
            $service->ports = $s['ports'] ?? array();
            $service->labels = $s['labels'] ?? array();
            $service->environment = $s['environment'] ?? array();
            $service->volumes = $s['volumes'] ?? array();
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
        $array = self::arrayFilterRec(array(
            'serviceName' => $this->serviceName,
            'service' => array(
                'image' => $this->image,
                'internalPorts' => $this->internalPorts,
                'dependsOn' => $this->dependsOn,
                'ports' => $this->ports,
                'labels' => $this->labels,
                'environment' => $this->environment,
                'volumes' => $this->volumes,
            )
        ));
        $this->checkValidity($array);
        return $array;
    }

    /**
     * @param \stdClass|array|string $data
     * @return bool
     * @throws ServiceException
     */
    private function checkValidity($data): bool
    {
        if (\is_array($data)) {
            $data = json_decode(json_encode($data), false);
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

    /**
     * Delete all key/value pairs with empty value by recursively using array_filter
     * @param array $input
     * @return mixed[] array
     */
    private static function arrayFilterRec(array $input): array
    {
        foreach ($input as &$value) {
            if (\is_array($value)) {
                $value = self::arrayFilterRec($value);
            }
        }
        return array_filter($input);
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @return int[]
     */
    public function getInternalPorts(): array
    {
        return $this->internalPorts;
    }

    /**
     * @return string[]
     */
    public function getDependsOn(): array
    {
        return $this->dependsOn;
    }

    /**
     * @return mixed[]
     */
    public function getPorts(): array
    {
        return $this->ports;
    }

    /**
     * @return mixed[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @return mixed[]
     */
    public function getEnvironment(): array
    {
        return $this->environment;
    }

    /**
     * @return mixed[]
     */
    public function getVolumes(): array
    {
        return $this->volumes;
    }

    /**
     * @param string $serviceName
     */
    public function setServiceName(string $serviceName): void
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @param int[] $internalPorts
     */
    public function setInternalPorts(array $internalPorts): void
    {
        $this->internalPorts = $internalPorts;
    }

    /**
     * @param string[] $dependsOn
     */
    public function setDependsOn(array $dependsOn): void
    {
        $this->dependsOn = $dependsOn;
    }

    /**
     * @param int $internalPort
     */
    public function addInternalPort(int $internalPort): void
    {
        $this->internalPorts[] = $internalPort;
    }

    /**
     * @param string $dependsOn
     */
    public function addDependsOn(string $dependsOn): void
    {
        $this->dependsOn[] = $dependsOn;
    }

    /**
     * @param string $source
     * @param string $target
     */
    public function addPort(string $source, string $target): void
    {
        $this->ports[] = array(
            'source' => $source,
            'target' => $target,
        );
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addLabel(string $key, string $value): void
    {
        $this->labels[] = array(
            'key' => $key,
            'values' => $value,
        );
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addEnvironment(string $key, string $value): void
    {
        $this->environment[] = array(
            'key' => $key,
            'values' => $value,
        );
    }

    /**
     * @param string $type
     * @param string $source
     * @param string $target
     * @param bool|null $readONly
     */
    public function addVolume(string $type, string $source, string $target, ?bool $readONly): void
    {
        $this->volumes[] = array(
            'type' => $type,
            'source' => $source,
            'target' => $target,
            'readOnly' => $readONly,
        );
    }
}
