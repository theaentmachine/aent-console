<?php

namespace TheAentMachine\Helper;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A helper class to easily create questions.
 */
class Question
{
    /** @var QuestionHelper */
    private $helper;

    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var string */
    private $question;

    /** @var string|null */
    private $default;

    /** @var bool */
    private $compulsory = false;

    /** @var callable|null */
    private $validator;

    /** @var string|null */
    private $helpText;

    /** @var bool  */
    private $yesNoQuestion = false;

    /** @var bool */
    private $choiceQuestion = false;

    /** @var mixed[] */
    private $choices;

    /** @var bool */
    private $multiselectQuestion = false;

    public function __construct(QuestionHelper $helper, InputInterface $input, OutputInterface $output, string $question)
    {
        $this->helper = $helper;
        $this->input = $input;
        $this->output = $output;
        $this->question = $question;
    }

    public function compulsory(): self
    {
        $this->compulsory = true;
        return $this;
    }

    public function setValidator(callable $validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    public function setHelpText(string $helpText): self
    {
        $this->helpText = $helpText;
        return $this;
    }

    public function setDefault(string $default): self
    {
        $this->default = $default;
        return $this;
    }

    public function yesNoQuestion(): self
    {
        $this->yesNoQuestion = true;
        return $this;
    }

    /**
     * @param mixed[] $choices
     * @return string
     */
    public function askSingleChoiceQuestion(array $choices) : string
    {
        $this->choiceQuestion = true;
        $this->choices = $choices;
        $this->multiselectQuestion = false;
        return $this->ask();
    }

    /**
     * @param mixed[] $choices
     * @return string[]
     */
    public function askMultipleChoiceQuestion(array $choices) : array
    {
        $this->choiceQuestion = true;
        $this->choices = $choices;
        $this->multiselectQuestion = true;
        $question = $this->preAsk();
        do {
            $answer = $this->helper->ask($this->input, $this->output, $question);
        } while ($this->helpText !== null && $answer === '?');
        return $answer;
    }

    public function ask(): string
    {
        $question = $this->preAsk();
        do {
            $answer = $this->helper->ask($this->input, $this->output, $question);
        } while ($this->helpText !== null && $answer === '?');

        return $answer;
    }

    /** Format the question text and update the validator */
    private function preAsk(): \Symfony\Component\Console\Question\Question
    {
        $text = $this->question;
        if ($this->helpText) {
            $text .= ' (? for help)';
        }
        if ($this->default) {
            if (!$this->yesNoQuestion) {
                $text .= ' [' . $this->default . ']';
            } elseif ($this->default === 'y') {
                $text .= ' [Y/n]';
            } elseif ($this->default === 'n') {
                $text .= ' [y/N]';
            } else {
                throw new \InvalidArgumentException('Default value must be "y" or "n".');
            }
        } elseif ($this->yesNoQuestion) {
            $text .= ' [y/n]';
        }
        $text .= ': ';
        $this->question = $text;


        $question = $this->choiceQuestion ? new \Symfony\Component\Console\Question\ChoiceQuestion($text, $this->choices, $this->default) : new \Symfony\Component\Console\Question\Question($text, $this->default);

        $validator = $this->validator;

        if ($this->yesNoQuestion) {
            $validator = function (?string $response) use ($validator) {
                $response = $response ?? '';
                $response = \strtolower(trim($response));
                if (!\in_array($response, ['y', 'n', 'yes', 'no'])) {
                    throw new \InvalidArgumentException('Answer must be "y" or "n"');
                }
                $response = \in_array($response, ['y', 'yes']) ? '1' : '';
                return $validator ? $validator($response) : $response;
            };
        }

//        if ($this->choiceQuestion) {
//            $validator = function (?string $response) use ($validator) {
//                $response = $response ?? '';
//                if (!$this->multiselectQuestion) {
//                    $index = (int)$response;
//                    if ($index < 0 || $index >= count($this->choices)) {
//                        throw new \InvalidArgumentException('Answer must be in range');
//                    }
//                    $response = $this->choices[$index];
//                }
//                return $validator ? $validator($response) : $response;
//            };
//        }

        if ($this->helpText !== null) {
            $validator = function (?string $response) use ($validator) {
                $response = $response ?? '';
                if (trim($response) === '?') {
                    $this->output->writeln($this->helpText ?: '');
                    return '?';
                }
                return $validator ? $validator($response) : $response;
            };
        }

        if ($this->compulsory) {
            $validator = function (?string $response) use ($validator) {
                $response = $response ?? '';
                if (trim($response) === '') {
                    throw new \InvalidArgumentException('This field is compulsory.');
                }
                return $validator ? $validator($response) : $response;
            };
        }
        $question->setValidator($validator);
        return $question;
    }
}
