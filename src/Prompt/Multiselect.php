<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

final class Multiselect extends Select
{
    /**
     * @return Question
     */
    protected function build(): Question
    {
        $this->multiselect = true;
        $question = parent::build();
        $message = $question->getQuestion();
        $validator = $question->getValidator();
        $question = new ChoiceQuestion($message, $this->items, $this->default);
        $question->setValidator($validator);
        $question->setMultiselect(true);
        return $question;
    }
}
