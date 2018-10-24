<?php


namespace TheAentMachine\Aenthill;

use Symfony\Component\Process\Process;
use TheAentMachine\Helper\ReplyAggregator;

final class Aenthill
{
    /**
     * Updates current aent in the manifest.
     *
     * @param null|array<string,string> $metadata
     * @param null|string[] $events
     */
    public static function update(?array $metadata = null, ?array $events = null): void
    {
        $command = ['aenthill', 'update'];

        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $command[] = '-m';
                $command[] = $key . '=' . $value;
            }
        }

        if (!empty($events)) {
            foreach ($events as $event) {
                $command[] = '-e';
                $command[] = $event;
            }
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();
    }

    /**
     * Adds a dependency in manifest to current aent.
     *
     * @param string $image
     * @param string $key
     * @param null|array<string,string> $metadata
     */
    public static function register(string $image, string $key, ?array $metadata = null): void
    {
        $command = ['aenthill', 'register', $image, $key];

        if (!empty($metadata)) {
            foreach ($metadata as $k => $value) {
                $command[] = '-m';
                $command[] = $k . '=' . $value;
            }
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();
    }

    public static function metadata(string $key): string
    {
        $command = ['aenthill', 'metadata', $key];
        $process = new Process($command);
        $process->mustRun();
        return $process->getOutput();
    }

    public static function dependency(string $key): string
    {
        $command = ['aenthill', 'dependency', $key];
        $process = new Process($command);
        $process->mustRun();
        return $process->getOutput();
    }

    /**
     * Starts an aent.
     *
     * @param string $target the image name or a key from the manifest.
     * @param string $event
     * @param null|string $payload
     * @return string[]
     */
    public static function run(string $target, string $event, ?string $payload = null): array
    {
        $replyAggregator = new ReplyAggregator();
        $replyAggregator->clear();
        $command = ['aenthill', 'run', $target, $event];

        if (null !== $payload) {
            $command[] = $payload;
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();

        return $replyAggregator->getReplies();
    }

    /**
     * @param string $target
     * @param string $event
     * @param mixed[] $payload
     * @return string[]
     */
    public static function runJson(string $target, string $event, array $payload): array
    {
        return self::run($target, $event, \GuzzleHttp\json_encode($payload));
    }

    /**
     * Dispatches an event to all aents from the manifest which can handle it.
     *
     * @param string $event
     * @param null|string $payload
     * @param null|string $filters
     * @return string[] the array of replies received from all aents that replied.
     */
    public static function dispatch(string $event, ?string $payload = null, ?string $filters = null): array
    {
        $replyAggregator = new ReplyAggregator();
        $replyAggregator->clear();

        $command = ['aenthill', 'dispatch', $event];

        if (null !== $payload) {
            $command[] = $payload;
        }

        if (!empty($filters)) {
            $command[] = '-f';
            $command[] = $filters;
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();

        return $replyAggregator->getReplies();
    }

    /**
     * @param mixed[]|object $payload
     * @param null|string $filters
     * @return mixed[]
     */
    public static function dispatchJson(string $event, $payload, ?string $filters = null): array
    {
        if (\is_object($payload) && !$payload instanceof \JsonSerializable) {
            throw new \RuntimeException('Payload object should implement JsonSerializable. Got an instance of ' . \get_class($payload));
        }
        $replies = self::dispatch($event, \GuzzleHttp\json_encode($payload), $filters);
        return \array_map(function (string $reply) {
            return \GuzzleHttp\json_decode($reply, true);
        }, $replies);
    }

    /**
     * Replies to the aent which started this aent.
     *
     * @param string $event
     * @param null|string $payload
     */
    public static function reply(string $event, ?string $payload = null): void
    {
        $command = ['aenthill', 'reply', $event];

        if (null !== $payload) {
            $command[] = $payload;
        }

        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();
    }

    /**
     * @param string $event
     * @param mixed[] $payload
     */
    public static function replyJson(string $event, array $payload): void
    {
        self::reply($event, \GuzzleHttp\json_encode($payload));
    }
}
