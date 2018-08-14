<?php

namespace TheAentMachine\Registry;

use PHPUnit\Framework\TestCase;
use TheAentMachine\Aenthill\CommonMetadata;
use TheAentMachine\Service\Enum\VolumeTypeEnum;
use TheAentMachine\Service\Environment\SharedEnvVariable;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;
use TheAentMachine\Service\Volume\BindVolume;
use TheAentMachine\Service\Volume\NamedVolume;

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
    "ports"         : [{"source": 80, "target": 8080, "comment": "a line of comment"}],
    "environment"   : {
                        "FOO": {"value": "foo", "type": "sharedEnvVariable", "comment": "foo", "containerId": "baz"},
                        "BAR": {"value": "bar", "type": "sharedSecret", "comment": "bar"},
                        "BAZ": {"value": "baz", "type": "imageEnvVariable", "comment": "baz"},
                        "QUX": {"value": "qux", "type": "containerEnvVariable", "comment": "qux"}
                      },
    "labels"        : {
                        "foo": {"value": "fooo", "comment": "fooo"},
                        "bar": {"value": "baar", "comment": "baar"}
                      },
    "volumes"       : [
                        {"type": "volume", "source": "foo", "target": "/foo", "readOnly": true, "comment": "it's a named volume tho", "requestStorage": "8Gi"},
                        {"type": "bind", "source": "/bar", "target": "/bar", "readOnly": false, "comment": "a bind volume"},
                        {"type": "tmpfs", "source": "baz", "comment": "a tmpfs"}
                      ],
    "virtualHosts": [
      {"host": "foo", "port": 80, "comment": "a default virtual host"},
      {"port": 8080, "comment": "it's ok"},
      {"hostPrefix": "foo", "port": 80}
    ],
    "needBuild": true,
    "isVariableEnvironment": true
  },
  "dockerfileCommands": [
    "RUN composer install"
  ],
  "destEnvTypes": [
    "DEV"
  ],
  "resources": {
    "requests": {
      "memory": "64Mi",
      "cpu": "250m"
    },
    "limits": {
      "memory": "128Mi",
      "cpu": "500m"
    }
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
        "type": "AGAIN?WTH",
        "source": "foo"
      }
    ]
  }
}
JSON;

    /** @throws ServiceException */
    public function testValidPayload(): void
    {
        $array = \GuzzleHttp\json_decode(self::VALID_PAYLOAD, true);
        $service = Service::parsePayload($array);
        $out = $service->jsonSerialize();
        $this->assertEquals($array, $out);
        $this->assertTrue($service->isForDevEnvType());
        $this->assertFalse($service->isForTestEnvType());
        $this->assertFalse($service->isForProdEnvType());
    }

    /** @throws ServiceException */
    public function testMissingServiceNamePayload(): void
    {
        $this->expectException(ServiceException::class);
        $array = \GuzzleHttp\json_decode(self::MISSING_SERVICE_NAME_PAYLOAD, true);
        Service::parsePayload($array)->jsonSerialize();
    }

    /** @throws ServiceException */
    public function testUnknownEnvVariableTypePayload(): void
    {
        $this->expectException(ServiceException::class);
        $array = \GuzzleHttp\json_decode(self::UNKNOWN_ENV_VARIABLE_TYPE_PAYLOAD, true);
        Service::parsePayload($array)->jsonSerialize();
    }

    /** @throws ServiceException */
    public function testUnknownVolumeTypePayload(): void
    {
        $this->expectException(ServiceException::class);
        $array = \GuzzleHttp\json_decode(self::UNKNOWN_VOLUME_TYPE_PAYLOAD, true);
        Service::parsePayload($array)->jsonSerialize();
    }

    /** @throws ServiceException */
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
        $s->addPort(80, 8080, 'a line of comment');
        $s->addLabel('foo', 'fooo', 'fooo');
        $s->addLabel('bar', 'baar', 'baar');
        $s->addSharedEnvVariable('FOO', 'foo', 'foo', 'baz');
        $s->addSharedSecret('BAR', 'bar', 'bar');
        $s->addImageEnvVariable('BAZ', 'baz', 'baz');
        $s->addContainerEnvVariable('QUX', 'qux', 'qux');
        $s->addNamedVolume('foo', '/foo', true, 'it\'s a named volume tho', '8Gi');
        $s->addBindVolume('/bar', '/bar', false, 'a bind volume');
        $s->addTmpfsVolume('baz', 'a tmpfs');
        $s->addDockerfileCommand('RUN composer install');
        $s->addVirtualHost('foo', 80, 'a default virtual host');
        $s->addVirtualHost(null, 8080, "it's ok");
        $s->addVirtualHostPrefix('foo', 80, null);
        $s->setNeedBuild(true);
        $s->setIsVariableEnvironment(true);
        $s->addDestEnvType(CommonMetadata::ENV_TYPE_DEV, true);
        $s->setRequestMemory('64Mi');
        $s->setRequestCpu('250m');
        $s->setLimitMemory('128Mi');
        $s->setLimitCpu('500m');
        $outArray = $s->jsonSerialize();
        $expectedArray = \GuzzleHttp\json_decode(self::VALID_PAYLOAD, true);
        $this->assertEquals($outArray, $expectedArray);

        $outArray = $s->imageJsonSerialize();
        $expectedArray = [
            'serviceName' => 'foo',
            'dockerfileCommands' => [
                'FROM foo/bar:baz',
                'ENV BAZ=baz',
                'COPY /bar /bar',
                'CMD foo -bar -baz --qux',
                'RUN composer install'
            ],
            'destEnvTypes' => ['DEV']
        ];
        $this->assertEquals($outArray, $expectedArray);
    }

    /** @throws ServiceException */
    public function testInvalidRequestMemoryPattern(): void
    {
        $s = new Service();
        $s->setServiceName('foo');
        $s->setRequestMemory('0.5Zi');
        $this->expectException(ServiceException::class);
        $s->jsonSerialize();
    }

    /** @throws ServiceException */
    public function testInvalidRequestCpuPattern(): void
    {
        $s = new Service();
        $s->setServiceName('foo');
        $s->setRequestCpu('0,1');
        $this->expectException(ServiceException::class);
        $s->jsonSerialize();
    }

    public function testVolumeRemovers(): void
    {
        $s = new Service();
        $s->setServiceName('my-service');
        $s->addBindVolume('./foo', '/opt/app/foo', true);
        $s->addBindVolume('./bar', '/opt/app/baz', false);
        $s->addNamedVolume('my-data', '/data', true);
        $s->removeVolumesBySource('./bar');
        /** @var BindVolume[]|NamedVolume[] $volumes */
        $volumes = $s->getVolumes();
        $this->assertEquals(count($volumes), 2);
        $this->assertEquals($volumes[0]->getType(), VolumeTypeEnum::BIND_VOLUME);
        $this->assertEquals($volumes[0]->getSource(), './foo');
        $this->assertEquals($volumes[1]->getType(), VolumeTypeEnum::NAMED_VOLUME);

        $s->addBindVolume('./bar', '/opt/app/baz', false);
        $s->removeAllBindVolumes();
        $volumes = $s->getVolumes();
        $this->assertEquals(count($volumes), 1);
        $this->assertEquals($volumes[0]->getType(), VolumeTypeEnum::NAMED_VOLUME);
        $this->assertEquals($volumes[0]->getTarget(), '/data');
    }

    public function testEnvVariableContains(): void
    {
        $s = new Service();
        $s->setServiceName('my-service');
        $s->addSharedSecret('MYSQL_ROOT_PASSWORD', 'foo', 'comment', 'container');
        self::assertCount(0, $s->getAllSharedEnvVariable());
        self::assertCount(1, $s->getAllSharedSecret());
        self::assertCount(0, $s->getAllImageEnvVariable());
        self::assertCount(0, $s->getAllContainerEnvVariable());
        $variable = $s->getAllSharedSecret()['MYSQL_ROOT_PASSWORD'];
        $this->assertInstanceOf(SharedEnvVariable::class, $variable);
        $this->assertSame('foo', $variable->getValue());
        $this->assertSame('comment', $variable->getComment());
        $this->assertSame('container', $variable->getContainerId());
    }
}