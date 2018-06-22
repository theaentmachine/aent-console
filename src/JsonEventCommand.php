<?php


namespace TheAentMachine;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Events that have JSON payloads should extend this class.
 */
abstract class JsonEventCommand extends EventCommand
{
    /**
     * @param mixed[] $payload
     * @return mixed[]|null
     */
    abstract protected function executeJsonEvent(array $payload): ?array;

    protected function executeEvent(?string $payload): ?string
    {
        if ($payload === null) {
            throw new \InvalidArgumentException('Empty payload. JSON message expected.');
        }
        $data = \json_decode($payload, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(
                'json_decode error: ' . json_last_error_msg()
            );
        }
        $result = $this->executeJsonEvent($data);
        if ($result === null) {
            return null;
        }
        return \json_encode($result);
    }
}
