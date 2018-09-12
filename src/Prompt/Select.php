<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class Select extends Input
{
    /** @var string[] */
    private $items;

    /**
     * @param string[] $items
     * @return self
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
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
        $question = new ChoiceQuestion($message, $this->items, $this->default);
        $question->setValidator($validator);
        return $question;
    }
}
