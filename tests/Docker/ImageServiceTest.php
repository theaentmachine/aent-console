<?php

namespace TheAentMachine\Docker;

use Docker\API\Exception\ImageDeleteNotFoundException;
use Gamez\Psr\Log\TestLogger;
use PHPUnit\Framework\TestCase;

class ImageServiceTest extends TestCase
{
    public function testPull(): void
    {
        $logger = new TestLogger();
        $imageService = new ImageService($logger);

        try {
            $imageService->rmi('busybox:1.29.2');
        } catch(ImageDeleteNotFoundException $e) {
            // Try to delete. If this fails, it's ok.
        }

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
