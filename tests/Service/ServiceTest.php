<?php

namespace TheAentMachine\Registry;


use PHPUnit\Framework\TestCase;
use TheAentMachine\Service\Exception\ServiceException;
use TheAentMachine\Service\Service;

class ServiceTest extends TestCase
{
    private const PAYLOAD = <<< 'JSON'
{
  "serviceName" : "foo",
  "service": {
    "image"         : "foo",
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

    /**
     * @throws ServiceException
     */
    public function testCheckValidity() : void
    {
        $p = json_decode(self::PAYLOAD, true);
        $service = Service::parsePayload($p);
        $json = json_encode($service->jsonSerialize(), JSON_PRETTY_PRINT);
        $this->assertNotEmpty($json);
    }
}
