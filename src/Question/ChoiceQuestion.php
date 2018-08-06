<?php

namespace TheAentMachine\Question;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion as SymfonyChoiceQuestion;

/**
 * A helper class to easily create choice questions.
 */
final class ChoiceQuestion extends AbstractQuestion
{
    /** @var string[] */
    private $choices;

    /** @var bool */
    private $multiselect = false;


    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $helper, string $question, array $choices, bool $printAnswer = true)
    {
        parent::__construct($input, $output, $helper, $question, $printAnswer);
        $this->choices = $choices;
    }

    public function setDefault(string $default): self
    {
        $this->default = $default;
        return $this;
    }

    public function setHelpText(string $helpText): self
    {
        $this->helpText = $helpText;
        return $this;
    }

    public function ask(): string
    {
        $question = $this->initQuestion(false);
        do {
            $answer = $this->helper->ask($this->input, $this->output, $question);
        } while ($this->helpText !== null && $answer === '?');

        return \is_string($answer)? $answer: (string)$answer;
    }

    /** @return string[] */
    public function askWithMultipleChoices(): array
    {
        $question = $this->initQuestion(true);

        do {
            $answer = $this->helper->ask($this->input, $this->output, $question);
        } while ($this->helpText !== null && $answer === '?');

        return \is_array($answer) ? $answer : [\is_string($answer) ? $answer : (string)$answer];
    }

    private function initQuestion(bool $multiselect): SymfonyChoiceQuestion
    {
        $text = $this->question;
        if (null !== $this->helpText) {
            $text .= ' (? for help)';
        }
        if (null !== $this->default) {
            $text .= ' [' . $this->default . ']';
        }
        $text .= ': ';
        $this->question = $text;

        $question = new SymfonyChoiceQuestion($this->question, $this->choices, $this->default);
        $this->multiselect = $multiselect;
        $question->setMultiselect($this->multiselect);
        $question->setValidator($this->getDefaultValidator());
        return $question;
    }

    /** Greatly inspired from the default Symfony's ChoiceQuestion validator*/
    private function getDefaultValidator(): callable
    {
        $choices = $this->choices;
        $errorMessage = 'Value "%s" is invalid';
        $multiselect = $this->multiselect;
        $isAssoc = (bool)count(array_filter(array_keys($this->choices), '\is_string'));

        return function ($selected) use ($choices, $errorMessage, $multiselect, $isAssoc) {

            // Collapse all spaces.
            $selectedChoices = str_replace(' ', '', $selected);

            if ($this->helpText !== null && $selectedChoices === '?') {
                $this->output->writeln($this->helpText ?: '');
                return '?';
            }

            if ($multiselect) {
                // Check for a separated comma values
                if (!preg_match('/^[^,]+(?:,[^,]+)*$/', $selectedChoices, $matches)) {
                    throw new InvalidArgumentException(sprintf($errorMessage, $selected));
                }
                $selectedChoices = explode(',', $selectedChoices);
            } else {
                $selectedChoices = array($selected);
            }

            $multiselectChoices = array();
            foreach ($selectedChoices as $value) {
                $results = array();
                foreach ($choices as $key => $choice) {
                    if ($choice === $value) {
                        $results[] = $key;
                    }
                }

                if (count($results) > 1) {
                    throw new InvalidArgumentException(sprintf('The provided answer is ambiguous. Value should be one of %s.', implode(' or ', $results)));
                }

                $result = array_search($value, $choices, true);

                if (!$isAssoc) {
                    if (false !== $result) {
                        $result = $choices[$result];
                    } elseif (isset($choices[$value])) {
                        $result = $choices[$value];
                    }
                } elseif (false === $result && isset($choices[$value])) {
                    $result = $value;
                }

                if (false === $result) {
                    throw new InvalidArgumentException(sprintf($errorMessage, $value));
                }
                $multiselectChoices[] = (string)$result;
            }
            if ($multiselect) {
                if ($this->printAnswer) {
                    $this->output->writeln('<info>You selected: ' . \implode(', ', $multiselectChoices) . '</info>');
                    $this->spacer();
                }
                return $multiselectChoices;
            }

            $answer = current($multiselectChoices);
            if ($this->printAnswer) {
                $this->output->writeln("<info>You selected: $answer</info>");
            }
            $this->spacer();

            return $answer;
        };
    }
}
