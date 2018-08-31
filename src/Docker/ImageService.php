<?php
namespace TheAentMachine\Docker;

use Docker\API\Client;
use Docker\API\Exception\ImageInspectNotFoundException;
use Docker\API\Model\ContainerConfig;
use Docker\API\Model\CreateImageInfo;
use Docker\Docker;
use Docker\Stream\CreateImageStream;
use Psr\Log\LoggerInterface;
use TheAentMachine\Exception\AenthillException;

class ImageService
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Docker
     */
    private $docker;

    public function __construct(LoggerInterface $logger)
    {
        $this->docker = Docker::create();
        $this->logger = $logger;
    }

    /**
     * @return int[]
     */
    public function getInternalPorts(string $imageName) : array
    {
        $ports = $this->getInspection($imageName)->getExposedPorts();
        if ($ports === null) {
            return [];
        }

        $finalPorts = [];
        foreach ($ports as $portStr => $obj) {
            // $portStr = "80/tcp". Let's remove the string by casting.
            $finalPorts[] = (int) $portStr;
        }
        return $finalPorts;
    }

    /**
     * @return string[]
     */
    public function getVolumes(string $imageName) : array
    {
        $volumes = $this->getInspection($imageName)->getVolumes();
        if ($volumes === null) {
            return [];
        }

        return \array_keys($volumes->getArrayCopy());
    }

    private function getInspection(string $imageName) : ContainerConfig
    {
        try {
            $config = $this->docker->imageInspect($imageName)->getConfig();
        } catch (ImageInspectNotFoundException $e) {
            $this->pull($imageName);
            $config = $this->docker->imageInspect($imageName)->getConfig();
        }
        if ($config === null) {
            throw new AenthillException('Cannot inspect container '.$imageName.'. Missing config key.');
        }
        return $config;
    }

    public function pullIfNotAvailable(string $imageName): void
    {
        try {
            $this->docker->imageInspect($imageName);
        } catch (ImageInspectNotFoundException $e) {
            $this->pull($imageName);
        }
    }

    public function pull(string $imageName): void
    {
        /** @var CreateImageStream $result */
        $result = $this->docker->imageCreate($imageName, [
            'fromImage' => $imageName
        ]);

        $result->onFrame(function (CreateImageInfo $frame) {
            $this->logger->info($frame->getStatus());
        });
        $result->wait();
    }

    public function rmi(string $imageName): void
    {
        $this->docker->imageDelete($imageName);
    }
}
