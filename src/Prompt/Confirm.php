<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Question\Question;
use TheAentMachine\Prompt\Helper\ValidatorHelper;

final class Confirm extends AbstractInput
{
    /** @var null|bool */
    private $default;

    /**
     * @param null|bool $default
     * @return self
     */
    public function setDefault(?bool $default): self
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
            $message .= $this->default === true ? ' [Y/n]: ' : ' [y/]: ';
            $question = new Question($message, $this->default === true ? 'yes' : 'no');
        } else {
            $message .= ' [y/n]: ';
            $question = new Question($message);
        }
        $question->setValidator(ValidatorHelper::merge($validator, $this->boolValidator()));
        return $question;
    }

    /**
     * @return callable|null
     */
    private function boolValidator(): ?callable
    {
        return function (?string $response) {
            $response = $response ?? '';
            $response = \strtolower(trim($response));
            if (!\in_array($response, ['y', 'n', 'yes', 'no'], true)) {
                throw new \InvalidArgumentException('Hey, answer must be "y" or "n"!');
            }
            $response = \in_array($response, ['y', 'yes'], true) ? '1' : '';
            return $response;
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
