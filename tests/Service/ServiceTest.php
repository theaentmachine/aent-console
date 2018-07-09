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
    "image"         : "foo/bar:baz",
    "command"       : ["foo", "-bar", "-baz", "--qux"],
    "internalPorts" : [1, 2, 3],
    "dependsOn"     : ["foo", "bar"],
    "ports"         : [{"source": 80, "target": 8080}],
    "environment"   : {
                        "FOO": {"value": "foo", "type": "sharedEnvVariable"},
                        "BAR": {"value": "bar", "type": "sharedSecret"},
                        "BAZ": {"value": "baz", "type": "imageEnvVariable"},
                        "QUX": {"value": "qux", "type": "containerEnvVariable"}
                      },
    "labels"        : {
                        "foo": {"value": "fooo"},
                        "bar": {"value": "baar"}
                      },               
    "volumes"       : [
                        {"type": "volume", "source": "foo", "target": "/foo", "readOnly": true},
                        {"type": "bind", "source": "/bar", "target": "/bar", "readOnly": false},
                        {"type": "tmpfs", "source": "baz"}
                      ]
  },
  "dockerfileCommands": [
    "RUN composer install"
  ]
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

    public function testValidPayload(): void
    {
        $array = json_decode(self::VALID_PAYLOAD, true);
        $service = Service::parsePayload($array);
        $out = $service->jsonSerialize();
        $this->assertEquals($array, $out);
    }

    public function testMissingServiceNamePayload(): void
    {
        $this->expectException(ServiceException::class);
        $array = json_decode(self::MISSING_SERVICE_NAME_PAYLOAD, true);
        Service::parsePayload($array)->jsonSerialize();
    }

    public function testUnknownEnvVariableTypePayload(): void
    {
        $this->expectException(ServiceException::class);
        $array = json_decode(self::UNKNOWN_ENV_VARIABLE_TYPE_PAYLOAD, true);
        Service::parsePayload($array)->jsonSerialize();
    }

    public function testUnknownVolumeTypePayload(): void
    {
        $this->expectException(ServiceException::class);
        $array = json_decode(self::UNKNOWN_VOLUME_TYPE_PAYLOAD, true);
        Service::parsePayload($array)->jsonSerialize();
    }

    public function testSettersAndAdders(): void
    {
        $s = new Service();
        $s->setServiceName('foo');
        $s->setImage('foo/bar:baz');
        $s->setCommand(['foo', '-bar', '-baz']);
        $s->addCommand('--qux');
        $s->setInternalPorts([1, 2]);
        $s->addInternalPort(3);
        $s->setDependsOn(['foo']);
        $s->addDependsOn('bar');
        $s->addPort(80, 8080);
        $s->addLabel('foo', 'fooo');
        $s->addLabel('bar', 'baar');
        $s->addSharedEnvVariable('FOO', 'foo');
        $s->addSharedSecret('BAR', 'bar');
        $s->addImageEnvVariable('BAZ', 'baz');
        $s->addContainerEnvVariable('QUX', 'qux');
        $s->addNamedVolume('foo', '/foo', true);
        $s->addBindVolume('/bar', '/bar', false);
        $s->addTmpfsVolume('baz');
        $s->addDockerfileCommand('RUN composer install');
        $outArray = $s->jsonSerialize();
        $expectedArray = json_decode(self::VALID_PAYLOAD, true);
        $this->assertEquals($outArray, $expectedArray);

        $outArray = $s->imageJsonSerialize();
        $expectedArray = [
            'FROM foo/bar:baz',
            'ENV BAZ=baz',
            'COPY /bar /bar',
            'CMD foo -bar -baz --qux',
            'RUN composer install'
        ];
        $this->assertEquals($outArray, $expectedArray);




    }
}