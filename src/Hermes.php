<?php
namespace TheAentMachine;

use Symfony\Component\Process\Process;

class Hermes
{
    public static function dispatch(string $event, ?string $payload = null): void
    {
        $command = ['hermes', 'dispatch', $event];
        if (!empty($payload)) {
            $command[] = $payload;
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);

        $process->mustRun();
    }

    /**
     * @param mixed[]|object $payload
     */
    public static function dispatchJson(string $event, $payload): void
    {
        if (\is_object($payload) && !$payload instanceof \JsonSerializable) {
            throw new \RuntimeException('Payload object should implement JsonSerializable. Got an instance of '.\get_class($payload));
        }
        self::dispatch($event, \json_encode($payload));
    }

    public static function reply(string $event, ?string $payload = null): void
    {
        $command = ['hermes', 'reply', $event];
        if (!empty($payload)) {
            $command[] = $payload;
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);

        $process->mustRun();
    }

    /**
     * @param mixed[] $payload
     */
    public static function replyJson(string $event, array $payload): void
    {
        self::reply($event, \json_encode($payload));
    }

    /**
     * @param string[] $images
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public static function setDependencies(array $images): void
    {
        $command = ['hermes', 'set:dependencies'];
        foreach ($images as $image) {
            $command[] = $image;
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);

        $process->mustRun();
    }

    /**
     * @param string[] $events
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public static function setHandledEvents(array $events): void
    {
        $command = ['hermes', 'set:handled-events'];
        foreach ($events as $event) {
            $command[] = $event;
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);

        $process->mustRun();
    }
    
    /**
     * @param string $handledEvent
     * @return string[]
     */
    public static function findAentsByHandledEvent(string $handledEvent): array
    {
        $containerProjectDir = Pheromone::getContainerProjectDirectory();

        $aenthillJSONstr = file_get_contents($containerProjectDir . '/aenthill.json');
        $aenthillJSON = \GuzzleHttp\json_decode($aenthillJSONstr, true);

        $aents = array();
        if (isset($aenthillJSON['aents'])) {
            foreach ($aenthillJSON['aents'] as $aent) {
                if (array_key_exists('handled_events', $aent) && \in_array($handledEvent, $aent['handled_events'], true)) {
                    $aents[] = $aent;
                }
            }
        }
        return $aents;
    }

    /**
     * Returns true if one of the aents installed can explicitly handle events of type $handledEvent
     *
     * @param string $handledEvent
     * @return bool
     */
    public static function canHandleEvent(string $handledEvent): bool
    {
        return count(self::findAentsByHandledEvent($handledEvent)) > 0;
    }
}
