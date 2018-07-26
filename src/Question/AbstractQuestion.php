<?php

namespace TheAentMachine\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractQuestion
{
    /** @var QuestionHelper */
    protected $helper;

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var string */
    protected $question;

    /** @var string|null */
    protected $default;

    /** @var string|null */
    protected $helpText;

    /** @var bool */
    protected $printAnswer;

    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $helper, string $question, bool $printAnswer = true)
    {
        $this->helper = $helper;
        $this->input = $input;
        $this->output = $output;
        $this->question = $question;
        $this->printAnswer = $printAnswer;
    }

    protected function spacer(): void
    {
        $this->output->writeln('');
    }

    /**
     * @param string $default
     * @return mixed
     */
    abstract public function setDefault(string $default);

    /**
     * @param string $helpText
     * @return mixed
     */
    abstract public function setHelpText(string $helpText);

    /**
     * @return mixed
     */
    abstract public function ask();
}
