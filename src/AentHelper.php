<?php


namespace TheAentMachine;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TheAentMachine\Registry\RegistryClient;
use TheAentMachine\Registry\TagsAnalyzer;

/**
 * A helper class for the most common questions asked in the console.
 */
class AentHelper
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var QuestionHelper */
    private $questionHelper;

    /** @var FormatterHelper */
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
        $this->output->writeln($this->formatterHelper->formatBlock($title, 'title', true));
    }

    /**
     * Displays text in a small block
     */
    public function subTitle(string $title): void
    {
        $this->registerStyle();
        $this->output->writeln($this->formatterHelper->formatBlock($title, 'title', false));
    }

    public function askForServiceName(string $serviceName, string $applicationName = ''): string
    {
        $answer = $this->question("$applicationName service name")
            ->setDefault($serviceName)
            ->compulsory()
            ->setHelpText('The "service name" is used as an identifier for the container you are creating. It is also bound in Docker internal network DNS and can be used from other containers to reference your container.')
            ->setValidator(function (string $value) {
                $value = trim($value);
                if (!\preg_match('/^[a-zA-Z0-9_.-]+$/', $value)) {
                    throw new \InvalidArgumentException('Invalid service name "' . $value . '". Service names can contain alphanumeric characters, and "_", ".", "-".');
                }
                return $value;
            })
            ->ask();

        $this->output->writeln("<info>Service name: $answer</info>");
        $this->spacer();

        return $answer;
    }

    public function spacer(): void
    {
        $this->output->writeln('');
    }

    public function askForTag(string $dockerHubImage, string $applicationName = ''): string
    {
        $registryClient = new RegistryClient();
        $availableVersions = $registryClient->getImageTagsOnDockerHub($dockerHubImage);

        $tagsAnalyzer = new TagsAnalyzer();
        $proposedTags = $tagsAnalyzer->filterBestTags($availableVersions);
        $default = $proposedTags[0] ?? null;
        $this->output->writeln("Please choose your $applicationName version.");
        if (!empty($proposedTags)) {
            $this->output->writeln('Possible values include: <info>' . \implode('</info>, <info>', $proposedTags) . '</info>');
        }
        $this->output->writeln('Enter "v" to view all available versions, "?" for help');
        $question = new Question(
            "Select your $applicationName version [$default]: ",
            $default
        );
        $question->setAutocompleterValues($availableVersions);
        $question->setValidator(function (string $value) use ($availableVersions, $dockerHubImage) {
            $value = trim($value);

            if ($value === 'v') {
                $this->output->writeln('Available versions: <info>' . \implode('</info>, <info>', $availableVersions) . '</info>');
                return 'v';
            }

            if ($value === '?') {
                $this->output->writeln("Please choose the version (i.e. the tag) of the $dockerHubImage image you are about to install. Press 'v' to view the list of available tags.");
                return '?';
            }

            if (!\in_array($value, $availableVersions)) {
                throw new \InvalidArgumentException("Version '$value' is invalid.");
            }

            return $value;
        });
        do {
            $version = $this->questionHelper->ask($this->input, $this->output, $question);
        } while ($version === 'v' || $version === '?');

        $this->output->writeln("<info>Selected version: $version</info>");
        $this->spacer();

        return $version;
    }

    public function question(string $question): \TheAentMachine\Helper\Question
    {
        return new \TheAentMachine\Helper\Question($this->questionHelper, $this->input, $this->output, $question);
    }

    /**
     * @param string[] $choices
     * @return Helper\ChoiceQuestion
     */
    public function choiceQuestion(string $question, array $choices): \TheAentMachine\Helper\ChoiceQuestion
    {
        return new \TheAentMachine\Helper\ChoiceQuestion($this->questionHelper, $this->input, $this->output, $question, $choices);
    }

    public function setEnvType(): string
    {
        $envType = $this->choiceQuestion('Select your environment type', [Metadata::ENV_TYPE_DEV, Metadata::ENV_TYPE_TEST, Metadata::ENV_TYPE_PROD])
            ->askSingleChoiceQuestion();
        $this->output->writeln("<info>Selected environment type: $envType</info>");
        $this->spacer();

        Manifest::addMetadata(Metadata::ENV_TYPE_KEY, $envType);
        return $envType;
    }

    /**
     * @return string
     * @throws Exception\MissingEnvironmentVariableException
     */
    public function registerCI(): string
    {
        $ci = $this->choiceQuestion('Select your reverse proxy:', ['gitlab-ci', 'travis-ci', 'circle-ci'])
            ->askSingleChoiceQuestion();
        $this->output->writeln("<info>Your CI: $ci</info>");
        $this->spacer();

        Manifest::addDependency("theaentmachine/aent-$ci", Metadata::CI_KEY, [
            Metadata::ENV_NAME_KEY => Manifest::getMetadata(Metadata::ENV_NAME_KEY),
            Metadata::ENV_TYPE_KEY => Manifest::getMetadata(Metadata::ENV_TYPE_KEY)
        ]);

        return Manifest::getDependency(Metadata::CI_KEY);
    }

    /**
     * @return string
     * @throws Exception\MissingEnvironmentVariableException
     */
    public function registerReverseProxy(): string
    {
        $reverseProxy = $this->choiceQuestion('Select your reverse proxy:', ['traefik', 'nginx', 'ingress'])
            ->askSingleChoiceQuestion();
        $this->output->writeln("<info>Your reverse proxy: $reverseProxy</info>");
        $this->spacer();

        Manifest::addDependency("theaentmachine/aent-$reverseProxy", Metadata::REVERSE_PROXY_KEY, [
            Metadata::ENV_NAME_KEY => Manifest::getMetadata(Metadata::ENV_NAME_KEY),
            Metadata::ENV_TYPE_KEY => Manifest::getMetadata(Metadata::ENV_TYPE_KEY)
        ]);

        return Manifest::getDependency(Metadata::REVERSE_PROXY_KEY);
    }
}
