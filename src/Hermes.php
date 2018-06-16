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
}
