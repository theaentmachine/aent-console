<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Question\Question;

class Input extends AbstractInput
{
    /** @var null|string */
    protected $default;

    /** @var bool */
    private $compulsory;

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
     * @param bool $compulsory
     * @return self
     */
    public function setCompulsory(bool $compulsory): self
    {
        $this->compulsory = $compulsory;
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
            $message .= ' [' . $this->default . ']:';
        }
        $question = new Question($message, $this->default);
        $question->setValidator($this->compulsoryValidator($validator));
        return $question;
    }

    /**
     * @param callable|null $validator
     * @return callable|null
     */
    private function compulsoryValidator(?callable $validator): ?callable
    {
        if ($this->compulsory && empty($this->default)) {
            return function (?string $response) use ($validator) {
                $response = $response ?? '';
                if (\trim($response) === '') {
                    throw new \InvalidArgumentException('Hey, this field is compulsory!');
                }
                return $validator ? $validator($response) : $response;
            };
        }
        return function (?string $response) use ($validator) {
            $response = $response ?? '';
            if (\trim($response) === '') {
                return $response;
            }
            return $validator ? $validator($response) : $response;
        };
    }

    /**
     * @return string
     */
    public function run(): string
    {
        $question = $this->build();
        do {
            $response = $this->questionHelper->ask($this->input, $this->output, $question);
        } while (!empty($this->helpText) && $response === '?');
        return empty($response) ? null : $response;
    }
}
