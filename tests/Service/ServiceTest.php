<?php

namespace TheAentMachine\Registry;


use PHPUnit\Framework\TestCase;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

class ServiceTest extends TestCase
{
    private const VALID_PAYLOAD = <<< 'JSON'
{
  "serviceName" : "foo",
  "service": {
    "image"         : "foo",
    "command"       : ["foo", "-bar", "-baz", "--qux"],
    "internalPorts" : [1, 2, 3],
    "dependsOn"     : ["foo", "bar"],
    "ports"         : [{"source": 80, "target": 8080}],
    "environment"   : {
                        "FOO": {
                          "value": "fooo",
                          "type": "containerEnvVariable"
                        }
                      },
    "labels"        : {
                        "foo": {"value": "fooo"},
                        "bar": {"value": "baar"}
                      },               
    "volumes"       : [
                        {
                          "type"      : "volume",
                          "source"    : "foo",
                          "target"    : "bar",
                          "readOnly"  : true
                        }
                      ]
  }
}
JSON;

    private const MISSING_SERVICE_NAME_PAYLOAD = <<< 'JSON'
{
  "service": {
    "internalPorts": [80]
  }
}
JSON;

    private const UNKNOWN_ENV_VARIABLE_TYPE_PAYLOAD = <<< 'JSON'
{
  "serviceName": "foo",
  "service": {
    "environment": {
      "FOO": {
        "value": "fooo",
        "type": "YIKES_THATS_SOME_BAD_TYPE_HERE"
      }
    }
  }
}
JSON;

    private const UNKNOWN_VOLUME_TYPE_PAYLOAD = <<< 'JSON'
{
  "serviceName": "foo",
  "service": {
    "volumes": [
      {
        "type": "AGAIN?WTF",
        "source": "foo"
      }
    ]
  }
}
JSON;

    public function testValidPayload() : void
    {
        $array = json_decode(self::VALID_PAYLOAD, true);
        $service = Service::parsePayload($array);
        $out = $service->jsonSerialize();
        $this->assertEquals($array, $out);
    }

    public function testMissingServiceNamePayload() : void
    {
        $this->expectException(ServiceException::class);
        $array = json_decode(self::MISSING_SERVICE_NAME_PAYLOAD, true);
        Service::parsePayload($array)->jsonSerialize();
    }

    public function testUnknownEnvVariableTypePayload() : void
    {
        $this->expectException(ServiceException::class);
        $array = json_decode(self::UNKNOWN_ENV_VARIABLE_TYPE_PAYLOAD, true);
        Service::parsePayload($array)->jsonSerialize();
    }

    public function testUnknownVolumeTypePayload() : void
    {
        $this->expectException(ServiceException::class);
        $array = json_decode(self::UNKNOWN_VOLUME_TYPE_PAYLOAD, true);
        Service::parsePayload($array)->jsonSerialize();
    }

    public function testLabels(): void
    {
        $service = new Service();
        $service->setServiceName('foobar');
        $service->addLabel('traefik.backend', 'foobar');
        $array = $service->jsonSerialize();
        $this->assertEquals(['traefik.backend' => ['value'=>'foobar']], $array['service']['labels']);
    }
}
