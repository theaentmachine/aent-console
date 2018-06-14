<?php

namespace TheAentMachine\Service;

use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Validator;
use TheAentMachine\Service\Exception\ServiceException;

class Service implements \JsonSerializable
{
    /** @var string */
    protected $serviceName = '';
    /** @var string */
    protected $image = '';
    /** @var int[] */
    protected $internalPorts = [];
    /** @var string[] */
    protected $dependsOn = [];
    /** @var mixed[] */
    protected $ports = [];
    /** @var mixed[] */
    protected $labels = [];
    /** @var mixed[] */
    protected $environment = [];
    /** @var mixed[] */
    protected $volumes = [];
    /** @var \stdClass */
    protected $validatorSchema;

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
     * @param mixed $port
     */
    public function addPort(array $port): void
    {
        $this->ports[] = array(
            'source' => $port['source'] ?? '',  //optional
            'target' => $port['target'],
        );
    }

    /**
     * @param mixed $label
     */
    public function addLabel(array $label): void
    {
        $this->labels[] = array(
            'key' => $label['key'],
            'values' => $label['values'] ?? '', //optional
        );
    }

    /**
     * @param mixed $environment
     */
    public function addEnvironment(array $environment): void
    {
        $this->environment[] = array(
            'key' => $environment['key'],
            'values' => $environment['values'] ?? '', //optional
        );
    }

    /**
     * @param mixed $volume
     */
    public function addVolume(array $volume): void
    {
        $this->volumes[] = array(
            'type' => $volume['type'],
            'source' => $volume['source'],
            'target' => $volume['target'],
            'readOnly' => $volume['readOnly'] ?? null,
        );
    }

    /**
     * @param string $serviceName
     * @return Service
     */
    public function setServiceName(string $serviceName): Service
    {
        $this->serviceName = $serviceName;
        return $this;
    }

    /**
     * @param string $image
     * @return Service
     */
    public function setImage(string $image): Service
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @param int[] $internalPorts
     * @return Service
     */
    public function setInternalPorts(array $internalPorts): Service
    {
        $this->internalPorts = $internalPorts;
        return $this;
    }

    /**
     * @param string[] $dependsOn
     * @return Service
     */
    public function setDependsOn(array $dependsOn): Service
    {
        $this->dependsOn = $dependsOn;
        return $this;
    }

    /**
     * @param mixed[] $ports
     * @return Service
     */
    public function setPorts(array $ports): Service
    {
        $this->ports = $ports;
        return $this;
    }

    /**
     * @param mixed[] $labels
     * @return Service
     */
    public function setLabels(array $labels): Service
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * @param mixed[] $environment
     * @return Service
     */
    public function setEnvironment(array $environment): Service
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @param mixed[] $volumes
     * @return Service
     */
    public function setVolumes(array $volumes): Service
    {
        $this->volumes = $volumes;
        return $this;
    }

}
