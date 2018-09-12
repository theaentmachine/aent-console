<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Question\Question;

final class Confirm extends AbstractInput
{
    /** @var bool */
    private $default;

    /**
     * @param bool $default
     * @return self
     */
    public function setDefault(bool $default): self
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
        $message .= $this->default ? ' [Y/n]:' : ' [y/]:';
        $question = new Question($message, $this->default ? 'yes' : 'no');
        $question->setValidator($this->boolValidator($validator));
        return $question;
    }

    /**
     * @param callable|null $validator
     * @return callable|null
     */
    private function boolValidator(?callable $validator): ?callable
    {
        return function (?string $response) use ($validator) {
            $response = $response ?? '';
            $response = \strtolower(trim($response));
            if (!\in_array($response, ['y', 'n', 'yes', 'no'], true)) {
                throw new \InvalidArgumentException('Hey, answer must be "y" or "n"!');
            }
            $response = \in_array($response, ['y', 'yes'], true) ? '1' : '';
            return $validator ? $validator($response) : $response;
        };
    }

    /**
     * @return bool
     */
    public function run(): bool
    {
        $question = $this->build();
        do {
            $response = $this->questionHelper->ask($this->input, $this->output, $question);
        } while (!empty($this->helpText) && $response === '?');
        return !empty($response);
    }
}
