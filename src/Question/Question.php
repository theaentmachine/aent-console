<?php


namespace TheAentMachine\Question;

use Symfony\Component\Console\Question\Question as SymfonyQuestion;

/**
 * A helper class to easily create questions.
 */
final class Question extends AbstractQuestion
{
    /** @var bool */
    private $compulsory = false;

    /** @var callable|null */
    private $validator;

    /** @var bool */
    private $yesNoQuestion = false;

    public function setDefault(string $default): self
    {
        $this->default = $default;
        return $this;
    }

    public function setHelpText(string $helpText): self
    {
        $this->helpText = $helpText;
        return $this;
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
        if (null !== $this->default && '' !== $this->default) {
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

        $question = new SymfonyQuestion($text, $this->default);

        $validator = $this->validator;

        if ($this->yesNoQuestion) {
            $validator = function (?string $response) use ($validator) {
                $response = $response ?? '';
                $response = \strtolower(trim($response));
                if (!\in_array($response, ['y', 'n', 'yes', 'no'], true)) {
                    throw new \InvalidArgumentException('Answer must be "y" or "n"');
                }
                $response = \in_array($response, ['y', 'yes'], true) ? '1' : '';
                return $validator ? $validator($response) : $response;
            };
        }

        if (null !== $this->helpText) {
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
        } else {
            $validator = function (?string $response) use ($validator) {
                $response = $response ?? '';
                if (trim($response) === '') {
                    return $response;
                }
                return $validator ? $validator($response) : $response;
            };
        }

        $question->setValidator($validator);

        do {
            $answer = $this->helper->ask($this->input, $this->output, $question);
        } while ($this->helpText !== null && $answer === '?');

        if ($this->printAnswer) {
            if ($this->yesNoQuestion) {
                $answerStr = \in_array($answer, ['y', 'yes', '1'], true) ? 'yes' : 'no';
            } else {
                $answerStr = $answer;
            }
            $this->output->writeln("<info>Your answer: $answerStr</info>");
        }
        $this->spacer();

        return \is_string($answer)? $answer: (string)$answer;
    }
}
