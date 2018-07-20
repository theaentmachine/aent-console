<?php

namespace TheAentMachine\Helper;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseQuestion
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

    public function __construct(QuestionHelper $helper, InputInterface $input, OutputInterface $output, string $question)
    {
        $this->helper = $helper;
        $this->input = $input;
        $this->output = $output;
        $this->question = $question;
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
