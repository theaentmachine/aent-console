<?php

namespace TheAentMachine\Aent\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Prompt\Prompt;

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
     * @param null|string $payload
     * @return null|string
     */
    abstract protected function executeEvent(?string $payload): ?string;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName($this->getEventName())
            ->setDescription('Handles the "' . $this->getEventName() . '" event')
            ->addArgument('payload', InputArgument::OPTIONAL, 'The event payload');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;
        $outputStyle = new OutputFormatterStyle('magenta');
        $this->output->getFormatter()->setStyle('info', $outputStyle);
        $outputStyle = new OutputFormatterStyle('black', 'magenta', ['bold']);
        $this->output->getFormatter()->setStyle('block', $outputStyle);
        $this->prompt = new Prompt($this->input, $this->output, $this->getHelper('question'), $this->getHelper('formatter'));
        $result = $this->executeEvent($input->getArgument('payload'));
        if ($result !== null) {
            Aenthill::reply('REPLY', $result);
        }
    }

    /**
     * @return string[]
     */
    protected function getAllEventNames(): array
    {
        return \array_map(function (AbstractEvent $event) {
            return $event->getEventName();
        }, \array_filter($this->getApplication()->all(), function (Command $command) {
            return $command instanceof AbstractEvent && !$command->isHidden();
        }));
    }
}
