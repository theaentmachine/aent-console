<?php


namespace TheAentMachine\Question;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question as SymfonyQuestion;
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

    /** @return string[] */
    private function getAvailableVersions(string $dockerHubImage): array
    {
        $registryClient = new RegistryClient();
        return $registryClient->getImageTagsOnDockerHub($dockerHubImage);
    }

    public function askForDockerImageTag(string $dockerHubImage, string $applicationName = ''): string
    {
        $availableVersions = $this->getAvailableVersions($dockerHubImage);

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
        $this->output->writeln('');

        return $version;
    }

    public function askForServiceName(string $serviceName, string $applicationName = ''): string
    {
        return $this->factory->question("$applicationName service name")
            ->setDefault($serviceName)
            ->compulsory()
            ->setHelpText('The "service name" is used as an identifier for the container you are creating. It is also bound in Docker internal network DNS and can be used from other containers to reference your container.')
            ->setValidator(CommonValidators::getAlphaValidator(['_', '.', '-']))
            ->ask();
    }

    /**
     * Return an array of {"ENV_NAME": "foo", "ENV_TYPE": "bar"}, chosen by the user
     * @return mixed[]
     * @throws CommonAentsException
     */
    public function askForEnvironments(): array
    {
        $environments = \array_unique(Aenthill::dispatchJson(CommonEvents::ENVIRONMENT_EVENT, []), SORT_REGULAR);

        if (empty($environments)) {
            $this->output->writeln('<error>No environments available.</error>');
            $this->output->writeln('Did you forget to install an orchestrator?');
            $this->output->writeln('<info>Available orchestrators:</info> ' . implode(', ', CommonAents::getAentsListByDependencyKey(CommonDependencies::ORCHESTRATOR_KEY)));
            exit(1);
        }

        $environmentsStr = [];
        $environmentsStr[] = 'All';
        $environmentsByStr = [];
        foreach ($environments as $env) {
            $str = $env[CommonMetadata::ENV_NAME_KEY] . ' (of type ' . $env[CommonMetadata::ENV_TYPE_KEY] . ')';
            $environmentsStr[] = $str;
            $environmentsByStr[$str] = $env;
        }

        $chosen = $this->factory->choiceQuestion('Environments', $environmentsStr, false)
            ->setHelpText('Choose your environment. You can input several environments separated by commas (,)')
            ->setDefault('All')
            ->askWithMultipleChoices();

        $this->output->writeln('<info>Environments: ' . \implode(', ', $chosen) . '</info>');
        $this->output->writeln('');

        $results = [];
        foreach ($chosen as $c) {
            if ($c === 'All') {
                $results = $environments;
                break;
            }
            $results[] = $environmentsByStr[$c];
        }

        return $results;
    }

    public function askForEnvType(): string
    {
        $envType = $this->factory->choiceQuestion('Environment type', [CommonMetadata::ENV_TYPE_DEV, CommonMetadata::ENV_TYPE_TEST, CommonMetadata::ENV_TYPE_PROD])
            ->ask();
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
        $available[] = 'Enter my own image';
        $image = $this->factory->choiceQuestion('Reverse proxy', $available)
            ->setDefault($available[0])
            ->setHelpText('A reverse proxy is useful for public facing services with a domain name. It handles the incoming requests and forward them to the correct container.')
            ->ask();

        $version = null;
        if ($image === 'Enter my own image') {
            do {
                $image = $this->factory->question('Docker image of your reverse proxy (without tag)')
                    ->compulsory()
                    ->setValidator(CommonValidators::getDockerImageWithoutTagValidator())
                    ->ask();
                try {
                    $version = $this->askForDockerImageTag($image, $image);
                } catch (ClientException $e) {
                    $this->output->writeln("<error>It seems that your image $image does not exist in the docker hub, please try again.</error>");
                    $this->output->writeln('');
                }
            } while ($version === null);
        } else {
            $availableVersions = $this->getAvailableVersions($image);
            if (count($availableVersions) === 1) {
                $version = $availableVersions[0];
            } else {
                $version = $this->askForDockerImageTag($image, $image);
            }
        }

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

        $available = CommonAents::getAentsListByDependencyKey(CommonDependencies::CI_KEY);
        $available[] = 'Enter my own image';

        $image = $this->factory->choiceQuestion('CI/CD', $available)
            ->setDefault($available[0])
            ->ask();

        $version = null;
        if ($image === 'Enter my own image') {
            do {
                $image = $this->factory->question('Docker image of your CI tool (without tag)')
                    ->compulsory()
                    ->setValidator(CommonValidators::getDockerImageWithoutTagValidator())
                    ->ask();
                try {
                    $version = $this->askForDockerImageTag($image, $image);
                } catch (ClientException $e) {
                    $this->output->writeln("<error>It seems that $image does not exist in the docker hub, please try again.</error>");
                    $this->output->writeln('');
                }
            } while ($version === null);
        } else {
            $availableVersions = $this->getAvailableVersions($image);
            if (count($availableVersions) === 1) {
                $version = $availableVersions[0];
            } else {
                $version = $this->askForDockerImageTag($image, $image);
            }
        }

        Manifest::addDependency("$image:$version", CommonDependencies::CI_KEY, [
            CommonMetadata::ENV_NAME_KEY => Manifest::mustGetMetadata(CommonMetadata::ENV_NAME_KEY),
            CommonMetadata::ENV_TYPE_KEY => Manifest::mustGetMetadata(CommonMetadata::ENV_TYPE_KEY)
        ]);

        return Manifest::mustGetDependency(CommonDependencies::CI_KEY);
    }

    /**
     * @return null|string
     * @throws CommonAentsException
     * @throws ManifestException
     */
    public function askForImageBuilder(): ?string
    {
        $envType = Manifest::mustGetMetadata(CommonMetadata::ENV_TYPE_KEY);

        if ($envType === CommonMetadata::ENV_TYPE_DEV) {
            return null;
        }

        $availableImageBuilders = CommonAents::getAentsListByDependencyKey(CommonDependencies::IMAGE_BUILDER_KEY);
        $availableImageBuilders[] = 'Enter my own image';

        $image = $this->factory->choiceQuestion('Image builder', $availableImageBuilders)
            ->setDefault($availableImageBuilders[0])
            ->setHelpText('An image builder can generate Dockerfiles, which then can be used to build images of your project.')
            ->ask();
        $version = null;

        if ($image === 'Enter my own image') {
            do {
                $image = $this->factory->question('Docker image of your image builder (without tag)')
                    ->compulsory()
                    ->setValidator(CommonValidators::getDockerImageWithoutTagValidator())
                    ->ask();
                try {
                    $version = $this->askForDockerImageTag($image, $image);
                } catch (GuzzleException $e) {
                    $this->output->writeln("<error>It seems that $image does not exist in the docker hub, please try again.</error>");
                    $this->output->writeln('');
                }
            } while ($version === null);
        } else {
            $availableVersions = $this->getAvailableVersions($image);
            if (count($availableVersions) === 1) {
                $version = $availableVersions[0];
            } else {
                $version = $this->askForDockerImageTag($image, $image);
            }
        }

        Manifest::addDependency("$image:$version", CommonDependencies::IMAGE_BUILDER_KEY, [
            CommonMetadata::ENV_NAME_KEY => Manifest::mustGetMetadata(CommonMetadata::ENV_NAME_KEY),
            CommonMetadata::ENV_TYPE_KEY => Manifest::mustGetMetadata(CommonMetadata::ENV_TYPE_KEY)
        ]);

        return Manifest::mustGetDependency(CommonDependencies::IMAGE_BUILDER_KEY);
    }
}
