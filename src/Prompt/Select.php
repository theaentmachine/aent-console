<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use TheAentMachine\Prompt\Helper\ValidatorHelper;

class Select extends Input
{
    /** @var mixed[] */
    private $items;

    /**
     * @param mixed[] $items
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
        if (!empty($this->helpText)) {
            $this->items['?'] = 'Help';
        }
        $question = new ChoiceQuestion($message, $this->items, $this->default);
        $question->setValidator(ValidatorHelper::merge($validator, $question->getValidator()));
        return $question;
    }
}
