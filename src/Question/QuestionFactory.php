<?php

namespace TheAentMachine\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class QuestionFactory
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var QuestionHelper */
    private $questionHelper;

    /**
     * QuestionFactory constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     */
    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
    }

    public function question(string $question, bool $printAnswer = true): Question
    {
        return new Question($this->input, $this->output, $this->questionHelper, $question, $printAnswer);
    }

    /**
     * @param string $question
     * @param string[] $choices
     * @param bool $printAnswer
     * @return ChoiceQuestion
     */
    public function choiceQuestion(string $question, array $choices, bool $printAnswer = true): ChoiceQuestion
    {
        return new ChoiceQuestion($this->input, $this->output, $this->questionHelper, $question, $choices, $printAnswer);
    }
}
