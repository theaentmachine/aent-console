<?php

namespace TheAentMachine\Prompt;

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TheAentMachine\Prompt\Helper\PromptHelper;

final class Prompt
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var QuestionHelper */
    private $questionHelper;

    /** @var FormatterHelper */
    private $formatterHelper;

    /** @var PromptHelper */
    private $promptHelper;

    /**
     * Prompt constructor.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @param FormatterHelper $formatterHelper
     */
    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper, FormatterHelper $formatterHelper)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->formatterHelper = $formatterHelper;
        $this->promptHelper = new PromptHelper($input, $output, $questionHelper);
    }

    /**
     * @param string $text
     * @return void
     */
    public function printBlock(string $text): void
    {
        $this->output->write("\n");
        $this->output->writeln('');
        $this->output->writeln($this->formatterHelper->formatBlock($text, 'block', true));
        $this->output->writeln('');
    }

    /**
     * @param string $text
     * @return void
     */
    public function printAltBlock(string $text): void
    {
        $this->output->write("\n");
        $this->output->writeln('');
        $this->output->writeln($this->formatterHelper->formatBlock($text, 'altblock', false));
        $this->output->writeln('');
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
            ->setCompulsory($compulsory)
            ->setValidator($validator);
        $input
            ->setDefault($default);
        return $input->run();
    }

    /**
     * @param string $text
     * @param string[] $items
     * @param null|string $helpText
     * @param null|string $default
     * @param bool $compulsory
     * @param callable|null $validator
     * @return null|string
     */
    public function autocompleter(string $text, array $items, ?string $helpText = null, ?string $default = null, bool $compulsory = false, ?callable $validator = null): ?string
    {
        $input = new Input($this->input, $this->output, $this->questionHelper);
        $input
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory($compulsory)
            ->setValidator($validator);
        $input
            ->setDefault($default)
            ->setAutocompleterValues($items);
        return $input->run();
    }

    /**
     * @param string $text
     * @param null|string $helpText
     * @param null|bool $default
     * @param bool $compulsory
     * @return bool
     */
    public function confirm(string $text, ?string $helpText = null, ?bool $default = null, bool $compulsory = false): bool
    {
        $confirm = new Confirm($this->input, $this->output, $this->questionHelper);
        $confirm
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory($compulsory);
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
            ->setCompulsory($compulsory)
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
     * @param null|string $default
     * @param bool $compulsory
     * @param callable|null $validator
     * @return null|string[]
     */
    public function multiselect(string $text, array $items, ?string $helpText = null, ?string $default = null, bool $compulsory = false, ?callable $validator = null): ?array
    {
        $select = new Multiselect($this->input, $this->output, $this->questionHelper);
        $select
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory($compulsory)
            ->setValidator($validator);
        $select
            ->setDefault($default);
        $select
            ->setItems($items);
        return $select->run();
    }

    /**
     * @return PromptHelper
     */
    public function getPromptHelper(): PromptHelper
    {
        return $this->promptHelper;
    }
}
