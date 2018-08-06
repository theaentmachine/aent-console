<?php


namespace TheAentMachine\Helper;

use Symfony\Component\Console\Output\OutputInterface;
use TheAentMachine\Exception\LogLevelException;
use TheAentMachine\Aenthill\Pheromone;

class LogLevelConfigurator
{
    /** @var array */
    private $levels = [
        'DEBUG' => OutputInterface::VERBOSITY_DEBUG,
        'INFO' => OutputInterface::VERBOSITY_VERY_VERBOSE,
        'WARN' => OutputInterface::VERBOSITY_VERBOSE,
        'ERROR' => OutputInterface::VERBOSITY_NORMAL,
    ];

    /** @var OutputInterface */
    private $output;

    /**
     * Log constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @throws LogLevelException
     * @return void
     */
    public function configureLogLevel(): void
    {
        $logLevel = Pheromone::getLogLevel();
        $this->output->setVerbosity($this->levels[$logLevel]);
    }
}
