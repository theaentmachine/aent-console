<?php


namespace TheAentMachine\Question;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TheAentMachine\Aenthill\Aenthill;
use TheAentMachine\Aenthill\CommonAents;
use TheAentMachine\Aenthill\CommonDependencies;
use TheAentMachine\Aenthill\CommonEvents;
use TheAentMachine\Aenthill\CommonMetadata;
use TheAentMachine\Aenthill\Manifest;
use TheAentMachine\Exception\CommonAentsException;
use TheAentMachine\Exception\ManifestException;
use TheAentMachine\Registry\RegistryClient;
use TheAentMachine\Registry\TagsAnalyzer;
use Symfony\Component\Console\Question\Question as SymfonyQuestion;

final class CommonQuestions
{
    /** @var InputInterface */
    private $input;

    /** @var OutputInterface */
    private $output;

    /** @var QuestionHelper */
    private $questionHelper;

    /** @var QuestionFactory */
    private $factory;

    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->factory = new QuestionFactory($input, $output, $questionHelper);
    }

    public function spacer(): void
    {
        $this->output->writeln('');
    }

    public function askForDockerImageTag(string $dockerHubImage, string $applicationName = ''): string
    {
        $registryClient = new RegistryClient();
        $availableVersions = $registryClient->getImageTagsOnDockerHub($dockerHubImage);

        $tagsAnalyzer = new TagsAnalyzer();
        $proposedTags = $tagsAnalyzer->filterBestTags($availableVersions);
        $default = $proposedTags[0] ?? $availableVersions[0];

        $this->output->writeln("Please choose your $applicationName version.");

        if (!empty($proposedTags)) {
            $this->output->writeln('Possible values include: <info>' . \implode('</info>, <info>', $proposedTags) . '</info>');
        }
        $this->output->writeln('Enter "v" to view all available versions, "?" for help');

        $question = new SymfonyQuestion("Select your $applicationName version [$default]: ", $default);

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

            if (!\in_array($value, $availableVersions, true)) {
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

    public function askForServiceName(string $serviceName, string $applicationName = ''): string
    {
        $answer = $this->factory->question("$applicationName service name")
            ->setDefault($serviceName)
            ->compulsory()
            ->setHelpText('The "service name" is used as an identifier for the container you are creating. It is also bound in Docker internal network DNS and can be used from other containers to reference your container.')
            ->setValidator(CommonValidators::getAlphaValidator(['_', '.', '-']))
            ->ask();
        $this->spacer();
        return $answer;
    }

    /**
     * @return mixed[]|null
     * @throws CommonAentsException
     */
    public function askForEnvironments(): ?array
    {
        $environments = \array_unique(Aenthill::dispatchJson(CommonEvents::ENVIRONMENT_EVENT, []), SORT_REGULAR);

        if (empty($environments)) {
            $this->output->writeln('<error>No environments available.</error>');
            $this->output->writeln('Did you forget to install an orchestrator?');
            $this->output->writeln('<info>Available orchestrators:</info> ' . implode(', ', CommonAents::getAentsListByDependencyKey(CommonDependencies::ORCHESTRATOR_KEY)));
            exit(1);
        }

        $environmentsStr = [];
        foreach ($environments as $env) {
            $environmentsStr[] = $env[CommonMetadata::ENV_NAME_KEY] . ' (of type '. $env[CommonMetadata::ENV_TYPE_KEY]  .')';
        }

        $chosen = $this->factory->choiceQuestion('Environments', $environmentsStr, false)
            ->askWithMultipleChoices();

        $this->output->writeln('<info>Environments: ' . \implode($chosen, ', ') . '</info>');
        $this->spacer();

        $results = [];
        foreach ($chosen as $c) {
            $results[] = $environments[\array_search($c, $environmentsStr, true)];
        }

        return $results;
    }

    public function askForEnvType(): string
    {
        $envType = $this->factory->choiceQuestion('Environment type', [CommonMetadata::ENV_TYPE_DEV, CommonMetadata::ENV_TYPE_TEST, CommonMetadata::ENV_TYPE_PROD])
            ->ask();
        $this->spacer();
        Manifest::addMetadata(CommonMetadata::ENV_TYPE_KEY, $envType);

        return $envType;
    }

    public function askForEnvName(?string $envType): string
    {
        $question = $this->factory->question('Environment name')
            ->compulsory()
            ->setValidator(CommonValidators::getAlphaValidator(['_', '.', '-']));

        if (null !== $envType) {
            $question->setDefault(\strtolower($envType));
        }

        $envName = $question->ask();
        $this->spacer();
        Manifest::addMetadata(CommonMetadata::ENV_NAME_KEY, $envName);
        return $envName;
    }

    /**
     * @return string
     * @throws CommonAentsException
     * @throws ManifestException
     */
    public function askForReverseProxy(): string
    {
        $available = CommonAents::getAentsListByDependencyKey(CommonDependencies::REVERSE_PROXY_KEY);
        $image = $this->factory->choiceQuestion('Reverse proxy', $available)
            ->setDefault($available[0])
            ->setHelpText('A reverse proxy is useful for public facing services with a domain name. It handles the incoming requests and forward them to the correct container.')
            ->ask();
        $this->spacer();

        if ($image === 'other') {
            $image = $this->factory->question('Name of your reverse proxy image')
                ->compulsory()
                ->setValidator(CommonValidators::getAlphaValidator(['_', '.', '-']))
                ->ask();
            $this->spacer();
        }

        $version = $this->askForDockerImageTag($image, $image);

        Manifest::addDependency("$image:$version", CommonDependencies::REVERSE_PROXY_KEY, [
            CommonMetadata::ENV_NAME_KEY => Manifest::mustGetMetadata(CommonMetadata::ENV_NAME_KEY),
            CommonMetadata::ENV_TYPE_KEY => Manifest::mustGetMetadata(CommonMetadata::ENV_TYPE_KEY)
        ]);

        return Manifest::mustGetDependency(CommonDependencies::REVERSE_PROXY_KEY);
    }

    /**
     * @return null|string
     * @throws CommonAentsException
     * @throws ManifestException
     */
    public function askForCI(): ?string
    {
        $envType = Manifest::mustGetMetadata(CommonMetadata::ENV_TYPE_KEY);

        if ($envType === CommonMetadata::ENV_TYPE_DEV) {
            return null;
        }

        $installCIAent = $this->factory->question('Do you use a CI/CD tool?')
            ->compulsory()
            ->yesNoQuestion()
            ->ask();
        $this->spacer();

        if (empty($installCIAent)) {
            return null;
        }

        $available = CommonAents::getAentsListByDependencyKey(CommonDependencies::CI_KEY);
        $available[] = 'other';
        $image = $this->factory->choiceQuestion('CI/CD', $available)
            ->setDefault($available[0])
            ->ask();
        $this->spacer();

        if ($image === 'other') {
            $image = $this->factory->question('Name of your CI image')
                ->compulsory()
                ->setValidator(CommonValidators::getAlphaValidator(['_', '.', '-']))
                ->ask();
            $this->spacer();
        }

        $version = $this->askForDockerImageTag($image, $image);

        Manifest::addDependency("$image:$version", CommonDependencies::CI_KEY, [
            CommonMetadata::ENV_NAME_KEY => Manifest::mustGetMetadata(CommonMetadata::ENV_NAME_KEY),
            CommonMetadata::ENV_TYPE_KEY => Manifest::mustGetMetadata(CommonMetadata::ENV_TYPE_KEY)
        ]);

        return Manifest::mustGetDependency(CommonDependencies::CI_KEY);
    }
}
