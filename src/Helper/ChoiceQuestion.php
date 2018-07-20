<?php

namespace TheAentMachine\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A helper class to easily create choice questions.
 */
class ChoiceQuestion extends BaseQuestion
{
    /** @var string[] */
    private $choices;

    /** @var bool */
    private $multiselect = false;


    public function __construct(QuestionHelper $helper, InputInterface $input, OutputInterface $output, string $question, array $choices)
    {
        parent::__construct($helper, $input, $output, $question);
        $this->choices = $choices;
    }

    public function setDefault($default): self
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

        return $answer;
    }

    /** @return string[] */
    public function askWithMultipleChoices(): array
    {
        $question = $this->initQuestion(true);

        do {
            $answer = $this->helper->ask($this->input, $this->output, $question);
        } while ($this->helpText !== null && $answer === '?');

        return $answer;
    }

    private function initQuestion(bool $multiselect): \Symfony\Component\Console\Question\ChoiceQuestion
    {
        $text = $this->question;
        if ($this->helpText) {
            $text .= ' (? for help)';
        }
        if ($this->default) {
            $defaultText = (\is_array($this->default) ? implode(', ', $this->default) : $this->default);
            $text .= ' [' . $defaultText . ']';
        }
        $text .= ': ';
        $this->question = $text;

        $question = new \Symfony\Component\Console\Question\ChoiceQuestion($this->question, $this->choices, $this->default);
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
                return $multiselectChoices;
            }

            return current($multiselectChoices);
        };
    }
}
