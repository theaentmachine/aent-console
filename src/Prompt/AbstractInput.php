<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TheAentMachine\Prompt\Helper\ValidatorHelper;

abstract class AbstractInput
{
    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var QuestionHelper */
    protected $questionHelper;

    /** @var string */
    protected $text;

    /** @var null|string */
    protected $helpText;

    /** @var bool */
    private $compulsory;

    /** @var callable|null */
    protected $validator;

    /**
     * AbstractInput constructor.
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

    /**
     * @return Question
     */
    protected function build(): Question
    {
        $message = $this->text;
        if (!empty($this->helpText)) {
            $message .= ' (? for help)';
        }
        $question = new Question($message);
        $validator = ValidatorHelper::merge($this->helpTextValidator(), $this->compulsoryValidator());
        $validator = ValidatorHelper::merge($validator, $this->validator);
        $question->setValidator($validator);
        return $question;
    }

    /**
     * @return callable|null
     */
    private function helpTextValidator(): ?callable
    {
        if (!empty($this->helpText)) {
            return function (?string $response) {
                $response = $response ?? '';
                if (\trim($response) === '?') {
                    $this->output->writeln($this->helpText ?: '');
                    return '?';
                }
                return $response;
            };
        }
        return null;
    }

    /**
     * @return callable|null
     */
    private function compulsoryValidator(): ?callable
    {
        if ($this->compulsory && empty($this->default)) {
            return function (?string $response) {
                $response = $response ?? '';
                if (\trim($response) === '') {
                    throw new \InvalidArgumentException('Hey, this field is compulsory!');
                }
                return $response;
            };
        }
        return function (?string $response) {
            $response = $response ?? '';
            if (\trim($response) === '') {
                return $response;
            }
            return $response;
        };
    }

    /**
     * @return mixed
     */
    abstract public function run();

    /**
     * @param string $text
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param null|string $helpText
     * @return self
     */
    public function setHelpText(?string $helpText): self
    {
        $this->helpText = $helpText;
        return $this;
    }

    /**
     * @param bool $compulsory
     * @return self
     */
    public function setCompulsory(bool $compulsory): self
    {
        $this->compulsory = $compulsory;
        return $this;
    }

    /**
     * @param callable|null $validator
     * @return self
     */
    public function setValidator(?callable $validator): self
    {
        $this->validator = $validator;
        return $this;
    }
}
