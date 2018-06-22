<?php


namespace TheAentMachine;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A helper class for the most common questions asked in the console.
 */
class AentHelper
{
    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var QuestionHelper
     */
    private $questionHelper;
    /**
     * @var FormatterHelper
     */
    private $formatterHelper;

    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper, FormatterHelper $formatterHelper)
    {

        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->formatterHelper = $formatterHelper;
    }

    private function registerStyle(): void
    {
        $outputStyle = new OutputFormatterStyle('black', 'cyan', ['bold']);
        $this->output->getFormatter()->setStyle('title', $outputStyle);
    }

    /**
     * Displays text in a big block
     */
    public function title(string $title): void
    {
        $this->registerStyle();
        $this->formatterHelper->formatBlock($title, 'title', true);
    }

    /**
     * Displays text in a small block
     */
    public function subTitle(string $title): void
    {
        $this->registerStyle();
        $this->formatterHelper->formatBlock($title, 'title', false);
    }
}
