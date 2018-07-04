<?php


namespace TheAentMachine;

use Symfony\Component\Console\Output\OutputInterface;
use TheAentMachine\Exception\CannotHandleEventException;
use TheAentMachine\Service\Service;

class CommonEvents
{
    private const NEW_SERVICE = 'NEW_SERVICE';
    private const NEW_VIRTUAL_HOST = 'NEW_VIRTUAL_HOST';
    private const NEW_IMAGE = 'NEW_IMAGE';

    /** @var AentHelper */
    private $aentHelper;
    /** @var OutputInterface */
    private $output;

    /**
     * CommonEvents constructor.
     * @param AentHelper $aentHelper
     * @param OutputInterface $output
     */
    public function __construct(AentHelper $aentHelper, OutputInterface $output)
    {
        $this->aentHelper = $aentHelper;
        $this->output = $output;
    }

    /**
     * @throws CannotHandleEventException
     */
    public function dispatchService(Service $service): void
    {
        $this->canDispatchServiceOrFail();
        Hermes::dispatchJson(self::NEW_SERVICE, $service);
    }

    /**
     * @throws CannotHandleEventException
     */
    public function canDispatchServiceOrFail(): void
    {
        $canHandle = Hermes::canHandleEvent(self::NEW_SERVICE);

        if (!$canHandle) {
            $this->output->writeln('<error>Heads up!</error>');
            $this->output->writeln('It seems that Aenthill does not know how or where to store this new service. You need to install a dedicated Aent for this.');
            $this->output->writeln('Most of the time, you want to put this service in a docker-compose.yml file. We have a pretty good Aent for this: <info>theaentmachine/aent-docker-compose</info>');

            $answer = $this->aentHelper
                ->question('Do you want me to add this Aent for you?')
                ->yesNoQuestion()
                ->setDefault('y')
                ->ask();

            if ($answer) {
                Hermes::setDependencies(['theaentmachine/aent-docker-compose']);
            } else {
                throw CannotHandleEventException::cannotHandleEvent(self::NEW_SERVICE);
            }
        }
    }

    /**
     * @throws CannotHandleEventException
     * @return array[] Returns the responses
     */
    public function dispatchNewVirtualHost(string $serviceName, int $virtualPort = 80, string $virtualHost = null): ?array
    {
        $this->canDispatchVirtualHostOrFail();

        $message = [
            'service' => $serviceName,
            'virtualPort' => $virtualPort
        ];
        if ($virtualHost !== null) {
            $message['virtualHost'] = $virtualHost;
        }

        return Hermes::dispatchJson(self::NEW_VIRTUAL_HOST, $message);
    }

    /**
     * @throws CannotHandleEventException
     */
    public function canDispatchVirtualHostOrFail(): void
    {
        $canHandle = Hermes::canHandleEvent(self::NEW_VIRTUAL_HOST);

        if (!$canHandle) {
            $this->output->writeln('<error>Heads up!</error>');
            $this->output->writeln('It seems that Aenthill does not know how to bind your container to a domain name. You need to install a reverse proxy for this.');
            $this->output->writeln('Traefik is a good reverse proxy. We have an Aent to add Traefik to your project: <info>theaentmachine/aent-traefik</info>.');

            $answer = $this->aentHelper
                ->question('Do you want me to add this Aent for you?')
                ->yesNoQuestion()
                ->setDefault('y')
                ->ask();

            if ($answer) {
                Hermes::setDependencies(['theaentmachine/aent-traefik']);
            } else {
                throw CannotHandleEventException::cannotHandleEvent(self::NEW_VIRTUAL_HOST);
            }
        }
    }

    /**
     * @throws CannotHandleEventException
     */
    public function dispatchImage(Service $service): void
    {
        $this->canDispatchImageOrFail();

        Hermes::dispatchJson(self::NEW_IMAGE, $service);
    }

    /**
     * @throws CannotHandleEventException
     */
    public function canDispatchImageOrFail(): void
    {
        $canHandle = Hermes::canHandleEvent(self::NEW_IMAGE);

        if (!$canHandle) {
            $this->output->writeln('<error>Heads up!</error>');
            $this->output->writeln('It seems that Aenthill does not know how to handle the creation of a new image. You need to install a dedicated Aent for this.');
            $this->output->writeln('Most of the time, you want to put the instructions in a Dockerfile. We have a pretty good Aent for this: <info>theaentmachine/aent-dockerfile</info>.');

            $answer = $this->aentHelper
                ->question('Do you want me to add this Aent for you?')
                ->yesNoQuestion()
                ->setDefault('y')
                ->ask();

            if ($answer) {
                Hermes::setDependencies(['theaentmachine/aent-dockerfile']);
            } else {
                throw CannotHandleEventException::cannotHandleEvent(self::NEW_IMAGE);
            }
        }
    }
}
