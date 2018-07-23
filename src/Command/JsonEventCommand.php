<?php


namespace TheAentMachine\Command;

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
        $data = \GuzzleHttp\json_decode($payload, true);
        $result = $this->executeJsonEvent($data);
        if ($result === null) {
            return null;
        }
        return \GuzzleHttp\json_encode($result);
    }
}
