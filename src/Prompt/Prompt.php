<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Prompt
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var QuestionHelper */
    private $questionHelper;

    /**
     * Prompt constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     */
    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
    }

    /**
     * @param string $text
     * @param null|string $helpText
     * @param null|string $default
     * @param callable|null $validator
     * @return null|string
     */
    public function input(string $text, ?string $helpText = null, ?string $default = null, ?callable $validator = null): ?string
    {
        $input = new Input($this->input, $this->output, $this->questionHelper);
        $input
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(false)
            ->setValidator($validator);
        $input
            ->setDefault($default);
        return $input->run();
    }

    /**
     * @param string $text
     * @param null|string $helpText
     * @param callable|null $validator
     * @return string
     */
    public function compulsoryInput(string $text, ?string $helpText = null, ?callable $validator = null): string
    {
        $input = new Input($this->input, $this->output, $this->questionHelper);
        $input
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(true)
            ->setValidator($validator);
        return $input->run();
    }

    /**
     * @param string $text
     * @param null|string $helpText
     * @param null|bool $default
     * @return bool
     */
    public function confirm(string $text, ?string $helpText = null, ?bool $default = null): bool
    {
        $confirm = new Confirm($this->input, $this->output, $this->questionHelper);
        $confirm
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(false);
        $confirm
            ->setDefault($default);
        return $confirm->run();
    }

    /**
     * @param string $text
     * @param null|string $helpText
     * @return bool
     */
    public function compulsoryConfirm(string $text, ?string $helpText = null): bool
    {
        $confirm = new Confirm($this->input, $this->output, $this->questionHelper);
        $confirm
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(true);
        return $confirm->run();
    }

    /**
     * @param string $text
     * @param mixed[] $items
     * @param null|string $helpText
     * @param null|string $default
     * @param callable|null $validator
     * @return null|string
     */
    public function select(string $text, array $items, ?string $helpText = null, ?string $default = null, ?callable $validator = null): ?string
    {
        $select = new Select($this->input, $this->output, $this->questionHelper);
        $select
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(false)
            ->setValidator($validator);
        $select
            ->setDefault($default);
        $select
            ->setItems($items);
        return $select->run();
    }

    /**
     * @param string $text
     * @param mixed[] $items
     * @param null|string $helpText
     * @param callable|null $validator
     * @return string
     */
    public function compulsorySelect(string $text, array $items, ?string $helpText = null, ?callable $validator = null): string
    {
        $select = new Select($this->input, $this->output, $this->questionHelper);
        $select
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(true)
            ->setValidator($validator);
        $select
            ->setItems($items);
        return $select->run();
    }

    /**
     * @param string $text
     * @param mixed[] $items
     * @param null|string $helpText
     * @param null|string $default
     * @param callable|null $validator
     * @return null|string[]
     */
    public function multiselect(string $text, array $items, ?string $helpText = null, ?string $default = null, ?callable $validator = null): ?array
    {
        $select = new Select($this->input, $this->output, $this->questionHelper);
        $select
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(false)
            ->setValidator($validator);
        $select
            ->setDefault($default);
        $select
            ->setItems($items);
        return $select->run();
    }

    /**
     * @param string $text
     * @param mixed[] $items
     * @param null|string $helpText
     * @param callable|null $validator
     * @return string[]
     */
    public function compulsoryMultiselect(string $text, array $items, ?string $helpText = null, ?callable $validator = null): array
    {
        $select = new Select($this->input, $this->output, $this->questionHelper);
        $select
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(true)
            ->setValidator($validator);
        $select
            ->setItems($items);
        return $select->run();
    }
}
