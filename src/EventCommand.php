<?php


namespace TheAentMachine;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

abstract class EventCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    protected $log;
    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var OutputInterface
     */
    protected $output;

    abstract protected function getEventName(): string;
    abstract protected function executeEvent(?string $payload): ?string;

    protected function configure()
    {
        $this
            ->setName($this->getEventName())
            ->setDescription('Handle the "' . $this->getEventName() . '" event')
            ->addArgument('payload', InputArgument::OPTIONAL, 'The event payload');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        // Let's send the list of caught events to Hercule
        Hermes::setHandledEvents($this->getAllEventNames());

        $logLevelConfigurator = new LogLevelConfigurator($output);
        $logLevelConfigurator->configureLogLevel();

        $this->log = new ConsoleLogger($output);

        $payload = $input->getArgument('payload');
        $this->input = $input;
        $this->output = $output;

        $result = $this->executeEvent($payload);

        // Now, let's send a "reply" event
        if ($result !== null) {
            Hermes::reply('reply', $result);
        }
    }

    /**
     * @return string[]
     */
    private function getAllEventNames(): array
    {
        return array_map(function (EventCommand $event) {
            return $event->getEventName();
        }, \array_filter($this->getApplication()->all(), function (Command $command) {
            return $command instanceof EventCommand && !$command->isHidden();
        }));
    }
}
