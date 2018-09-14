<?php

namespace TheAentMachine\Prompt\Helper;

use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TheAentMachine\Prompt\Input;
use TheAentMachine\Registry\RegistryClient;
use TheAentMachine\Registry\TagsAnalyzer;

final class PromptHelper
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var QuestionHelper */
    private $questionHelper;

    /**
     * PromptHelper constructor.
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
     * @return string
     */
    public function getDockerHubImage(): string
    {
        $dockerHubImageInput = new Input($this->input, $this->output, $this->questionHelper);
        $dockerHubImageInput
            ->setText("\nThe docker image of your custom aent (without tag)")
            ->setCompulsory(true)
            ->setValidator(ValidatorHelper::getDockerImageWithoutTagValidator());
        $registryClient = new RegistryClient();
        do {
            $image = $dockerHubImageInput->run();
            try {
                $tags = $registryClient->getImageTagsOnDockerHub($image);
            } catch (RequestException $e) {
                $tags = [];
            }
            if (empty($tags)) {
                $this->output->writeln("\nThe image <info>$image</info> does not seem to exist on Docker Hub. Try again!");
            }
        } while (empty($tags));
        $tagsAnalyzer = new TagsAnalyzer();
        $proposedTags = $tagsAnalyzer->filterBestTags($tags);
        $default = $proposedTags[0] ?? $tags[0];
        $this->output->writeln("\nGreat! You may now choose your <info>$image</info> image version.");
        if (!empty($proposedTags)) {
            $this->output->writeln('Possible values include: <info>' . \implode('</info>, <info>', $proposedTags) . '</info>');
        }
        $this->output->writeln('Enter "v" to view all available versions, "?" for help');
        $question = new Question("Your <info>$image</info> image version [$default]: ", $default);
        $question->setAutocompleterValues($tags);
        $question->setValidator(function (string $response) use ($tags, $image) {
            $response = \trim($response);
            if ($response === 'v') {
                $this->output->writeln('Available versions: <info>' . \implode('</info>, <info>', $tags) . '</info>');
                return 'v';
            }
            if ($response === '?') {
                $this->output->writeln("Please choose the version (i.e. the tag) of the <info>$image</info> image you are about to install. Press 'v' to view the list of available tags.");
                return '?';
            }
            if (!\in_array($response, $tags, true)) {
                throw new InvalidArgumentException("Version \"$response\" is invalid.");
            }
            return $response;
        });
        do {
            $version = $this->questionHelper->ask($this->input, $this->output, $question);
        } while ($version === 'v' || $version === '?');
        $aent = $image . ':' . $version;
        $this->output->writeln("\n👌 Alright, I'm going to use <info>$aent</info>!");
        return $aent;
    }
}
