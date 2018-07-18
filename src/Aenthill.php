<?php


namespace TheAentMachine;

use Symfony\Component\Process\Process;
use TheAentMachine\Exception\MissingEnvironmentVariableException;

class Aenthill
{
    /**
     * Installs or updates current aent in the manifest.
     *
     * @param null|string[] $events
     * @param null|array<string,string> $metadata
     */
    public static function installOrUpdate(?array $events = null, ?array $metadata = null): void
    {
        $command = ['aenthill', 'install'];
        if (!empty($events)) {
            foreach ($events as $event) {
                $command[] = '-e';
                $command[] = $event;
            }
        }
        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $command[] = '-m';
                $command[] = $key . '=' . $value;
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
     * @param array|null $events
     * @param array|null $metadata
     */
    public static function addDependency(string $image, string $key, ?array $events = null, ?array $metadata = null): void
    {
        $command = ['aenthill', 'register', $image, $key];
        if (!empty($events)) {
            foreach ($events as $event) {
                $command[] = '-e';
                $command[] = $event;
            }
        }
        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $command[] = '-m';
                $command[] = $key . '=' . $value;
            }
        }
        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();
    }


    /**
     * Starts an aent.
     *
     * @param string $target the image name or a key from the manifest.
     * @param string $event
     * @param null|string $payload
     */
    public static function run(string $target, string $event, ?string $payload = null): void
    {
        $command = ['aenthill', 'run', $target, $event];
        if (!empty($payload)) {
            $command[] = $payload;
        }
        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();
    }

    /**
     * @param string $target
     * @param string $event
     * @param mixed[] $payload
     */
    public static function runJson(string $target, string $event, array $payload): void
    {
        self::run($target, $event, \GuzzleHttp\json_encode($payload));
    }

    /**
     * Dispatches an event to all aents from the manifest which can handle it.
     *
     * @param string $event
     * @param null|string $payload
     * @return string[] the array of replies received from all aents that replied.
     */
    public static function dispatch(string $event, ?string $payload = null): array
    {
        $replyAggregator = new ReplyAggregator();
        $replyAggregator->clear();
        $command = ['aenthill', 'dispatch', $event];
        if (!empty($payload)) {
            $command[] = $payload;
        }
        $process = new Process($command);
        $process->enableOutput();
        $process->setTty(true);
        $process->mustRun();
        $replies = $replyAggregator->getReplies();
        return $replies;
    }

    /**
     * @param mixed[]|object $payload
     * @return mixed[]
     */
    public static function dispatchJson(string $event, $payload): array
    {
        if (\is_object($payload) && !$payload instanceof \JsonSerializable) {
            throw new \RuntimeException('Payload object should implement JsonSerializable. Got an instance of '.\get_class($payload));
        }
        $replies = self::dispatch($event, \GuzzleHttp\json_encode($payload));
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
        if (!empty($payload)) {
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
    
    /**
     * Returns the list of aents in the manifest which handles the given event.
     *
     * @param string $handledEvent
     * @return string[]
     * @throws MissingEnvironmentVariableException
     */
    public static function findAentsByHandledEvent(string $handledEvent): array
    {
        $containerProjectDir = Pheromone::getContainerProjectDirectory();

        $aenthillJSONstr = file_get_contents($containerProjectDir . '/aenthill.json');
        if ($aenthillJSONstr === false) {
            throw new \RuntimeException('Failed to load file '.$containerProjectDir . '/aenthill.json');
        }
        $aenthillJSON = \GuzzleHttp\json_decode($aenthillJSONstr, true);

        $aents = array();
        if (isset($aenthillJSON['aents'])) {
            foreach ($aenthillJSON['aents'] as $aent) {
                if (array_key_exists('events', $aent) && \in_array($handledEvent, $aent['events'], true)) {
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
     * @throws MissingEnvironmentVariableException
     */
    public static function canHandleEvent(string $handledEvent): bool
    {
        return count(self::findAentsByHandledEvent($handledEvent)) > 0;
    }
}
