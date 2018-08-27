<?php

namespace TheAentMachine\Docker;

use PHPUnit\Framework\TestCase;

class ImageServiceTest extends TestCase
{

    public function testGetInternalPorts()
    {
        $ports = ImageService::getInternalPorts('mongo:3.4-jessie'/*'php:7.2-apache'*/);
        $this->assertSame([80], $ports);
    }
}
