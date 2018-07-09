<?php

namespace TheAentMachine;

use Symfony\Component\Process\Process;

class Hermes
{
    /**
     * @param string $event
     * @param null|string $payload
     * @return string[] The array of replies received from all Aents that replied.
     */
    public static function dispatch(string $event, ?string $payload = null): array
    {
        $replyAggregator = new ReplyAggregator();
        $replyAggregator->clear();

        $command = ['hermes', 'dispatch', $event];
        if (!empty($payload)) {
            $command[] = $payload;
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);

        $process->mustRun();

        return $replyAggregator->getReplies();
    }

    /**
     * @param mixed[]|object $payload
     * @return mixed[]
     */
    public static function dispatchJson(string $event, $payload): array
    {
        if (\is_object($payload) && !$payload instanceof \JsonSerializable) {
            throw new \RuntimeException('Payload object should implement JsonSerializable. Got an instance of ' . \get_class($payload));
        }
        $replies = self::dispatch($event, \GuzzleHttp\json_encode($payload));

        return \array_map(function (string $reply) {
            return \GuzzleHttp\json_decode($reply, true);
        }, $replies);
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
        self::reply($event, \GuzzleHttp\json_encode($payload));
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
        $manifest = Pheromone::getAenthillManifestContent();

        $aents = array();
        if (isset($manifest['aents'])) {
            foreach ($manifest['aents'] as $aent) {
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
