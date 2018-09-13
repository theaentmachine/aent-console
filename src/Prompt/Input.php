<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Question\Question;

class Input extends AbstractInput
{
    /** @var null|string */
    protected $default;

    /**
     * @param null|string $default
     * @return self
     */
    public function setDefault(?string $default): self
    {
        $this->default = $default;
        return $this;
    }

    /**
     * @return Question
     */
    protected function build(): Question
    {
        $question = parent::build();
        $message = $question->getQuestion();
        $validator = $question->getValidator();
        if (!empty($this->default)) {
            $message .= ' [' . $this->default . ']: ';
        }
        $question = new Question($message, $this->default);
        $question->setValidator($validator);
        return $question;
    }

    /**
     * @return null|string
     */
    public function run(): ?string
    {
        $question = $this->build();
        do {
            $response = $this->questionHelper->ask($this->input, $this->output, $question);
        } while (!empty($this->helpText) && $response === '?');
        return empty($response) ? null : $response;
    }
}
