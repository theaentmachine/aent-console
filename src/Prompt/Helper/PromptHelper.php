<?php

namespace TheAentMachine\Prompt\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TheAentMachine\Aent\Registry\AentItemRegistry;
use TheAentMachine\Aent\Registry\ColonyRegistry;
use TheAentMachine\Aenthill\Pheromone;
use TheAentMachine\Prompt\Input;
use TheAentMachine\Prompt\Select;
use TheAentMachine\Registry\RegistryClient;
use TheAentMachine\Registry\TagsAnalyzer;
use TheAentMachine\Service\Volume\BindVolume;

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
     * @param ColonyRegistry $registry
     * @param string $text
     * @param null|string $helpText
     * @return AentItemRegistry
     */
    public function getFromColonyRegistry(ColonyRegistry $registry, string $text, ?string $helpText = null): AentItemRegistry
    {
        $aents = $registry->getAents();
        $assoc = [];
        foreach ($aents as $aent) {
            $assoc[$aent->getName()] = $aent;
        }
        $items = \array_keys($assoc);
        $items[] = 'Custom';
        $select = new Select($this->input, $this->output, $this->questionHelper);
        $select
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(true);
        $select
            ->setItems($items);
        $response = $select->run();
        if ($response === 'Custom') {
            $image = $this->getDockerHubImage();
            return new AentItemRegistry($image, $image);
        }
        return $assoc[$response];
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
        $image = $dockerHubImageInput->run();
        $version = $this->getVersion($image);
        $aent = $image . ':' . $version;
        return $aent;
    }

    /**
     * @param string $image
     * @return string
     */
    public function getVersion(string $image): string
    {
        $registryClient = new RegistryClient();
        $tags = $registryClient->getImageTagsOnDockerHub($image);
        $tagsAnalyzer = new TagsAnalyzer();
        $proposedTags = $tagsAnalyzer->filterBestTags($tags);
        $default = $proposedTags[0] ?? $tags[0];
        $this->output->writeln("\nLet's choose the version of the <info>$image</info> image!");
        if (!empty($proposedTags)) {
            $this->output->writeln('Possible values include: <info>' . \implode('</info>, <info>', $proposedTags) . '</info>');
        }
        $this->output->writeln('Enter "v" to view all available versions, "?" for help.');
        $question = new Question("Version [$default]: ", $default);
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
        return $version;
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        $input = new Input($this->input, $this->output, $this->questionHelper);
        $text = "\nYour service name";
        $helpText = "The service name is used as an identifier for the container you are creating. It is also bound in Docker internal network DNS and can be used from other containers to reference your container.";
        $input
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(true)
            ->setValidator(ValidatorHelper::getAlphaWithAdditionalCharactersValidator(['_', '.', '-']));
        $response = $input->run();
        return $response ?? '';
    }

    /**
     * @param string $text
     * @param string $target
     * @param null|string $helpText
     * @return BindVolume
     */
    public function getBindVolume(string $text, string $target, ?string $helpText = null): BindVolume
    {
        $input = new Input($this->input, $this->output, $this->questionHelper);
        $input
            ->setText($text)
            ->setHelpText($helpText)
            ->setCompulsory(true)
            ->setValidator(function (string $dir) {
                $dir = trim($dir, '/') ?: '.';
                $rootDir = Pheromone::getContainerProjectDirectory();
                $fullDir = $rootDir.'/'.$dir;
                if (!is_dir($fullDir)) {
                    mkdir($fullDir);
                }
                return $dir;
            });
        $source = $input->run() ?? '';
        return new BindVolume($source, $target);
    }

    /**
     * @param string $serviceName
     * @param int $port
     * @param string $baseVirtualHost
     * @return string
     */
    public function getSubdomain(string $serviceName, int $port, string $baseVirtualHost): string
    {
        $input = new Input($this->input, $this->output, $this->questionHelper);
        $text = sprintf(
            "\nSubdomain for your service <info>%s</info> on port <info>%s</info> (relative to <info>%s</info>)",
            $serviceName,
            $port,
            $baseVirtualHost
        );
        $input
            ->setText($text)
            ->setCompulsory(true)
            ->setValidator(ValidatorHelper::getAlphaWithAdditionalCharactersValidator(['-']));
        return $input->run() ?? '';
    }
}
