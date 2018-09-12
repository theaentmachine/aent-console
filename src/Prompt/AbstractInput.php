<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

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
        $question->setValidator($this->helpTextValidator());
        return $question;
    }

    /**
     * @return callable|null
     */
    private function helpTextValidator(): ?callable
    {
        $validator = $this->validator;
        if (!empty($this->helpText)) {
            return function (?string $response) use ($validator) {
                $response = $response ?? '';
                if (\trim($response) === '?') {
                    $this->output->writeln($this->helpText ?: '');
                    return '?';
                }
                return $validator ? $validator($response) : $response;
            };
        }
        return null;
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
     * @param callable|null $validator
     * @return self
     */
    public function setValidator(?callable $validator): self
    {
        $this->validator = $validator;
        return $this;
    }
}
