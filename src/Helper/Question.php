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
    /**
     * @var QuestionHelper
     */
    private $helper;
    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var string
     */
    private $question;
    /**
     * @var string|null
     */
    private $default;
    /**
     * @var bool
     */
    private $compulsory = false;
    /**
     * @var callable
     */
    private $validator;
    /**
     * @var string|null
     */
    private $helpText;
    /**
     * @var bool
     */
    private $yesNoQuestion = false;

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

    public function ask(): string
    {
        $text = $this->question;
        if ($this->helpText) {
            $text .= ' (? for help)';
        }
        if ($this->default) {
            if (!$this->yesNoQuestion) {
                $text .= ' ['.$this->default.']';
            } elseif ($this->default === 'y') {
                $text .= ' [Y/n]';
            } elseif ($this->default === 'n') {
                $text .= ' [y/N]';
            } else {
                $text .= ' [y/n]';
            }
        }
        $text .= ': ';

        $question = new \Symfony\Component\Console\Question\Question($text, $this->default);

        $validator = $this->validator;

        if ($this->yesNoQuestion) {
            $validator = function (?string $response) use ($validator) {
                $response = trim(\strtolower($response));
                if (!\in_array($response, ['y', 'n', 'yes', 'no'])) {
                    throw new \InvalidArgumentException('Answer must be "y" or "n"');
                }
                $response = \in_array($response, ['y', 'yes']) ? '1' : '';
                return $validator ? $validator($response) : $response;
            };
        }

        if ($this->helpText !== null) {
            $validator = function (?string $response) use ($validator) {
                if (trim($response) === '?') {
                    $this->output->writeln($this->helpText ?: '');
                    return '?';
                }
                return $validator ? $validator($response) : $response;
            };
        }

        if ($this->compulsory) {
            $validator = function (?string $response) use ($validator) {
                if (trim($response) === '') {
                    throw new \InvalidArgumentException('This field is compulsory.');
                }
                return $validator ? $validator($response) : $response;
            };
        }

        $question->setValidator($validator);

        do {
            $answer = $this->helper->ask($this->input, $this->output, $question);
        } while ($this->helpText !== null && $answer === '?');

        return $answer;
    }
}
