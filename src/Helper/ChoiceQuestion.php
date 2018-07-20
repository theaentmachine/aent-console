<?php

namespace TheAentMachine\Helper;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion as SymfonyChoiceQuestion;

/**
 * A helper class to easily create choice questions.
 */
class ChoiceQuestion extends BaseQuestion
{
    /** @var string[] */
    private $choices;

    public function __construct(QuestionHelper $helper, InputInterface $input, OutputInterface $output, string $question, array $choices)
    {
        parent::__construct($helper, $input, $output, $question);
        $this->choices = $choices;
    }

    /**
     * @return string
     */
    public function askSingleChoiceQuestion(): string
    {
        $question = new SymfonyChoiceQuestion($this->question, $this->choices);
        return $this->helper->ask($this->input, $this->output, $question);
    }

    /**
     * @return string[]
     */
    public function askMultipleChoiceQuestion(): array
    {
        $question = new SymfonyChoiceQuestion($this->question, $this->choices);
        $question->setMultiselect(true);
        return $this->helper->ask($this->input, $this->output, $question);
    }
}
