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
     * @param mixed[] $payload
     */
    public static function dispatchJson(string $event, array $payload): void
    {
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
}
