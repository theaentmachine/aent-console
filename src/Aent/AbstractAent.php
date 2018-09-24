<?php

namespace TheAentMachine\Aent;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TheAentMachine\Aent\Event\ReplyEvent;
use TheAentMachine\Aent\Event\VoidEvent;
use TheAentMachine\Helper\ReplyAggregator;

abstract class AbstractAent extends Application
{
    /** @var VoidEvent */
    private $voidEvent;

    /**
     * AbstractAent constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->voidEvent = new VoidEvent();
        $this->add($this->voidEvent);
        $this->add(new ReplyEvent(new ReplyAggregator()));
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @return int|void
     * @throws \Exception
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $input = new ArgvInput();
        $output = new ConsoleOutput();
        $outputStyle = new OutputFormatterStyle('magenta');
        $output->getFormatter()->setStyle('info', $outputStyle);
        $outputStyle = new OutputFormatterStyle('black', 'magenta', ['bold']);
        $output->getFormatter()->setStyle('block', $outputStyle);
        $outputStyle = new OutputFormatterStyle('black', 'cyan', ['bold']);
        $output->getFormatter()->setStyle('altblock', $outputStyle);
        parent::run($input, $output);
    }

    /**
     * Overrides the Symfony "find" method to return a default command if no command is found.
     * @param string $name
     * @return Command
     */
    public function find($name)
    {
        try {
            if (!$this->has($name)) {
                return $this->voidEvent;
            }
            return parent::find($name);
        } catch (CommandNotFoundException $e) {
            return $this->voidEvent;
        }
    }
}
