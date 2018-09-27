<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Question\Question;

class Input extends AbstractInput
{
    /** @var null|string */
    protected $default;

    /** @var string[] */
    private $autocompleterValues;

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
     * @param string[] $autocompleterValues
     * @return self
     */
    public function setAutocompleterValues(array $autocompleterValues): self
    {
        $this->autocompleterValues = $autocompleterValues;
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
        } else {
            $message .= ': ';
        }
        $question = new Question($message, $this->default);
        $question->setValidator($validator);
        if (!empty($this->autocompleterValues)) {
            $question->setAutocompleterValues($this->autocompleterValues);
        }
        return $question;
    }

    /**
     * @return null|mixed
     */
    public function run()
    {
        $question = $this->build();
        do {
            $response = $this->questionHelper->ask($this->input, $this->output, $question);
        } while (!empty($this->helpText) && $response === '?');
        return empty($response) ? null : $response;
    }
}
