<?php
namespace TheAentMachine;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class Hercule
{
    /**
     * @param string[] $events
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public static function setHandledEvents(array $events): void
    {
        $command = ['hercule', 'set:handled-events'];
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

    /**
     * Registers a new Aent in Aenthill
     */
    public static function addAent(string $aent): void
    {
        $containerProjectDir = Pheromone::getContainerProjectDirectory();

        $aenthillJSONstr = file_get_contents($containerProjectDir . '/aenthill.json');
        $aenthillJSON = \GuzzleHttp\json_decode($aenthillJSONstr, true);

        $aents = array();
        if (isset($aenthillJSON['aents'])) {
            foreach ($aenthillJSON['aents'] as $aentDescriptor) {
                if ($aentDescriptor['image'] === $aent) {
                    // Aent already there. Let's return.
                    return;
                }
            }
        }

        $aents['aents'][]['image'] = $aent;

        $filesystem = new Filesystem();
        $filesystem->dumpFile($containerProjectDir . '/aenthill.json', $aents);
    }
}
