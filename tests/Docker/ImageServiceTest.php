<?php

namespace TheAentMachine\Docker;

use Gamez\Psr\Log\TestLogger;
use PHPUnit\Framework\TestCase;

class ImageServiceTest extends TestCase
{
    public function testPull(): void
    {
        $logger = new TestLogger();
        $imageService = new ImageService($logger);

        $imageService->rmi('busybox:1.29.2');

        $imageService->pull('busybox:1.29.2');
        $log = $logger->log;
        $this->assertTrue($log->has('Pulling from library/busybox'));
    }

    public function testGetInternalPorts(): void
    {
        $logger = new TestLogger();
        $imageService = new ImageService($logger);

        $ports = $imageService->getInternalPorts('php:7.2-apache');
        $this->assertSame([80], $ports);
    }

    public function testGetVolumes(): void
    {
        $logger = new TestLogger();
        $imageService = new ImageService($logger);

        $ports = $imageService->getVolumes('mysql:5.7');
        $this->assertSame(['/var/lib/mysql'], $ports);
    }
}
