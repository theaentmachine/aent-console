<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use TheAentMachine\Prompt\Helper\ValidatorHelper;

class Select extends Input
{
    /** @var mixed[] */
    protected $items;

    /** @var bool */
    protected $multiselect = false;

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
        $question = new ChoiceQuestion($message, $this->items, $this->default);
        $question->setValidator(ValidatorHelper::merge($validator, $this->getDefaultValidator()));
        return $question;
    }

    /**
     * Adjusted from Symfony\Component\Console\Question.
     * @return callable
     */
    private function getDefaultValidator(): callable
    {
        $choices = $this->items;
        $multiselect = $this->multiselect;
        $errorMessage = 'Value "%s" is invalid';
        $isAssoc = (bool)\count(\array_filter(\array_keys($choices), '\is_string'));

        return function ($selected) use ($choices, $errorMessage, $multiselect, $isAssoc) {
            // Collapse all spaces.
            $selectedChoices = \str_replace(' ', '', $selected);
            if (!empty($this->helpText) && $selectedChoices === '?') {
                return '?';
            }
            if ($multiselect) {
                // Check for a separated comma values
                if (!\preg_match('/^[^,]+(?:,[^,]+)*$/', $selectedChoices, $matches)) {
                    throw new InvalidArgumentException(\sprintf($errorMessage, $selected));
                }
                $selectedChoices = \explode(',', $selectedChoices);
            } else {
                $selectedChoices = array($selected);
            }
            $multiselectChoices = array();
            foreach ($selectedChoices as $value) {
                $results = array();
                foreach ($choices as $key => $choice) {
                    if ($choice === $value) {
                        $results[] = $key;
                    }
                }
                if (\count($results) > 1) {
                    throw new InvalidArgumentException(\sprintf('The provided answer is ambiguous. Value should be one of %s.', \implode(' or ', $results)));
                }
                $result = \array_search($value, $choices);
                if (!$isAssoc) {
                    if (false !== $result) {
                        $result = $choices[$result];
                    } elseif (isset($choices[$value])) {
                        $result = $choices[$value];
                    }
                } elseif (false === $result && isset($choices[$value])) {
                    $result = $value;
                }
                if (false === $result) {
                    throw new InvalidArgumentException(\sprintf($errorMessage, $value));
                }
                $multiselectChoices[] = (string) $result;
            }
            if ($multiselect) {
                return $multiselectChoices;
            }
            return \current($multiselectChoices);
        };
    }
}
