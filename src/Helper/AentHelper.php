<?php


namespace TheAentMachine\Helper;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TheAentMachine\Question\CommonQuestions;
use TheAentMachine\Question\QuestionFactory;
use TheAentMachine\Question\Question;
use TheAentMachine\Question\ChoiceQuestion;

/**
 * A helper class for the most common questions asked in the console.
 */
final class AentHelper
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var QuestionHelper */
    private $questionHelper;

    /** @var FormatterHelper */
    private $formatterHelper;

    /** @var QuestionFactory */
    private $factory;

    /** @var CommonQuestions */
    private $commonQuestions;


    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper, FormatterHelper $formatterHelper)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->formatterHelper = $formatterHelper;
        $this->factory = new QuestionFactory($input, $output, $questionHelper);
        $this->commonQuestions = new CommonQuestions($input, $output, $questionHelper);
    }

    private function registerStyle(): void
    {
        $outputStyle = new OutputFormatterStyle('black', 'cyan', ['bold']);
        $this->output->getFormatter()->setStyle('title', $outputStyle);
    }

    /**
     * Displays text in a big block
     */
    public function title(string $title): void
    {
        $this->registerStyle();
        $this->spacer();
        $this->output->writeln($this->formatterHelper->formatBlock($title, 'title', true));
        $this->spacer();
    }

    /**
     * Displays text in a small block
     */
    public function subTitle(string $title): void
    {
        $this->registerStyle();
        $this->output->writeln($this->formatterHelper->formatBlock($title, 'title', false));
    }

    public function spacer(): void
    {
        $this->output->writeln('');
    }

    public function question(string $question, bool $printAnswer = true): Question
    {
        return $this->factory->question($question, $printAnswer);
    }

    /**
     * @param string $question
     * @param string[] $choices
     * @param bool $printAnswer
     * @return ChoiceQuestion
     */
    public function choiceQuestion(string $question, array $choices, bool $printAnswer = true): ChoiceQuestion
    {
        return $this->factory->choiceQuestion($question, $choices, $printAnswer);
    }

    /**
     * @return CommonQuestions
     */
    public function getCommonQuestions(): CommonQuestions
    {
        return $this->commonQuestions;
    }
}
