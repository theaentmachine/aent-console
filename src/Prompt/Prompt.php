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
     * @param bool $compulsory
     * @param callable|null $validator
     * @return null|string
     */
    public function input(string $text, ?string $helpText = null, ?string $default = null, bool $compulsory = false, ?callable $validator = null): ?string
    {
        $input = new Input($this->input, $this->output, $this->questionHelper);
        $input
            ->setText($text)
            ->setHelpText($helpText)
            ->setValidator($validator);
        $input
            ->setDefault($default)
            ->setCompulsory($compulsory);
        return $input->run();
    }

    /**
     * @param string $text
     * @param null|string $helpText
     * @param bool $default
     * @return bool
     */
    public function confirm(string $text, ?string $helpText = null, bool $default = true): bool
    {
        $confirm = new Confirm($this->input, $this->output, $this->questionHelper);
        $confirm
            ->setText($text)
            ->setHelpText($helpText);
        $confirm
            ->setDefault($default);
        return $confirm->run();
    }

    /**
     * @param string $text
     * @param mixed[] $items
     * @param null|string $helpText
     * @param null|string $default
     * @param bool $compulsory
     * @param callable|null $validator
     * @return null|string
     */
    public function select(string $text, array $items, ?string $helpText = null, ?string $default = null, bool $compulsory = false, ?callable $validator = null): ?string
    {
        $select = new Select($this->input, $this->output, $this->questionHelper);
        $select
            ->setText($text)
            ->setHelpText($helpText)
            ->setValidator($validator);
        $select
            ->setDefault($default)
            ->setCompulsory($compulsory);
        $select
            ->setItems($items);
        return $select->run();
    }
}
