<?php


namespace TheAentMachine;


use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TheAentMachine\Exception\CannotHandleEventException;
use TheAentMachine\Service\Service;

class CommonEvents
{
    private const NEW_DOCKER_SERVICE_INFO = 'NEW_DOCKER_SERVICE_INFO';

    /**
     * @throws CannotHandleEventException
     */
    public function dispatchService(Service $service, QuestionHelper $helper, OutputInterface $output): void
    {
        $this->canDispatchServiceOrFail($helper, $output);

        Hermes::dispatchJson(self::NEW_DOCKER_SERVICE_INFO, $service);
    }

    /**
     * @throws CannotHandleEventException
     */
    public function canDispatchServiceOrFail(QuestionHelper $helper, OutputInterface $output): void
    {
        $canHandle = Hercule::canHandleEvent(self::NEW_DOCKER_SERVICE_INFO);

        if (!$canHandle) {
            $output->writeln('<error>Heads up!</error>');
            $output->writeln('It seems that Aenthill does not know how or where to store this new service. You need to install a dedicated Aent for this.');
            $output->writeln('Most of the time, you want to put this service in a docker-compose.yml file. We have a pretty good Aent for this: <info>theaentmachine/aent-docker-compose</info>.');
            $question = new Question('Do you want me to add this Aent for you? (y/n) ', 'y');
            $question->setValidator(function (string $value) {
                $value = \strtolower(trim($value));

                if ($value !== 'y' && $value !== 'n') {
                    throw new \Exception('Please type "y" or "n"');
                }

                return $value;
            });
            $answer = $helper->ask($this->input, $this->output, $question);

            if ($answer === 'y') {
                Hercule::addAent('theaentmachine/aent-docker-compose');
            } else {
                throw CannotHandleEventException::cannotHandleEvent(self::NEW_DOCKER_SERVICE_INFO);
            }
        }
    }
}
