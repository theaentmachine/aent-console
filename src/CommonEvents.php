<?php


namespace TheAentMachine;

use Symfony\Component\Console\Output\OutputInterface;
use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Service\Service;

class CommonEvents
{
    private const NEW_SERVICE = 'NEW_SERVICE';
    private const NEW_VIRTUAL_HOST = 'NEW_VIRTUAL_HOST';
    private const NEW_IMAGE = 'NEW_IMAGE';

    /** @var AentHelper */
    private $aentHelper;

    /** @var OutputInterface */
    private $output;

    /**
     * CommonEvents constructor.
     * @param AentHelper $aentHelper
     * @param OutputInterface $output
     */
    public function __construct(AentHelper $aentHelper, OutputInterface $output)
    {
        $this->aentHelper = $aentHelper;
        $this->output = $output;
    }

    public function dispatchService(Service $service): void
    {
        Aenthill::dispatchJson(self::NEW_SERVICE, $service);
    }

    public function dispatchNewVirtualHost(string $serviceName, int $virtualPort = 80, string $virtualHost = null): ?array
    {
        $message = [
            'service' => $serviceName,
            'virtualPort' => $virtualPort
        ];
        if ($virtualHost !== null) {
            $message['virtualHost'] = $virtualHost;
        }

        return Aenthill::dispatchJson(self::NEW_VIRTUAL_HOST, $message);
    }


    public function dispatchImage(Service $service): void
    {
        Aenthill::dispatchJson(self::NEW_IMAGE, $service->imageJsonSerialize());
    }
}
