<?php

namespace TheAentMachine\Aent\Event;

use Safe\Exceptions\StringsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Prompt\Prompt;
use function Safe\sprintf;

abstract class AbstractEvent extends Command
{
    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var Prompt */
    protected $prompt;

    /**
     * @return string
     */
    abstract protected function getEventName(): string;

    /**
     * @return bool
     */
    abstract protected function shouldRegisterEvents(): bool;

    /**
     * return @void
     */
    abstract protected function beforeExecute(): void;

    /**
     * @param null|string $payload
     * @return null|string
     */
    abstract protected function executeEvent(?string $payload): ?string;

    /**
     * return @void
     */
    abstract protected function afterExecute(): void;

    /**
     * @return void
     * @throws StringsException
     */
    protected function configure()
    {
        $this
            ->setName($this->getEventName())
            ->setDescription(sprintf('Handles the <info>%s</info> event', $this->getEventName()))
            ->addArgument('payload', InputArgument::OPTIONAL, 'The event payload');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if ($this->shouldRegisterEvents()) {
            $this->registerEvents();
        }
        $this->input = $input;
        $this->output = $output;
        $this->prompt = new Prompt($this->input, $this->output, $this->getHelper('question'), $this->getHelper('formatter'));
        $this->beforeExecute();
        $result = $this->executeEvent($input->getArgument('payload'));
        $this->afterExecute();
        if ($result !== null) {
            Aenthill::reply('REPLY', $result);
        }
    }

    /**
     * @return void
     */
    public function registerEvents(): void
    {
        $events = \array_map(function (AbstractEvent $event) {
            return $event->getEventName();
        }, \array_filter($this->getApplication()->all(), function (Command $command) {
            return $command instanceof AbstractEvent && !$command->isHidden();
        }));
        Aenthill::update(null, $events);
    }

    /**
     * @return string
     */
    protected function getAentName(): string
    {
        return $this->getApplication()->getName();
    }
}
