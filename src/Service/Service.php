<?php

namespace TheAentMachine\Service;

use Opis\JsonSchema\ValidationError;
use Opis\JsonSchema\Validator;
use TheAentMachine\Service\Exception\ServiceException;

class Service implements \JsonSerializable
{
    /** @var string */
    protected $serviceName;
    /** @var string */
    protected $image;
    /** @var int[]|string[] */
    protected $internalPorts;
    /** @var string[] */
    protected $dependsOn;
    /** @var mixed[] */
    protected $ports;
    /** @var mixed[] */
    protected $labels;
    /** @var mixed[] */
    protected $environment;
    /** @var mixed[] */
    protected $volumes;
    /** @var \stdClass */
    protected $validatorSchema;

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->serviceName = '';
        $this->image = '';
        $this->internalPorts = array();
        $this->dependsOn = array();
        $this->ports = array();
        $this->labels = array();
        $this->environment = array();
        $this->volumes = array();
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
     * @param \stdClass|array|string $data
     * @param bool $throwsException
     * @return bool
     * @throws ServiceException
     */
    public function checkValidity($data, bool $throwsException = true): bool
    {
        if (\is_array($data)) {
            $data = json_decode(json_encode($data), false);
        }
        $validator = new Validator();
        $result = $validator->dataValidation($data, $this->validatorSchema);
        if ($throwsException && !$result->isValid()) {
            /** @var ValidationError $vError */
            $vError = $result->getFirstError();
            throw ServiceException::invalidServiceData($vError);
        }
        return $result->isValid();
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
        $array = Utils::arrayFilterRec(array(
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
     * @param int[]|string[] $internalPorts
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
     * @param string[] $ports
     * @return Service
     */
    public function setPorts(array $ports): Service
    {
        $this->ports = $ports;
        return $this;
    }

    /**
     * @param string[] $labels
     * @return Service
     */
    public function setLabels(array $labels): Service
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * @param string[] $environment
     * @return Service
     */
    public function setEnvironment(array $environment): Service
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @param string[] $volumes
     * @return Service
     */
    public function setVolumes(array $volumes): Service
    {
        $this->volumes = $volumes;
        return $this;
    }
}
